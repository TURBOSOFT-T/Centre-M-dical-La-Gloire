<div class="table-responsive-sm">
    @include('components.alert')
    <table class="table table-striped dt-responsive nowrap w-100">
        <thead class="table-dark cusor">
            <tr>
               
                <th>Nom</th>
              
                <th>Création</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($examens as $examen)
                <tr>
                   
                    <td>
                        {{ $examen->nom }}
                    </td>
                    
                    <td>
                        {{ $examen->created_at }}
                    </td>
                    <td class="text-end">
                       
                        <button class="btn btn-sm btn-dark" data-bs-toggle="modal"
                            data-bs-target="#examen-{{ $examen->id }}">
                            <i class="ri-edit-box-line"></i> Modifier
                        </button>
                      
                      
                        <button class="btn btn-sm btn-danger" onclick="toggle_confirmation({{ $examen->id }})">
                             <i class="bx bx-trash"></i>
                        </button>
                        
                        <button class="btn btn-sm btn-success d-none" type="button" id="confirmBtn{{ $examen->id }}"
                            wire:click="delete({{ $examen->id }})">
                            <i class="bi bi-check-circle"></i>
                            <span class="hide-tablete">
                                Confirmer
                            </span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="text-center p-5">
                            <img width="80" height="80"
                                src="https://img.icons8.com/external-outline-geotatah/80/1fb141/external-brand-a-commerce-automated-commerce-outline-geotatah.png"
                                alt="external-brand-a-commerce-automated-commerce-outline-geotatah" />
                            <br>
                            <h6 class="text-muted">Aucun laboratoire n'a été trouvé</h6>
                
                    

                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @foreach ($examens as $examen)
        <!-- Center modal content -->
        <div class="modal fade" id="examen-{{ $examen->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="myCenterModalLabel">
                            {{ $examen->nom }}
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            @livewire('Examens.Update',['examen'=>$examen])
                        </p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endforeach
</div>
