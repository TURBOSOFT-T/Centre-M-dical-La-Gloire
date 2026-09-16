@section('titre', 'Liste des marques')
@extends('admin.fixe')

@section('body')
    <!--page-content-wrapper-->
    <div class="page-content-wrapper">
        <div class="page-content">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="javascript: void(0);">{{ config('app.name') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('marques') }}">Les marques</a>
                                </li>
                                <li class="breadcrumb-item active">Liste</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->


            <div class="card radius-15">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card-title">
                                <h5 class="mb-0 my-auto">
                                    Liste des marques
                                </h5>
                            </div>
                        </div>
                        <div class="col-sm-6">

                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-sm-8">
                            @livewire('Marques.Liste')
                        </div>
                        <div class="col-sm-4">
                              @can('marque_add')
                            <h5>
                                <b>
                                    Ajouter une marque
                                </b>
                            </h5>
                            <br>
                            
                            @livewire('Marques.Add')
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection
