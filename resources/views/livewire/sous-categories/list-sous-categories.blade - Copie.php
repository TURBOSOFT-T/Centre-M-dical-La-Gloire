<div>

    
    @include('components.alert')

    <div class="table-responsive-sm">
        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
            <thead class="table-dark cusor">
                <tr>
                   
                  
                    <th>Nom  Sous categorie</th>
                     <th>Nombre produit</th>
                  
                   
                    <th> Date création</th>
                    <th style="text-align: right;">
                        <span wire:loading>
                            <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20" class="rounded shadow"
                                alt="">
                        </span>
                    </th>
                </tr>
            </thead>


            <tbody>
                @forelse ($categories as $cat)
                    <tr>
                     
                        <td>
                            

                            {{ $cat->nom }}
                        </td>
                      {{--   <td>{{$cat->produits->count()}}</td>  --}}
                         <td>{{ $cat->productCount()}}</td>  

                       
                        
                       
                       
                        <td>{{ $cat->created_at->format('d/m/Y') }} </td>
                        <td style="text-align: right;">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-dark"
                                    onclick="url(' {{ route('sous_categories.update', ['id' => $cat->id]) }} ')">
                                    <i class="ri-edit-box-line"></i>
                                </button>
                               
                              
                                @can('category_delete')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="toggle_confirmation({{ $cat->id }})">
                                        <i class="ri-delete-bin-6-line"></i>
                                    </button>
                                @endcan
                            </div>

                          
                            <button class="btn btn-sm btn-success d-none" type="button"
                                id="confirmBtn{{ $cat->id }}" wire:click="delete({{ $cat->id }})">
                                <i class="bi bi-check-circle"></i>
                                <span class="hide-tablete">
                                    Confirmer
                                </span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <div>
                                <img src="/icons/icons8-ticket-100.png" height="100" width="100" alt=""
                                    srcset="">
                            </div>
                            Aucune category trouvée
                        </td>
                    </tr>
                @endforelse

            </tbody>


        </table>
    </div>
    {{ $categories->links('pagination::bootstrap-4') }}



</div>
