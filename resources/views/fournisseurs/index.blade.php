@extends('layouts.app')
@section('title', 'Fournisseurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Fournisseurs</h2>
        <p class="text-muted mb-0">{{ $fournisseurs->count() }} fournisseur(s)</p>
    </div>
    <button class="btn btn-sf-primary" data-bs-toggle="modal" data-bs-target="#fourModal">
        <i class="bi bi-plus-lg me-1"></i> Ajouter
    </button>
</div>

<div class="sf-panel">
    <table class="table sf-table mb-0">
        <thead><tr><th>Nom</th><th>Téléphone</th><th>Email</th><th>Adresse</th><th></th></tr></thead>
        <tbody>
            @forelse($fournisseurs as $f)
                <tr>
                    <td class="fw-semibold">{{ $f->nom }}</td>
                    <td style="font-family:monospace">{{ $f->telephone ?? '—' }}</td>
                    <td>{{ $f->email ?? '—' }}</td>
                    <td>{{ $f->adresse ?? '—' }}</td>
                    <td class="text-end">
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('fournisseurs.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce fournisseur ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Aucun fournisseur</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="fourModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('fournisseurs.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nouveau fournisseur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nom</label>
                        <input class="form-control" name="nom" required placeholder="ex: Bureau Plus Maroc"></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Téléphone</label>
                        <input class="form-control" name="telephone"></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email"></div>
                    <div class="mb-2"><label class="form-label small fw-semibold">Adresse</label>
                        <input class="form-control" name="adresse"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sf-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
