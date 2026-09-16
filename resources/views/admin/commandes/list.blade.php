@section('titre', 'Liste des commandes')
@extends('admin.fixe')

@section('body')


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
                                    <a href="{{ route('commandes') }}">Commandes</a>
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

                    <div class="card-title">
                        <div class="d-flex justify-content-between">
                            <h5 class="header-title">
                                Liste des commandes
                            </h5>
                            <div>
                                @can('order_add')
                                    <button class="btn btn-sm btn-primary" onclick="url('{{ route('new_commande') }}')">
                                        <i class="ri-folder-add-fill"></i> Ajouter une commande
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <hr>
                    @livewire('Commandes.ListCommande')
                </div>
            </div>


        </div>
    </div>


    





    <!-- Center modal content -->
    <div class="modal fade" id="modal-note" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myCenterModalLabel">
                        Ajouter une note a la commande de <span id="nom-client">[ nom client]</span>.
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('add_note') }}" method="post">
                    <div class="modal-body">

                        @csrf
                        <label for="">
                            Description
                        </label>
                        <input type="hidden" id="id_commande" name="id_commande" required>
                        <textarea name="note" class="form-control" rows="10" required
                            placeholder="Veuillez entrer le maximun d'information "></textarea>

                        <br>
                        <div class="alert alert-warning">
                            <b><i class="ri-information-line"></i> Attention.</b>
                            <p class="small">
                                En ajoutant une note a cette commande , elle ecrasera la note precedente si elle existe .
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="ri-save-line"></i>
                            Enregistrer la note
                        </button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <script>
        function add_note(id_commande, nom) {
            //open modal an set id commande in id_mcommande input in modal
            $("#id_commande").val(id_commande);
            $('#nom-client').html(" " + nom);
            $('#modal-note').modal('show');
        }
    </script>

@endsection
