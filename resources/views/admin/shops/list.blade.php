@section('titre', 'Liste des boutiques')
@extends('admin.fixe')

@section('body')
<!--page-content-wrapper-->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- start page title -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">{{ config('app.name') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('shops') }}">Boutiques</a>
                            </li>
                            <li class="breadcrumb-item active">Liste</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card radius-15">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-title">
                        <h5 class="mb-0 my-auto">Liste des boutiques</h5>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#import">
                            <i class="ri-file-excel-line"></i> Importer fichier Excel
                        </button>

                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add">
                            <i class="ri-user-add-line"></i> Ajouter une boutique
                        </button>
                    </div>
                </div>
                <hr />
                @include('components.alert')
                @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Opération réussie',
                        text: '{{ session('
                        success ') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                </script>
                @endif
                <div class="table-responsive-sm">
                    <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="table-dark cursor-pointer">
                            <tr>
                                <th>Photo</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Rôle</th>

                                <th style="text-align: right;">
                                    <span wire:loading>
                                        <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20" class="rounded shadow" alt="Chargement...">
                                    </span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($shops as $shop)
                            <tr>
                                <td>
                                    <img src="{{ $shop->avatar() }}" width="40" height="40" class="rounded" alt="Avatar">
                                </td>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->email }}</td>
                                <td>{{ $shop->phone }}</td>
                                <td>{{ $shop->role }}</td>

                                <td style="text-align: right;">
                                    <div class="btn-group">
                                        <!-- Bouton Activer / Désactiver -->
                                         @can('table_delete')
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="deleteData('{{ route('shop.delete', $shop->id) }}')">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
                                        @endcan
                                        <!-- Bouton Modifier -->
                                         @can('table_edit')
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editShop{{ $shop->id }}">
                                            <i class="ri-edit-2-line"></i>
                                        </button>
                                        @endcan
                                      <!--   <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#roleModal{{ $shop->id }}">
                                            Modifier rôle
                                        </button> -->
                                           @can('table_edit')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#shop-{{ $shop->id }}">
                                            <i class="ri-settings-5-line"></i> Permissions
                                        </button>
                                        @endcan
                                       
                                    </div>

                                    <button class="btn btn-sm btn-success d-none" type="button" id="confirmBtn{{ $shop->id }}" onclick="url('/admin/shop/delete/{{ $shop->id }}')">
                                        <i class="bi bi-check-circle"></i>
                                        <span class="hide-tablete">Confirmer</span>
                                    </button>

                                    @include('admin.shops.modal-permissions', ['shop' => $shop])
                                 <!--    @include('admin.shops.modal-roles', ['shop' => $shop]) -->
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune boutique trouvée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Modal Modification Boutique -->
 @foreach($shops as $shop)
<div class="modal fade" id="editShop{{ $shop->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Modifier : {{ $shop->name }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('shop.update', $shop->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nom de la boutique</label>
                        <input type="text" name="name" class="form-control" value="{{ $shop->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $shop->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $shop->phone }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
<!-- Modal Ajouter Boutique -->
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myCenterModalLabel">Ajouter une boutique.</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @livewire('AddShops')
        </div>
    </div>
</div>

<!-- Modal Importer Excel -->
<div class="modal fade" id="import" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myCenterModalLabel">Importer fichier Excel.</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="import_file" class="form-control" required />
                        <button type="submit" class="btn btn-primary">Importer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteData(url) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible pour SWOOT BIO !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Utilise l'URL passée directement en paramètre
                window.location.href = url;
            }
        })
    }
</script>
@endsection