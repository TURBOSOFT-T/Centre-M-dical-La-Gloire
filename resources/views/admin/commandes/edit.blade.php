@section('titre', 'Modifier la commande')
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
                                    Modifier la commandedu client du client :
                                    <span class="text-capitalize">
                                        {{ $commande->nom }}
                                    </span>
                                </h5>
                            </div>

                           
                            @if($commande->commercial->nom)
                                 <div>
                                <h5 class="header-title">
                                    Responssable de la commande :
                                    <span class="text-capitalize">
                                        {{ $commande->commercial->nom ?? 'Comm' }}
                                    </span>
                                </h5>
                            </div>  
                            @endif
                            <div>
                                <button class="btn btn-dark btn-sm" type="button"
                                    onclick="url('{{ route('print_commande', ['id' => $commande->id]) }}')">
                                    Impprimer
                                </button>
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive-sm">
                            @livewire('Commandes.EditCommande', ['commande' => $commande])
                        </div>
                    </div>
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->


    </div>
@endsection
