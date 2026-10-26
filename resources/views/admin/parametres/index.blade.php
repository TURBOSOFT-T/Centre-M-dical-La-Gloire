@section('titre', 'Paramètres')
@extends('admin.fixe')

@section('body')


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
                                    <a href="{{ route('parametres') }}">Paramètres</a>
                                </li>
                                <li class="breadcrumb-item active">{{ Auth::user()->nom }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!--end breadcrumb-->
            <div class="user-profile-page">
                <div class="card radius-15">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-7 border-right">
                                <div class="d-md-flex align-items-center">
                                    <div class="mb-md-0 mb-3">
                                        <img src="{{ Auth::user()->avatar() }}" class="rounded-circle shadow" width="130"
                                            height="130" alt="" />
                                    </div>
                                    <div class="ms-md-4 flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <h4 class="mb-0">
                                                {{ Auth::user()->nom }}
                                            </h4>
                                        </div>
                                        <p class="mb-0 text-muted">
                                            Administrateur
                                        </p>
                                        <p class="text-primary">
                                            <i class='bx bx-buildings'></i> {{ Auth::user()->adresse }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-5">
                                <p style="text-align: right;">
                                    <img src="/icons/logo.png" height="50" alt="" srcset="">
                                </p>
                            </div>
                        </div>
                        <!--end row-->
                        <br>
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#Experience">
                                    <span class="p-tab-name">
                                        <i class="ri-history-line"></i>
                                        Connexion
                                    </span>
                                    <i class='bx bx-donate-blood font-24 d-sm-none'></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#Edit-Profile">
                                    <span class="p-tab-name">
                                        <i class="ri-lock-password-line"></i>
                                        Edit Profile
                                    </span>
                                    <i class='bx bx-message-edit font-24 d-sm-none'></i>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            <div class="tab-pane fade show active" id="Experience">
                                <div class="card shadow-none border mb-0 radius-15">
                                    <div class="card-body">
                                        <div class="d-sm-flex align-items-center mb-3">
                                            <h5 class="mb-0">
                                                Historique de connexion
                                            </h5>
                                        </div>
                                        <div class="table-responsive">
                                            <table class=" table ">
                                                <thead class="table-dark table cusor">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Statut</th>
                                                        <th>Adresse ip</th>
                                                        <th>Date</th>
                                                        <th>Navigateur</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($connexions as $connexion)
                                                        <tr>
                                                            <td>
                                                                @if (strpos($connexion->user_agent, 'Mobile') !== false || strpos($connexion->user_agent, 'Android') !== false)
                                                                    <p>
                                                                        <i class="ri-smartphone-line"></i>
                                                                        Téléphone.
                                                                    </p>
                                                                @else
                                                                    <p>
                                                                        <i class="ri-computer-line"></i>
                                                                        Ordinateur
                                                                    </p>
                                                                @endif

                                                            </td>
                                                            <td>
                                                                @if (request()->ip() == $connexion->ip_address)
                                                                    <i class="ri-play-circle-fill text-success"></i>
                                                                    Active
                                                                @else
                                                                    <i class="ri-stop-fill text-danger"></i>
                                                                    Terminer
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $connexion->ip_address }}
                                                            </td>
                                                            <td>
                                                                {{ $connexion->created_at }}
                                                            </td>
                                                            <td>
                                                                {{ strlen($connexion->user_agent) > 50 ? substr($connexion->user_agent, 0, 50) . '...' : $connexion->user_agent }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5">
                                                                <div class="alert alert-warning text-center">
                                                                    <strong>Aucune connexion trouvée</strong>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="Edit-Profile">
                                <div class="card shadow-none border mb-0 radius-15">
                                    <div class="card-body">
                                        @livewire('AdminParametres')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
