<div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            Date 
            <input type="date" aria-label="First name" class="form-control" wire:model.live="debut">
        </div>
        <div class="my-auto">
            {{-- <button class="btn btn-sm btn-primary" >
                <i class="ri-file-excel-2-line"></i>
                Exporter en Excel
            </button> --}}
        </div>
    </div>



    <div class="table-responsive-sm">


        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
            <thead class="table-dark cusor">
                <tr>
                    <th>
                        <span wire:loading>
                            <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                        </span>
                        Type de mouvement
                    </th>
                    <th>Nom</th>
                    <th>Quantite</th>
                    <th>Boutique</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historiques as $historique)
                    <tr>
                        <td>
                            <span class="text-success">
                                <i class="ri-download-2-line"></i>
                                {{ $historique->type }}
                            </span>

                        </td>
                        <td> {{ $historique->produit->nom }} </td>
                        <td> {{ $historique->quantite }} Unité(s) </td>
                        <td>{{ $historique->shop ? $historique->shop->name : 'Boutique supprimée' }}</td>
                        <td> {{ $historique->created_at }} </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            <div>
                                <img src="/icons/icons8-ticket-100.png" height="100" width="100" alt=""
                                    srcset="">
                            </div>
                            Aucun historique trouvé 
                            @if ($debut)
                                pour la date {{ $debut}}.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $historiques->links('pagination::bootstrap-4') }}
        </div>
    </div> <!-- end card body-->

</div>
