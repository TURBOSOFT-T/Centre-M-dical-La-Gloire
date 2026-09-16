<div>
    <form wire:submit="create">
        <div class="modal-body">
            @include('components.alert')
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nom *
                        </label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name')
                            <span class="text-danger small"> {{ $message }} </span>
                        @enderror
                    </div>
                </div>
             
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Adresse E-mail *
                        </label>
                        <input type="text" class="form-control" wire:model="email">
                        @error('email')
                            <span class="text-danger small"> {{ $message }} </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Numéro de téléphone
                        </label>
                        <input type="tel" class="form-control" wire:model="phone">
                        @error('phone')
                            <span class="text-danger small"> {{ $message }} </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Mot de passe *
                        </label>
                        <div class="form-control d-flex justify-content-between">
                            <div>
                                <i class="ri-lock-line"></i> [ Par défaut : ]
                                <b> <span id="spanContent"> 123456789 </span> </b>
                                <input type="hidden" id="inputContent" value="123456789">
                            </div>
                            <div>
                                <button class="btn btn-sm" type="button" id="btnCopy">
                                    <i class="ri-file-copy-2-line"></i> copier
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary">
                <span wire:loading>
                    <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                </span>
                Enregistrer
            </button>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("btnCopy").addEventListener("click", function() {
                var input = document.getElementById("inputContent");
                input.select();
                document.execCommand("copy");
                alert("Le contenu a été copié dans le presse-papiers ");
            });
        });
    </script>

</div>
