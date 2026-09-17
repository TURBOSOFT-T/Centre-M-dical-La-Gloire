<div>
    <form wire:submit="filtrer">
        <div class="row">
            <div class="col-sm-6">
                <span>
                    <b>{{ $produits->count() }}</b> Résultats sur {{ $total }}.
                </span>
            </div>
            <div class="col-sm-6">
                <div class="input-group mb-3">
                    <!-- <input type="text" class="form-control btn-sm" wire:model="key"
                        placeholder="Titre, Description des articles"> -->
                    <input type="text"
                        wire:model.live.debounce.300ms="key"
                        class="form-control"
                        placeholder="Recherche par Titre, Description et reference">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            Recherche
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('components.alert')

    <div class="table-responsive-sm">
        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
            <thead class="table-dark cursor">
                <tr>
                    <th title="Ajouter un produit dans la bannière de l'accueil">Top</th>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Disponibilité</th>
                    <th>Stocks par Boutique</th>
                    <th>Prix vente</th>
                    <th>Prix achat</th>
                    <th>Sell</th>
                    <th>Vues</th>
                  
                    <th>Création</th>
                    <th style="text-align: right;">
                        <span wire:loading>
                            <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20" class="rounded shadow" alt="Chargement...">
                        </span>
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($produits as $produit)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input" @checked($produit->top == 1)
                        wire:click="add_top({{ $produit->id }})">
                    </td>
                    <td>
                        <img src="{{ Storage::url($produit->photo) }}" width="40" height="40" class="rounded shadow" alt="">
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline" data-bs-toggle="modal" data-bs-target="#qr-code-{{ $produit->id }}">
                            <i class="ri-qr-code-line"></i>
                        </button>
                        {{ $produit->nom }}
                    </td>

                    <!-- Colonne 1 : Statut du Stock Général -->
                    <td class="cursor">
                        @if ($produit->stock > 50)
                        <span class="text-success" title="En Stock Globalement">
                            <i class="fas fa-check-circle"></i>
                            <span class="badge bg-success">En Stock</span>
                            <b>{{ $produit->stock }} U.</b>
                        </span>
                        @elseif ($produit->stock > 0 && $produit->stock <= 50)
                            <span title="{{ $produit->stock }} Produit(s) au total">
                            <b>{{ $produit->stock }} U.</b>
                            <span class="badge" style="background-color: #e0d600; color: #fff;">Stock Bas</span>
                            </span>
                            @else
                            <span class="text-danger" title="Rupture Générale">
                                <i class="fas fa-times-circle"></i>
                                <span class="badge bg-danger">Rupture</span>
                            </span>
                            @endif
                    </td>

                    <!-- Colonne 2 : Détails des Boutiques & Stocks Particuliers -->
                    <td>
                        @if($produit->shops && $produit->shops->isNotEmpty())
                        <div class="dropdown">
                            <button class="btn btn-xs btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.75rem; padding: 2px 6px;">
                                <i class="ri-store-2-line"></i> Voir la répartition ({{ $produit->shops->count() }})
                            </button>
                            <ul class="dropdown-menu shadow p-2" style="min-width: 200px; font-size: 0.85rem;">
                                <li class="dropdown-header border-bottom mb-1 pb-1"><b>Stocks par Magasin</b></li>
                                @foreach($produit->shops as $shop)
                                <li class="d-flex justify-content-between align-items-center my-1">
                                    <span><i class="ri-map-pin-line text-muted"></i> {{ $shop->name }}</span>
                                    <!-- On extrait la quantité stockée spécifiquement pour cette boutique via la table pivot -->
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ $shop->pivot->stock_particulier ?? 0 }} U.
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @else
                        <span class="text-muted small">Aucun shop assigné</span>
                        @endif
                    </td>

                    <td>
                        @if ($produit->inPromotion())
                        <span class="small">- {{ $produit->inPromotion()->pourcentage }} %</span>
                        <b class="text-success">{{ $produit->getPrice() }} <x-devise></x-devise></b>
                        <br>
                        <strike>
                            <span class="text-danger small">{{ $produit->prix }} <x-devise></x-devise></span>
                        </strike>
                        @else
                        {{ $produit->getPrice() }} <x-devise></x-devise>
                        @endif
                    </td>
                    <td>
                        @can('price_view')
                        {{ $produit->prix_achat }}
                        <x-devise></x-devise>
                    </td>
                    @endcan
                    <td>
                        <i class="ri-wallet-2-line vert"></i> {{ $produit->vendus->count() }}
                    </td>
                    <td>
                        <i class="ri-bar-chart-box-line vert"></i> {{ $produit->vues->count() }}
                    </td>
                
                    <td>{{ $produit->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: right;">
                        <div class="btn-group">
                            @can('gestion_stock')
                            <button class="btn btn-primary btn-sm" title="Ajouter Stock" wire:click="openModal({{ $produit->id }})">
                                <i class="fas fa-plus"></i>
                            </button>
                            @endcan

                            @can('product_edit')
                            <button class="btn btn-sm btn-dark" onclick="url('{{ route('produits.update', ['id' => $produit->id]) }}')">
                                <i class="ri-edit-box-line"></i>
                            </button>
                            @endcan

                            @can('product_edit')
                            <button class="btn btn-sm btn-warning" title="Promotion" onclick="url('{{ route('promotions_produit', ['id' => $produit->id]) }}')">
                                <i class="ri-discount-percent-fill"></i>
                            </button>
                            @endcan

                            @can('product_delete')
                            <button class="btn btn-sm btn-danger" onclick="toggle_confirmation({{ $produit->id }})">
                                <i class="ri-delete-bin-6-line"></i>
                            </button>
                            @endcan
                        </div>

                        <!-- QrCode Modal -->
                        <!-- QrCode Modal -->
                        <div class="modal fade" id="qr-code-{{ $produit->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Accès rapide au produit</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table" style="text-align: left; vertical-align: middle;">
                                            <tr>
                                                <td>Prix d'achat :</td>
                                                <td>{{ $produit->prix_achat }} <x-devise></x-devise></td>
                                            </tr>
                                            <tr>
                                                <td>Bénéfice / produit :</td>
                                                <td>{{ $produit->prix - $produit->prix_achat }} <x-devise></x-devise></td>
                                            </tr>
                                            <tr>
                                                <td>Référence :</td>
                                                <td>{{ $produit->reference }}</td>
                                            </tr>

                                            <!-- Section Stocks par Boutique -->
                                            <tr class="table-light">
                                                <td colspan="2" class="fw-bold text-secondary text-uppercase" style="font-size: 0.8rem;">
                                                    Stocks par boutique
                                                </td>
                                            </tr>
                                            @foreach($produit->shops as $shop)
                                            <tr>
                                                <td class="ps-3 text-muted">{{ $shop->name }} :</td>
                                                <td>
                                                    {{-- Ajustez la relation ou méthode selon votre structure (ex: $produit->stockInShop($shop->id) ou $shop->pivot->stock) --}}
                                                    <span class="fw-semibold">
                                                        {{-- Version sécurisée avec l'opérateur optionnel de PHP 8+ --}}
                                                        {{ $shop->pivot->stock_particulier ?? 0 }} U. </span>
                                                </td>
                                            </tr>
                                            @endforeach

                                            <!-- Total Global -->
                                            <tr class="table-warning font-weight-bold">
                                                <td>Stock Total Global :</td>
                                                <td class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">{{ $produit->stock }} U.</span>
                                                    <b class="cursor btn btn-xs btn-outline-dark py-0 px-1" onclick="url('{{ route('produits.historique', ['id' => $produit->id]) }}')">
                                                        <i class="ri-history-fill"></i> Historique
                                                    </b>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <div class="text-center p-2">
                                            {{-- {!! QrCode::size(100)->generate(route('produit2', ['id' => $produit->id])) !!} --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-sm btn-success d-none" type="button" id="confirmBtn{{ $produit->id }}" wire:click="delete({{ $produit->id }})">
                            <i class="bi bi-check-circle"></i>
                            <span class="hide-tablete">Confirmer</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center">
                        <div>
                            <img src="/icons/icons8-ticket-100.png" height="100" width="100" alt="">
                        </div>
                        Aucun produit trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $produits->links('pagination::bootstrap-5') }}

    @role('admin')
    <div class="text-end p-2">
        <a href="{{ route('corbeille') }}" class="text-danger">
            <i class="ri-delete-bin-line"></i> Corbeille ( {{ $total_supprimers }} )
        </a>
    </div>
    @endrole

    <!-- Livewire Modal : Ajouter du Stock -->
    @if ($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter du Stock pour : <b>{{ $selectedProduit }}</b></h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <form wire:submit="addStock">
                        <div class="mb-3">
                            <label for="shop_id" class="form-label">Sélectionner la boutique</label>
                            <select id="shop_id" wire:model="shop_id" class="form-select" required>
                                <option value="">-- Choisir une boutique --</option>
                                @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                            @error('shop_id')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock_input" class="form-label">Quantité à ajouter</label>
                            <input type="number" id="stock_input" wire:model="stock" class="form-control" min="1" required autocomplete="off">
                            @error('stock')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading wire:target="addStock" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Ajouter au stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }

        .badge-success {
            background-color: green;
        }

        .badge-danger {
            background-color: red;
        }

        .btn-xs {
            padding: 1px 5px;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 3px;
        }
    </style>


    <style>
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 10px;
        }

        .page-item .page-link {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }

        .page-item.active .page-link {
            background-color: #5EA13C;
            /* Couleur de votre boutique */
            color: white;
            border-color: #5EA13C;
        }
    </style>

</div>