@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Utilisateurs</h2>
        <p class="text-muted mb-0">{{ $users->count() }} compte(s) — réservé aux administrateurs</p>
    </div>
    <button class="btn btn-sf-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="bi bi-plus-lg me-1"></i> Ajouter un utilisateur
    </button>
</div>

<div class="sf-panel">
    <table class="table sf-table mb-0">
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th></th></tr></thead>
        <tbody>
            @foreach($users as $u)
                <tr>
                    <td class="fw-semibold">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <span class="sf-badge {{ $u->role === 'admin' ? 'sf-badge-sky' : 'sf-badge-teal' }}">
                            {{ $u->role === 'admin' ? 'Administrateur' : 'Magasinier' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-sf-outline me-1" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($u->id !== auth()->id())
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('users.update', $u) }}">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title">Modifier l'utilisateur</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label small fw-semibold">Nom</label>
                                        <input class="form-control" name="name" value="{{ $u->name }}" required></div>
                                    <div class="mb-3"><label class="form-label small fw-semibold">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ $u->email }}" required></div>
                                    <div class="mb-2"><label class="form-label small fw-semibold">Rôle</label>
                                        <select class="form-select" name="role">
                                            <option value="magasinier" @selected($u->role=='magasinier')>Magasinier</option>
                                            <option value="admin" @selected($u->role=='admin')>Administrateur</option>
                                        </select></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-sf-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal création -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nouvel utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nom</label>
                        <input class="form-control" name="name" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Mot de passe</label>
                        <input type="password" class="form-control" name="password" required minlength="8"></div>
                    <div class="mb-2"><label class="form-label small fw-semibold">Rôle</label>
                        <select class="form-select" name="role">
                            <option value="magasinier">Magasinier</option>
                            <option value="admin">Administrateur</option>
                        </select></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sf-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
