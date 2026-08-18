<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'nom', 'reference', 'category_id', 'fournisseur_id',
        'quantite', 'seuil_alerte', 'quantite_max', 'prix_achat',
        'emplacement', 'criticite',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function emplacements()
    {
        return $this->hasMany(StockEmplacement::class);
    }

    public function historiquePrix()
    {
        return $this->hasMany(PrixAchatHistorique::class)->orderByDesc('date_changement');
    }

    /**
     * Quantité déjà répartie dans un emplacement précis (somme de stock_emplacements).
     */
    public function quantiteAffectee(): int
    {
        return $this->emplacements->sum('quantite');
    }

    /**
     * Quantité qui n'est pas encore répartie dans un emplacement précis.
     * Ne doit jamais être négatif côté métier — si ça arrive, la répartition dépasse le stock réel.
     */
    public function quantiteNonAffectee(): int
    {
        return max($this->quantite - $this->quantiteAffectee(), 0);
    }

    public function enAlerte(): bool
    {
        return $this->quantite <= $this->seuil_alerte;
    }

    /**
     * Capacité de référence utilisée pour la jauge (100% = ce niveau).
     * Si aucune capacité n'a été définie, on retombe sur une estimation
     * basée sur le seuil d'alerte, pour ne jamais afficher une jauge fausse.
     */
    public function capaciteReference(): int
    {
        return $this->quantite_max ?: max($this->seuil_alerte * 4, $this->quantite, 1);
    }

    /**
     * Taux de remplissage réel (0 à 1) par rapport à la capacité de référence.
     */
    public function tauxRemplissage(): float
    {
        return min($this->quantite / $this->capaciteReference(), 1);
    }

    /**
     * Niveau d'urgence de l'alerte stock, pour un affichage plus fin que "en alerte / pas en alerte".
     * rupture   : stock à 0
     * critique  : à la moitié du seuil ou moins (ou article marqué "critique" en alerte)
     * bas       : sous le seuil, sans être encore critique
     */
    public function niveauUrgence(): string
    {
        if ($this->quantite <= 0) {
            return 'rupture';
        }
        if (! $this->enAlerte()) {
            return 'ok';
        }
        if ($this->criticite === 'critique' || $this->quantite <= $this->seuil_alerte / 2) {
            return 'critique';
        }
        return 'bas';
    }



  public function criticiteAutomatique(): string
{
    return $this->quantite < $this->seuil_alerte
        ? 'critique'
        : 'normal';
}
}
