<div class="table-responsive-sm">
    @include('components.alert')
    <table class="table table-striped dt-responsive nowrap w-100">
        <thead class="table-dark cusor">
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Articles</th>
                <th>Création</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($marques as $marque)
                <tr>
                    <td>
                        <img src="{{ Storage::url($marque->image) }}" width="50" height="50" class="rounded shadow"
                            alt="">
                    </td>
                    <td>
                        {{ $marque->nom }}
                    </td>
                    <td>
                        {{ $marque->produits->count() }}
                    </td>
                    <td>
                        {{ $marque->created_at }}
                    </td>
                    <td class="text-end">
                        @can('marque_edit')
                        <button class="btn btn-sm btn-dark" data-bs-toggle="modal"
                            data-bs-target="#marque-{{ $marque->id }}">
                            <i class="ri-edit-box-line"></i> Modifier
                        </button>
                        @endcan
                        @can('marque_delete')
                        <button class="btn btn-sm btn-danger" onclick="toggle_confirmation({{ $marque->id }})">
                            <i class="ri-delete-bin-6-line"></i>
                        </button>
                        @endcan
                        <button class="btn btn-sm btn-success d-none" type="button" id="confirmBtn{{ $marque->id }}"
                            wire:click="delete({{ $marque->id }})">
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
                            <h6 class="text-muted">Aucune marque trouvée</h6>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @foreach ($marques as $marque)
        <!-- Center modal content -->
        <div class="modal fade" id="marque-{{ $marque->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="myCenterModalLabel">
                            {{ $marque->nom }}
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            @livewire('Marques.Update',['marque'=>$marque])
                        </p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endforeach
</div>
