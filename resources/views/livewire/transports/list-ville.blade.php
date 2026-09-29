<div class="row">
    <div class="col-sm-8">
        <div class="card radius-15">
            <div class="card-body">
                <div class="card-title">
                    <h5 class="mb-0 my-auto">
                        Liste des  frais de transport
                    </h5>
                </div>
                <div class="table-responsive-sm">
                    <table  wire:poll.8s id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="table-dark cusor">
                            <tr>
                                
                                <th>Villes</th>

                                <th>Frais</th>
                              
                                <th>création</th>
                                <th style="text-align: right;">
                                    <span wire:loading>
                                        <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20"
                                            class="rounded shadow" alt="">
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transports as $transport)
                                <tr data-id="{{ $transport->id }}" class="cusor">
                                 
                                    <td> {{ $transport->ville }} </td>
                                    <td> {{ $transport->frais }}  <x-devise></x-devise> </td>
                                   
                                    <td> {{ $transport->created_at->format('d/m/Y') }} </td>
                                    <td class="text-end">
                                       
                                        <button class="btn btn-sm btn-danger"
                                            onclick="toggle_confirmation({{ $transport->id }})">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success d-none" type="button"
                                            id="confirmBtn{{ $transport->id }}"
                                            wire:click="delete({{ $transport->id }})">
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
                                        <div class="text-center p-3">
                                            <p>Aucun  frais de transport  trouvé</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card radius-15">
            <div class="card-body">
                <div class="card-title">
                    <h5 class="mb-0 my-auto">
                        Enregistrement
                    </h5>
                </div>
                <form wire:submit="save">
                    <div class="mb-2">
                        <label for="">Ville</label>
                        <input type="text" name="ville" wire:model="ville" class="form-control" id="">
                        @error('nom')
                            <span class="small text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label for="">frais</label>
                        <input type="number" name="frais" wire:model="frais" class="form-control" id="">
                        @error('frais')
                            <span class="small text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
             
                
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading>
                                <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                            </span>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
