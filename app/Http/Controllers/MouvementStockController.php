<?php

namespace App\Http\Controllers;

use App\Mail\AlerteRuptureCritique;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MouvementStockController extends Controller
{
    public function index(Request $request)
    {
        $query = MouvementStock::with(['produit', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('produit_id')) {
            $query->where('produit_id', $request->produit_id);
        }

        $mouvements = $query->latest('date_mouvement')->latest('id')->paginate(15)->withQueryString();
        $produits = Produit::orderBy('nom')->get();

        return view('mouvements.index', compact('mouvements', 'produits'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'motif' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $produit = Produit::lockForUpdate()->findOrFail($data['produit_id']);

                if ($data['type'] === 'sortie' && $produit->quantite < $data['quantite']) {
                    throw new \Exception("Stock insuffisant : seulement {$produit->quantite} disponible(s).");
                }

                MouvementStock::create([
                    'produit_id' => $produit->id,
                    'user_id' => $request->user()->id,
                    'type' => $data['type'],
                    'quantite' => $data['quantite'],
                    'motif' => $data['motif'] ?? null,
                    'date_mouvement' => now()->toDateString(),
                ]);

                if ($data['type'] === 'entree') {
                    $produit->increment('quantite', $data['quantite']);
                } else {
                    $produit->decrement('quantite', $data['quantite']);
                    $this->notifierSiRuptureCritique($produit);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Mouvement enregistré avec succès.');
    }

    public function destroy(MouvementStock $mouvement)
    {
        DB::transaction(function () use ($mouvement) {
            $produit = Produit::lockForUpdate()->findOrFail($mouvement->produit_id);

            if ($mouvement->type === 'entree') {
                $produit->decrement('quantite', $mouvement->quantite);
            } else {
                $produit->increment('quantite', $mouvement->quantite);
            }

            $mouvement->delete();
        });

        return back()->with('success', 'Mouvement annulé, stock restauré.');
    }

    /**
     * Ajustement manuel du stock (inventaire physique, correction d'erreur...).
     * Contrairement à entrée/sortie, on saisit ici directement la NOUVELLE quantité réelle,
     * et le système calcule l'écart tout seul — réservé aux admins (voir routes/web.php).
     */
    public function ajuster(Request $request)
    {
        $data = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'nouvelle_quantite' => 'required|integer|min:0',
            'motif' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data, $request) {
            $produit = Produit::lockForUpdate()->findOrFail($data['produit_id']);
            $ancienneQuantite = $produit->quantite;
            $nouvelleQuantite = $data['nouvelle_quantite'];
            $ecart = abs($nouvelleQuantite - $ancienneQuantite);

            MouvementStock::create([
                'produit_id' => $produit->id,
                'user_id' => $request->user()->id,
                'type' => 'ajustement',
                'quantite' => $ecart,
                'ancienne_quantite' => $ancienneQuantite,
                'nouvelle_quantite' => $nouvelleQuantite,
                'motif' => $data['motif'],
                'date_mouvement' => now()->toDateString(),
            ]);

            $produit->update(['quantite' => $nouvelleQuantite]);
            $this->notifierSiRuptureCritique($produit);
        });

        return back()->with('success', 'Stock ajusté avec succès.');
    }

    /**
     * Envoie un email aux administrateurs si un article marqué "critique" vient
     * d'atteindre un stock de 0. N'interrompt jamais le mouvement en cas d'échec d'envoi
     * (ex: serveur SMTP non configuré) — l'erreur est seulement journalisée.
     */
    private function notifierSiRuptureCritique(Produit $produit): void
    {
        if ($produit->criticite !== 'critique' || $produit->quantite > 0) {
            return;
        }

        $destinataires = User::where('role', 'admin')->pluck('email');

        if ($destinataires->isEmpty()) {
            return;
        }

        try {
            Mail::to($destinataires)->send(new AlerteRuptureCritique($produit->fresh(['category', 'fournisseur'])));
        } catch (\Throwable $e) {
            Log::warning('Échec envoi email rupture critique (produit #' . $produit->id . ') : ' . $e->getMessage());
        }
    }

    /**
     * Génère un bon de mouvement en PDF (traçabilité physique de la sortie/entrée).
     */
    public function exportPdf(MouvementStock $mouvement)
    {
        $mouvement->load(['produit', 'user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mouvements.pdf-bon', compact('mouvement'));

        return $pdf->stream('bon-mouvement-' . $mouvement->id . '.pdf');
    }

    /**
     * Exporte l'historique des mouvements en CSV (compatible Excel).
     */
    public function exportCsv()
    {
        $mouvements = MouvementStock::with(['produit', 'user'])
            ->latest('date_mouvement')
            ->get();

        // Construction en mémoire (voir ProduitController::exportCsv pour le pourquoi).
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Date', 'Article', 'Référence', 'Type', 'Quantité', 'Motif', 'Agent']);

        foreach ($mouvements as $m) {
            fputcsv($handle, [
                $m->date_mouvement,
                $m->produit->nom,
                $m->produit->reference,
                $m->type === 'entree' ? 'Entrée' : 'Sortie',
                $m->quantite,
                $m->motif ?? '',
                $m->user->name ?? '',
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $content = "\xEF\xBB\xBF" . "sep=,\r\n" . $csv;

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mouvements_stock_' . now()->format('Y-m-d') . '.csv"',
            'Content-Length' => strlen($content),
        ]);
    }



        public function exportXlsx()
    {
        $mouvements = MouvementStock::with(['produit', 'user'])
            ->latest('date_mouvement')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Mouvements');

        $headers = [
            'Date',
            'Article',
            'Référence',
            'Type',
            'Quantité',
            'Motif',
            'Agent'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $sheet->getStyle('A1:G1')
            ->getFont()
            ->setBold(true);

        $row = 2;

        foreach ($mouvements as $m) {

            $type = match ($m->type) {
                'entree' => 'Entrée',
                'sortie' => 'Sortie',
                default => 'Ajustement'
            };

            $sheet->fromArray([
                $m->date_mouvement,
                $m->produit->nom ?? '',
                $m->produit->reference ?? '',
                $type,
                $m->quantite,
                $m->motif ?? '',
                $m->user->name ?? '',
            ], null, "A{$row}");

            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'mouvements_stock_' . now()->format('Y-m-d') . '.xlsx';

        $tempPath = storage_path('app/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download(
            $tempPath,
            $filename
        )->deleteFileAfterSend(true);
    }
}
