@section('titre', 'Détails de la commande')
@extends('admin.fixe')

@section('body')
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- start page title -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb ">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">{{ config('app.name') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('commandes') }}">Commandes</a>
                            </li>
                            <li class="breadcrumb-item active">Commande #{{ $commande->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="card radius-15">
            <div class="card-body">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="header-title">
                                Commande du client :
                                <span class="text-capitalize">
                                    {{ $commande->nom }}
                                </span>
                            </h5>

                        </div>

                        <div style="font-size: 13px; color: #333;">
                            {{ \App\Helpers\TranslationHelper::TranslateText('Référence :') }}
                            <strong>{{ $commande->reference }}</strong>
                            {{-- <br>
        <em>{{ \App\Helpers\TranslationHelper::TranslateText('Scannez ce code pour consulter la commande en ligne') }}</em>
                            < --}}</div>
                                <div>
                                    <button class="btn btn-dark btn-sm" type="button"
                                    onclick="url('{{ route('print_commande', ['id' => $commande->id]) }}')">
                                    <i class="ri-printer-line"></i>
                                    Impprimer
                                    </button>

                                   

                                </div>
                        </div>
                        <br>
                        <p class="text-muted small mb-0">
                            Dans cette section, vous pouvez trouver des détails tels que les produits commandés, les
                            quantités, les prix unitaires, les informations sur l'expédition et bien plus encore.
                            Assurez-vous de prendre en compte toutes les informations pertinentes pour traiter
                            efficacement la commande.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-8 table-responsive-sm">
                                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <thead class="table-dark">
                                        <tr>
                                            <th></th>
                                            <th>Product</th>
                                            {{-- <th>Type</th> --}}
                                            <th>Prix</th>
                                            <th>Qty</th>
                                            <th>Sub-Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($commande->contenus as $contenu)
                                        <tr>
                                            <td style="width: 50px;">
                                                @if ($contenu->type == 'produit')
                                                <img src="{{ Storage::url($contenu->produit->photo) }}"
                                                    width="40 " height="40 " class="rounded shadow"
                                                    alt="photo">
                                                @else
                                                <img src="{{ $contenu->pack->photo() }}"
                                                    width="40" height="40" class="rounded shadow"
                                                    alt="photo">
                                                @endif
                                            </td>
                                            <td>
                                                @if ($contenu->type == 'produit')
                                                {{ $contenu->produit->nom }}
                                                @else
                                                {{ $contenu->pack->nom }}
                                                @endif
                                            </td>
                                            {{-- <td>
                                                    <b class="text-capitalize">
                                                        {{ $contenu->type }}
                                            </b>
                                            </td> --}}
                                            <td>
                                                {{ intval($contenu->prix_unitaire) }} <x-devise></x-devise>
                                            </td>
                                            <td>
                                                {{ $contenu->quantite }}
                                            </td>
                                            <td>
                                                {{ $contenu->quantite * $contenu->prix_unitaire }} <x-devise></x-devise>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-4 text-capitalize">

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-3">
                                            <b>Résumé de la commande </b>
                                        </h6>
                                        Montant total : <br>
                                        <b>{{ $commande->montant()  - $commande->coupon ?? '' }}<x-devise></x-devise></b>
                                        @if ($commande->frais)
                                        ( Frais de livraison inclu ( {{ $commande->frais ?? 0 }} <x-devise></x-devise>) ) .
                                        @endif
                                        @if ($commande->coupon)
                                        ( Le code promo est appliqué d'une valeur de ( {{ $commande->coupon ?? 0 }} <x-devise></x-devise>) ) .
                                        @endif
                                        <br>
                                        <i class="ri-calendar-check-line"></i>Date : {{ $commande->created_at }}
                                    </div>
                                    <div class="text-center">
                                        {!! QrCode::size(70)->generate(route('print_commande', ['id' => $commande->id])) !!}
                                    </div>
                                </div>



                                <br>
                                <hr>
                                <h6 class="card-title mt-3">
                                    <b>Informations du client</b>
                                </h6>
                                <i class="ri-user-line"></i> Nom : {{ $commande->nom }} <br>
                                <!--  <i class="ri-user-line"></i> Prénom : {{ $commande->prenom }} <br> -->
                                <i class="ri-phone-line"></i> Phone : {{ $commande->phone }} <br>
                                {{-- <i class="ri-map-pin-line"></i> Adresse : {{ $commande->adresse }} <br>
                                --}}
                                <hr>
                                <h6 class="card-title mt-3">
                                    <b>Notes</b>
                                </h6>
                                <div>
                                    @if ($commande->note)
                                    {{ $commande->note }}
                                    @else
                                    <i class="text-muted">
                                        <i class="ri-information-line"></i>
                                        Aucune note disponible pour cette commande.
                                    </i>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->


    </div>
    @endsection