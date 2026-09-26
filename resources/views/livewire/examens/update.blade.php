<div class="card shadow-sm border-0 radius-12">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0 font-weight-bold">
            <i class="bx bx-edit me-2"></i>Modifier l'Examen : {{ $nom }}
        </h6>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form wire:submit.prevent="update">
            {{-- Nom du groupe d'examen --}}
            <div class="mb-3">
                <label class="form-label font-weight-bold">Nom du groupe d'examen <span class="text-danger">*</span></label>
                <input type="text" wire:model="nom" class="form-control @error('nom') is-invalid @enderror" placeholder="ex: Hémogramme (NFS), Bilan Lipidique...">
                @error('nom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Sous-analyses & Tarifs dynamiques --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label font-weight-bold mb-0">Analyses associées & Tarifs</label>
                    <button type="button" wire:click="addCaracteristique" class="btn btn-sm btn-outline-primary radius-30">
                        <i class="bx bx-plus me-1"></i> Ajouter une analyse
                    </button>
                </div>

                @foreach($caracteristiques as $index => $item)
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-7">
                            <input type="text" 
                                   wire:model="caracteristiques.{{ $index }}.nom" 
                                   class="form-control @error("caracteristiques.{$index}.nom") is-invalid @enderror" 
                                   placeholder="Nom de l'analyse (ex: Taux d'Hémoglobine)">
                            @error("caracteristiques.{$index}.nom")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="number" 
                                       wire:model="caracteristiques.{{ $index }}.prix" 
                                       class="form-control @error("caracteristiques.{$index}.prix") is-invalid @enderror" 
                                       placeholder="Tarif">
                                <span class="input-group-text bg-light">FCFA</span>
                            </div>
                            @error("caracteristiques.{$index}.prix")
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-1 text-center">
                            @if(count($caracteristiques) > 1)
                                <button type="button" wire:click="removeCaracteristique({{ $index }})" class="btn btn-outline-danger btn-sm radius-8">
                                    <i class="bx bx-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between border-top pt-3">
                <a href="{{ route('examens') }}" class="btn btn-secondary px-4 radius-30">
                    <i class="bx bx-arrow-back me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary px-4 radius-30">
                    <i class="bx bx-save me-1"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>