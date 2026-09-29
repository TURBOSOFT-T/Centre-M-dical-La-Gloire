<div class="modal fade" id="editModal{{ $personnel->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-start">
            <div class="modal-header">
                <h6 class="modal-title">Modifier le personnel : {{ $personnel->nom }} {{ $personnel->prenom }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Correction ici : 'update-personnels' avec un 's' à la fin --}}
            @livewire('update-personnels', ['personnelId' => $personnel->id], key('edit-'.$personnel->id))
        </div>
    </div>
</div>