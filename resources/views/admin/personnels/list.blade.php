@section('titre', 'Liste des personnels')
@extends('admin.fixe')

@section('body')
<!--page-content-wrapper-->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- Start Page Title -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">{{ config('app.name') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('personnels') }}">personnels</a>
                            </li>
                            <li class="breadcrumb-item active">Liste</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <div class="card radius-15">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Liste des personnels</h5>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#import">
                            <i class="ri-file-excel-2-line me-1"></i> Importer fichier Excel
                        </button>

                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add">
                            <i class="ri-user-add-line me-1"></i> Ajouter un personnel
                        </button>
                    </div>
                </div>

                <hr />
                @include('components.alert')

                <div class="table-responsive">
                    <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Boutique(s)</th>
                                <th>Rôle(s)</th>
                                <th class="text-end">
                                    <span wire:loading>
                                        <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20" class="rounded shadow" alt="Chargement...">
                                    </span>
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($personnels as $personnel)
                            <tr>
                                <td>{{ $personnel->nom }}</td>
                                <td>{{ $personnel->prenom }}</td>
                                <td>{{ $personnel->email }}</td>
                                <td>{{ $personnel->phone }}</td>

                                <!-- Affichage des Boutiques -->
                                <td>
                                    @forelse($personnel->shops as $shop)
                                    <span class="badge bg-primary px-2 py-1 me-1 mb-1">
                                        <i class="ri-store-2-line"></i> {{ $shop->name }}
                                    </span>
                                    @empty
                                    <span class="badge bg-secondary px-2 py-1">Aucun shop</span>
                                    @endforelse
                                </td>

                                <!-- Affichage des Rôles associés aux Boutiques -->
                                <td>
                                    @forelse($personnel->shops as $shop)
                                    @php
                                    $roleInShop = $shop->pivot->role_in_shop ?? $personnel->role;
                                    @endphp
                                    @if($roleInShop)
                                    <span class="badge bg-info text-dark px-2 py-1 me-1 mb-1">
                                        {{ ucfirst($roleInShop) }}
                                    </span>
                                    @endif
                                    @empty
                                    @if($personnel->role)
                                    <span class="badge bg-info text-dark px-2 py-1">
                                        {{ ucfirst($personnel->role) }}
                                    </span>
                                    @else
                                    <span class="badge bg-secondary px-2 py-1">Aucun</span>
                                    @endif
                                    @endforelse
                                </td>

                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $personnel->id }}" title="Modifier">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#roleModal{{ $personnel->id }}">
                                            Modifier Rôle
                                        </button>

                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#personnel-{{ $personnel->id }}">
                                            <i class="ri-settings-5-line"></i> Permissions
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm" onclick="toggle_confirmation({{ $personnel->id }})">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
                                    </div>

                                    <button class="btn btn-sm btn-success d-none mt-1" type="button" id="confirmBtn{{ $personnel->id }}" onclick="window.location.href='/admin/personnel/delete/{{ $personnel->id }}'">
                                        <i class="bi bi-check-circle"></i>
                                        <span class="hide-tablete">Confirmer</span>
                                    </button>
                                    @include('admin.personnels.modal-edit', ['personnel' => $personnel])
                                    @include('admin.personnels.modal-permissions', ['personnel' => $personnel])
                                    @include('admin.personnels.modal-roles')
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Aucun personnel trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @role('admin')
                <div class="text-end p-2">
                    <a href="{{ route('corbeilles') }}" class="text-danger">
                        <i class="ri-delete-bin-line"></i> Corbeille ( {{ $total_supprimers }} )
                    </a>
                </div>
                @endrole
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter -->
<div class="modal fade" id="add" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Ajouter un personnel</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @livewire('AddPersonnels')
        </div>
    </div>
</div>

<!-- Modal Importer -->
<div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Importer un fichier Excel</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Sélectionner un fichier (.xlsx, .xls, .csv)</label>
                        <input type="file" name="import_file" id="import_file" class="form-control" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary">Importer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection