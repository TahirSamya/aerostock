@extends('layouts.app')
@section('title', 'Catégories')

@section('content')
<div class="sf-page-header">
    <div>
        <h2 class="fw-bold mb-1">Catégories</h2>
        <p class="text-muted mb-0">{{ $categories->count() }} catégorie(s)</p>
    </div>
    <button class="btn btn-sf-primary" data-bs-toggle="modal" data-bs-target="#catModal">
        <i class="bi bi-plus-lg me-1"></i> Ajouter
    </button>
</div>

<div class="sf-panel">
    <table class="table sf-table mb-0">
        <thead><tr><th>Nom</th><th>Code</th><th>Description</th><th>Articles liés</th><th></th></tr></thead>
        <tbody>
            @forelse($categories as $c)
                <tr>
                    <td class="fw-semibold">{{ $c->nom }}</td>
                    <td><span class="sf-badge sf-badge-sky" style="font-family:monospace">{{ $c->code }}</span></td>
                    <td class="text-muted">{{ $c->description ?? '—' }}</td>
                    <td>{{ $c->produits_count }}</td>
                    <td class="text-end">
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('categories.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="sf-empty"><i class="bi bi-tags"></i><span>Aucune catégorie</span></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="catModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nouvelle catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nom</label>
                        <input class="form-control" name="nom" required placeholder="ex: Fournitures de bureau"></div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Code (préfixe de référence)</label>
                        <input class="form-control" name="code" required maxlength="10" style="text-transform:uppercase" placeholder="ex: BUR">
                    </div>
                    <div class="mb-2"><label class="form-label small fw-semibold">Description</label>
                        <input class="form-control" name="description"></div>
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
