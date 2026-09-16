<div>
    <!-- Sélection du Magasin pour la gestion du stock -->
    <div class="card mb-4 border border-primary">
        <div class="card-body bg-light">
            <div class="row align-items-center">
                <div class="col-sm-4">
                    <label class="form-label font-weight-bold text-primary">
                        <i class="ri-store-2-line"></i> Magasin / Point de vente *
                    </label>
                </div>
                <div class="col-sm-8">
                    <select wire:model.live="shop_id" class="form-select @error('shop_id') is-invalid @enderror">
                        <option value="">-- Choisir le magasin pour le stock --</option>
                        @foreach ($shops ?? [] as $shop)
                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                    @error('shop_id')
                    <span class="small text-danger" role="alert"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Section Recherche et Ajout de Produit -->
        <div class="col-sm-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6>Ajouter un produit</h6>
                <div>
                    <!-- Indicateur de chargement discret pendant la recherche -->
                    <span wire:loading wire:target="key" class="spinner-border spinner-border-sm text-primary" role="status"></span>
                </div>
            </div>

            <div class="mb-3">
                <input type="text"
                    placeholder="{{ $shop_id ? 'Recherche d\'un produit...' : '⚠️ Choisissez d\'abord un magasin' }}"
                    wire:model.live.debounce.300ms="key"
                    class="form-control"
                    @if(!$shop_id) disabled title="Veuillez d'abord choisir un magasin" @endif>
            </div>

            <div>
                <table class="w-100">
                    @if ($produits)
                    @forelse ($produits as $index => $produit)
                    <tr>
                        <td class="align-middle py-1">
                            <span class="font-weight-medium">{{ $produit['nom'] }}</span>
                            <div class="small text-muted">
                                <span class="text-primary text-capitalize"> {{ $produit['type'] }} </span> |
                                {{ number_format($produit['prix'], 0, ',', ' ') }} <x-devise></x-devise>
                            </div>
                        </td>
                        <td class="text-end align-middle">
                            <div class="input-group input-group-sm mb-1">
                                <!-- Changement ici : .live pour garantir que la qté soit lue au clic du bouton -->
                                <!-- Dans votre fichier blade -->
                                <input id="quantite_{{ $produit['id'] }}"
                                    wire:model="quantites.{{ $produit['id'] }}"
                                    type="number"
                                    min="1"
                                    max="{{ $stocksDisponibles[$produit['id']] ?? 0 }}"
                                    class="form-control text-center"
                                    placeholder="Qté">

                                <button class="btn btn-primary"
                                    type="button"
                                    wire:click="ajouterProduit({{ $produit['id'] }}, '{{ $produit['type'] }}', '{{ $produit['reference'] }}')"
                                    @if(!$shop_id) disabled title="Choisissez un magasin d'abord" @endif>
                                    <i class="ri-add-circle-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2">
                            <div class="text-center p-2 text-muted">
                                <i class="ri-information-line"></i> Aucun produit trouvé ou disponible.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    @endif
                </table>
            </div>
        </div>

        <!-- Section Panier de la Commande -->
        <div class="col-sm-8">
            @include('components.alert')
          
            <div class="table-responsive-sm">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Désignation</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Montant Total</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @forelse ($paniers ?? [] as $item)
                        <tr>
                            <td>
                                <div class="font-weight-medium">{{ $item['nom'] }}</div>
                                <div class="small text-capitalize text-muted">
                                    {{ $item['type'] }}
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">x{{ $item['quantite'] }}</span></td>
                            <td>{{ number_format($item['prix'], 0, ',', ' ') }} <x-devise></x-devise></td>
                            <td class="font-weight-bold">{{ number_format($item['prix'] * $item['quantite'], 0, ',', ' ') }} <x-devise></x-devise></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger" type="button"
                                    wire:click="delete_from_session({{ $item['id'] }})">
                                    <i class="ri-delete-bin-6-line"></i>
                                </button>
                            </td>
                        </tr>
                        @php
                        $total += $item['prix'] * $item['quantite'];
                        @endphp
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Le panier est vide.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br>
    <hr><br>

    <!-- Formulaire de Finalisation (Masqué si le panier est vide OU si aucun shop n'est choisi) -->
    @if (!empty($paniers) && $shop_id)
    <div class="d-flex justify-content-between p-2 card mb-3" style="background-color: #027461; color: white;">
        <h5 class="header-title mb-0">Finalisation de la commande</h5>
    </div>

    <div class="card-body p-0">
        <form wire:submit.prevent="order">
            <div class="row">
                <!-- Informations Client -->
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" placeholder="Nom du client" required wire:model="nom" class="form-control">
                            @error('nom')
                            <span class="small text-danger" role="alert"> {{ $message }} </span>
                            @enderror
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Numéro de téléphone *</label>
                            <input type="tel" placeholder="Numéro de téléphone" wire:model="phone" class="form-control">
                            @error('phone')
                            <span class="small text-danger" role="alert"> {{ $message }} </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Recherche Client Existant -->
                <div class="col-sm-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>Recherche d'un client enregistré</h6>
                        <span wire:loading wire:target="recherche" class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    </div>
                    <div class="mb-3">
                        <input type="text" wire:model.live.debounce.300ms="recherche" placeholder="Nom, prénom, téléphone..." class="form-control">
                    </div>

                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Téléphone</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clients ?? [] as $client)
                                <tr>
                                    <td>{{ $client->phone }}</td>
                                    <td>{{ $client->nom }}</td>
                                    <td>{{ $client->prenom }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="import({{ $client->id }})">
                                            <i class="ri-import-line"></i> Importer
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center py-2">
                                        Aucun client trouvé
                                        @if($recherche) "<b>{{ $recherche }}</b>" @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <br>
            <hr>

            <!-- Zone de Validation Globale -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="mb-0">
                        Montant de la commande : <span class="text-success font-weight-bold">{{ number_format($total, 0, ',', ' ') }} <x-devise></x-devise></span>
                    </h5>
                </div>
                <div>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="ri-check-double-line"></i> Valider cette commande
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif
</div>