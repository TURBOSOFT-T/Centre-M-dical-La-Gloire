
<div  wire:poll.1s class="table-responsive">
    <table class="table axil-product-table axil-wishlist-table">
        <thead>
            <tr>
                <th scope="col" width="5%" class="product-remove"></th>
                <th scope="col" width="20%" class="product-thumbnail"> {{ \App\Helpers\TranslationHelper::TranslateText('Produit') }}</th>
              {{--   <th scope="col" class="product-title"></th> --}}
                <th scope="col" class="product-title"> {{ \App\Helpers\TranslationHelper::TranslateText('Date ajout') }}</th>
           
                <th scope="col" width="5%" class="product-price"> {{ \App\Helpers\TranslationHelper::TranslateText('Prix unitaire') }}</th>
                <th scope="col" class="product-stock-status">  {{ \App\Helpers\TranslationHelper::TranslateText('Status') }}</th>
                <th scope="col" width="30%" class="product-add-cart"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($favoris as $key => $favo)
            
            <tr>
                <td>
                    <div class="product-remove">
                    
                        <a class="remove-wishlist" wire:click="delete({{  $favo->id  ?? ''}})">
                            <i class="fal fa-times"></i>
                        </a>
                    </div>
                </td>
                {{-- <td class="product-remove"><a href="#" class="remove-wishlist"><i class="fal fa-times"></i></a></td> --}}
                <td class="product-thumbnail"><a href="{{ route('details-produits', ['id' => $favo->produit->id, 'slug'=>Str::slug(Str::limit($favo->produit->nom, 10))]) , }}"><img src="{{ Storage::url($favo->produit->photo) }}" alt="Digital Product"></a>
                {{ $favo->produit->nom ??  '' }}
                </td>
                {{-- <td class="product-title"><a href="{{ route('details-produits', ['id' => $favo->produit->id, 'slug'=>Str::slug(Str::limit($favo->produit->nom, 10))]) , }}">{{
                                            $favo->produit->nom }}</a></td> --}}
                <td>  <p class="date">{{ $favo->created_at->format('d-m-Y') }}</p></td>
                <td class="product-price" data-title="Price"><span class="currency-symbol"></span>  {{ $favo->produit->getPrice() }}  <x-devise></x-devise></td>
                <td class="product-stock-status" data-title="Status">   @if ($favo->produit->stock > 0)
                   
                    <label class="badge btn-bg-primary2">  {{ \App\Helpers\TranslationHelper::TranslateText('Stock disponible') }}</label>
                @else
                    <label class="badge bg-danger">  {{ \App\Helpers\TranslationHelper::TranslateText('Indisponible') }}</label>
                @endif</td>
                <td class="product-add-cart">
                    <a  onclick="AddToCart( {{ $favo->produit->id }} )" class="axil-btn btn-bg-primary2"> {{ \App\Helpers\TranslationHelper::TranslateText('Panier') }}</a></td>
            </tr>
            @empty
            <tr>
                <td colspan="6 ">
                    <div class="text-center p-5"><svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" width="120" height="120" fill="currentColor">
                            <path
                                d="M12.001 4.52853C14.35 2.42 17.98 2.49 20.2426 4.75736C22.5053 7.02472 22.583 10.637 20.4786 12.993L11.9999 21.485L3.52138 12.993C1.41705 10.637 1.49571 7.01901 3.75736 4.75736C6.02157 2.49315 9.64519 2.41687 12.001 4.52853ZM18.827 6.1701C17.3279 4.66794 14.9076 4.60701 13.337 6.01687L12.0019 7.21524L10.6661 6.01781C9.09098 4.60597 6.67506 4.66808 5.17157 6.17157C3.68183 7.66131 3.60704 10.0473 4.97993 11.6232L11.9999 18.6543L19.0201 11.6232C20.3935 10.0467 20.319 7.66525 18.827 6.1701Z">
                            </path>
                        </svg> <br>
                        <h5>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Vous n\'avez pas de produits en favori.') }}
                            
                        
                        </h5>
                    </div>
                </td>
            </tr>
            @endforelse

        
        </tbody>
    </table>

    <style>
        .btn-bg-primary2 {
            background-color: #5EA13C;
           /*  color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none; */
        }

        .btn-bg-secondary2 {
        background-color: #EFB121; /* Couleur de fond, bleu dans cet exemple */
        color: #ffffff; /* Couleur du texte, blanc dans cet exemple */
        border: none;
        padding: 10px 20px; /* Optionnel, ajuste la taille */
        border-radius: 5px; /* Optionnel, arrondit les coins */
        text-decoration: none; /* Supprime le soulignement */
    }
    </style>
</div>