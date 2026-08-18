@extends('layouts.app')
@section('title', 'Fournisseurs')

@section('content')

<div class="sf-page-header">
    <div>
        <h2 class="fw-bold mb-1">Fournisseurs</h2>
        <p class="text-muted mb-0">
            {{ $fournisseurs->total() }} fournisseur(s)
        </p>
    </div>

    <button class="btn btn-sf-primary"
            data-bs-toggle="modal"
            data-bs-target="#fourModal">
        <i class="bi bi-plus-lg me-1"></i>
        Ajouter
    </button>
</div>

<form method="GET" class="d-flex gap-2 mb-3">

    <input type="text"
           name="search"
           value="{{ request('search') }}"
           class="form-control"
           style="max-width:400px"
           placeholder="Rechercher un fournisseur...">

    <button type="submit"
            class="btn btn-primary">
        <i class="bi bi-search"></i>
    </button>

    @if(request('search'))
        <a href="{{ route('fournisseurs.index') }}"
           class="btn btn-outline-secondary">
            Réinitialiser
        </a>
    @endif

</form>

<div class="sf-panel">

    <table class="table sf-table mb-0">

        <thead>
        <tr>
            <th>Nom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Adresse</th>
            <th></th>
        </tr>
        </thead>

        <tbody>

        @forelse($fournisseurs as $f)

            <tr>

                <td class="fw-semibold">
                    {{ $f->nom }}
                </td>

                <td style="font-family:monospace">
                    {{ $f->telephone ?? '—' }}
                </td>

                <td>
                    {{ $f->email ?? '—' }}
                </td>

                <td>
                    {{ $f->adresse ?? '—' }}
                </td>

                <td class="text-end">

                    @if(auth()->user()->isAdmin())

                        <form action="{{ route('fournisseurs.destroy', $f) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer ce fournisseur ?')">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>

                        </form>

                    @endif

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="5">

                    <div class="sf-empty">
                        <i class="bi bi-truck"></i>
                        <span>Aucun fournisseur trouvé</span>
                    </div>

                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-3">
    {{ $fournisseurs->withQueryString()->links() }}
</div>

<div class="modal fade" id="fourModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST"
                  action="{{ route('fournisseurs.store') }}">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        Nouveau fournisseur
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Nom
                        </label>

                        <input class="form-control"
                               name="nom"
                               required
                               placeholder="Ex : Bureau Plus Maroc">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Téléphone
                        </label>

                        <input class="form-control"
                               name="telephone">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Email
                        </label>

                        <input type="email"
                               class="form-control"
                               name="email">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">
                            Adresse
                        </label>

                        <input class="form-control"
                               name="adresse">
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-sf-outline"
                            data-bs-dismiss="modal">
                        Annuler
                    </button>

                    <button type="submit"
                            class="btn btn-sf-primary">
                        Enregistrer
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection