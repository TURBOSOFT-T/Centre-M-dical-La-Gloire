<div>
    @include('components.alert')
<div class="card border-0 shadow-sm p-4 radius-15 mb-4">
    <h5 class="text-primary font-weight-bold mb-3"><i class="bx bx-data me-2"></i>Sauvegarde & Restauration de la Base de Données</h5>
    
    <div class="d-flex flex-wrap align-items-center gap-3">
        <!-- Bouton d'exportation globale -->
        <a href="{{ route('database.export.sql') }}" class="btn btn-success radius-30">
            <i class="bx bx-download me-1"></i> Télécharger la sauvegarde complète (.sql)
        </a>

        <!-- Formulaire d'importation globale -->
        <form action="{{ route('database.import.sql') }}" method="POST" enctype="multipart/form-data" class="d-inline-flex align-items-center gap-2">
            @csrf
            <div class="input-group">
                <input type="file" name="sql_file" class="form-control" accept=".sql" required>
                <button type="submit" class="btn btn-danger" onclick="return confirm('ATTENTION CRITIQUE : Restaurer une base de données va remplacer l\'ensemble des données actuelles par celles du fichier. Êtes-vous absolument sûr de vouloir continuer ?')">
                    <i class="bx bx-upload me-1"></i> Restaurer la base
                </button>
            </div>
        </form>
    </div>

   
</div>

<div class="mt-3">
    <!-- Bouton pour recevoir la base de données par e-mail -->
    <a href="{{ route('database.email.sql') }}" class="btn btn-info text-white radius-30">
        <i class="bx bx-envelope me-1"></i> Envoyer la sauvegarde par E-mail
    </a>
</div>
    <form wire:submit="update_form">

        <hr class="my-6 mx-n4" />
        <div class="text-center bg-primary card my-auto p-1 mb-3">
            <h6 class="text-white">
                Logos, et Adresses
            </h6>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Logo(157*40) </label>
                    <input type="file" wire:model="logo" accept="image/*" class="form-control">
                    @error('logo')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Icone(157*40)</label>
                    <input type="file" wire:model="icon" accept="image/*" class="form-control">
                    @error('icon')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Email</label>
                    <input type="mail" wire:model="email" step="0.1" class="form-control">
                    @error('email')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Telephone</label>
                    <input type="number" wire:model="telephone" step="0.1" class="form-control">
                    @error('telephone')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Addresse</label>
                    <input type="text" wire:model="addresse" step="0.1" class="form-control">
                    @error('addresse')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label><strong>Description :</strong></label>
                <textarea class="ckeditor form-control" name="description" wire:model="description"></textarea>
                @error('description')
                <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>



            <br>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="submit">
                    <span wire:loading>
                        <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                    </span>
                    <i class="ri-save-line me-1 fs-16 lh-1"></i>
                    Enregistrer les changements
                </button>
            </div>
    </form>

</div>