<!-- Center modal content -->
<div class="modal fade" id="examen-{{ $examen->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="myCenterModalLabel">
                    <i class='bx bxs-message-rounded-dots' ></i>{{ $examen->nom }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>
                    @livewire('Examens.Add',['examen'=>$examen])
                </p>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
