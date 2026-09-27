@section('titre', 'Liste des examens')
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
                                <a href="{{ route('examens') }}">Les examens</a>
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
                                Liste des examens
                            </h5>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('examens.export.sql') }}" class="btn btn-outline-success radius-30">
                            <i class="bx bx-download me-1"></i> Exporter en SQL
                        </a>

                        <form action="{{ route('examens.import.sql') }}" method="POST" enctype="multipart/form-data" class="d-inline-flex align-items-center gap-2">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="file" name="sql_file" class="form-control" accept=".sql" required>
                                <button type="submit" class="btn btn-outline-primary" onclick="return confirm('Attention : Importer un fichier SQL va écraser les données actuelles de la table examens. Voulez-vous continuer ?')">
                                    <i class="bx bx-upload me-1"></i> Importer
                                </button>
                            </div>
                        </form>

                        {{-- Affichage des messages flash de succès ou d'erreur --}}
                        @if(session('success'))
                        <div class="alert alert-success mt-2 mb-0 py-1 small">
                            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger mt-2 mb-0 py-1 small">
                            <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
                        </div>
                        @endif
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-8">
                        @livewire('Examens.Liste')
                    </div>
                    <div class="col-sm-4">

                        <h5>
                            <b>
                                Ajouter un examens
                            </b>
                        </h5>
                        <br>

                        @livewire('Examens.Add')

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection