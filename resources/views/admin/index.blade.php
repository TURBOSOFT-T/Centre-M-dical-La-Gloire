@extends('admin.fixe')
@section('titre', 'Accueil')
@section('body')

    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <form  id="myForm" action="{{ route('filtre-dashboard') }}" method="POST" >
                        @csrf
                        <div class="input-group mb-3">
                            <input type="date" name="date_debut" class="form-control"  value="{{ session('date_debut', old('date_debut')) }}">
                            <input type="date" name="date_fin" class="form-control" value="{{ session('date_fin', old('date_fin')) }}">
                           
                            <button type="submit" class="btn btn-primary">
                                Filtrer
                            </button>

                            <input type="button" onclick="resetForm()" value="Réinitialiser" class="btn btn-secondary">
 
                           
                        </div>
                    </form>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-3">
                    <div class="card radius-15 bg-voilet">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h2 class="mb-0 text-white">
                                        {{ $totalDesCommandes }} <x-devise></x-devise>
                                        <i class='bx bxs-up-arrow-alt font-14 text-white'></i>
                                    </h2>
                                </div>
                                <div class="ms-auto font-35 text-white"><i class="bx bx-cart-alt"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-white">
                                        Ventes totals
                                    </p>
                                </div>
                                {{-- <div class="ms-auto font-14 text-white">+23.4%</div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="card radius-15 bg-primary-blue">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h2 class="mb-0 text-white">
                                        {{ $total_produits }}
                                        <i class='bx bxs-down-arrow-alt font-14 text-white'></i>
                                    </h2>
                                </div>
                                <div class="ms-auto font-35 text-white">
                                    <i class="ri-ink-bottle-line"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-white">Produits</p>
                                </div>
                                {{-- <div class="ms-auto font-14 text-white">+14.7%</div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="card radius-15 bg-rose">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h2 class="mb-0 text-white">
                                        {{ $total_commandes }}
                                        <i class='bx bxs-up-arrow-alt font-14 text-white'></i>
                                    </h2>
                                </div>
                                <div class="ms-auto font-35 text-white"><i class="bx bx-tachometer"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-white">
                                        Total des commandes
                                    </p>
                                </div>
                                {{-- <div class="ms-auto font-14 text-white">-12.9%</div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="card radius-15 bg-sunset">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h2 class="mb-0 text-white">
                                        {{ $totalUser }}
                                        <i class='bx bxs-up-arrow-alt font-14 text-white'></i>
                                    </h2>
                                </div>
                                <div class="ms-auto font-35 text-white"><i class="bx bx-user"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-white">
                                        Clients
                                    </p>
                                </div>
                                {{-- <div class="ms-auto font-14 text-white">+13.6%</div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->

            <div class="row">
                <div class="col-sm-6">
                    <div class="card radius-15">
                        <div class="card-header border-bottom-0">
                            <div class="d-lg-flex align-items-center">
                                <div>
                                    <h5 class="mb-2 mb-lg-0">
                                        Statistiques globale
                                    </h5>
                                </div>
                                <div class="ms-lg-auto mb-2 mb-lg-0">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart1" data-ventesPerMonth='@json($ventesPerMonth)'
                                data-commandesPerMonth='@json($commandesPerMonth)'
                                data-visitsPerMonth='@json($visitsPerMonth)'></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card radius-15">
                        <div class="card-header border-bottom-0">
                            <div class="d-lg-flex align-items-center">
                                <div>
                                    <h5 class="mb-2 mb-lg-0">
                                        Statistiques globale de confirmation des commandes
                                    </h5>
                                </div>
                                <div class="ms-lg-auto mb-2 mb-lg-0">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart12" data-stat_commande_confirmer_Month='@json($stat_commande_confirmer_Month)'
                                data-stat_commande_non_confirmer_Month='@json($stat_commande_non_confirmer_Month)'></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-3">
                    <div class="card radius-15">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        Statistiques des commandes
                                    </h5>
                                </div>
                                <div class="dropdown ms-auto">
                                    <div class="cursor-pointer font-24 dropdown-toggle dropdown-toggle-nocaret"
                                        data-bs-toggle="dropdown"><i class="bx bx-dots-horizontal-rounded"></i>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="javascript:;">
                                            Action</a>
                                        <a class="dropdown-item" href="javascript:;">Another action</a>
                                        <div class="dropdown-divider"></div> <a class="dropdown-item"
                                            href="javascript:;">Something else here</a>
                                    </div>
                                </div>
                            </div>
                            <div id="chart2" data-json="{{ $json_commandes }}"></div>

                            <div class="legends">
                                <div class="row">
                                    <div class="col-12 col-lg-5">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i
                                                    class='bx bxs-circle font-13 text-primary-blue me-2'></i>
                                                Créé
                                            </div>
                                            <div>
                                                {{ $commandes['créé'] ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="my-2"></div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i
                                                    class='bx bxs-circle font-13 text-shineblue me-2'></i>
                                                Traitement
                                            </div>
                                            <div>
                                                {{ $commandes['traitement'] ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="my-2"></div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i
                                                    class='bx bxs-circle font-13 text-primary me-2'></i>
                                                Livraison
                                            </div>
                                            <div>
                                                {{ $commandes['livraison'] ?? 0 }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-2">
                                        <div class="vertical-separater"></div>
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i
                                                    class='bx bxs-circle font-13 text-dark me-2'></i>
                                                Livré
                                            </div>
                                            <div>
                                                {{ $commandes['livré'] ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="my-2"></div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i class='bx bxs-circle font-13 me-2'
                                                    style="color: #00cc00;"></i>
                                                Payée
                                            </div>
                                            <div>
                                                {{ $commandes['payée'] ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="my-2"></div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i class='bx bxs-circle font-13 me-2'
                                                    style="color: #ff8000;"></i>
                                                Planification
                                            </div>
                                            <div>
                                                {{ $commandes['planification'] ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i
                                                    class='bx bxs-circle font-13 text-red me-2'></i>
                                                Retournée
                                            </div>
                                            <div>
                                                {{ $commandes['retournée'] ?? 0 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="card radius-15">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        Etat de confirmation
                                    </h5>
                                </div>
                            </div>
                            <div id="chart22" data-confirmer=" {{ $etat_commandes['confirmer'] }}"
                                data-non_confirmer=" {{ $etat_commandes['non-confirmer'] }}"></div>
                            <br><br><br>
                            <div class="legends">
                                <div class="row">
                                    <div class="col">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i class='bx bxs-circle font-13  me-2'
                                                    style="color: #00cc00;"></i>
                                                Confirmé
                                            </div>
                                            <div>
                                                {{ $etat_commandes['pourcentage_confirmer'] }} %
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-secondary"><i class='bx bxs-circle font-13  me-2'
                                                    style="color: #ff392b;"></i>
                                                Non confimé
                                            </div>
                                            <div>
                                                {{ $etat_commandes['pourcentage_non-confirmer'] }} %
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card radius-15">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        Statistiques d'enregistrement des clients
                                    </h5>
                                </div>
                                <div class="dropdown ms-auto">
                                </div>
                            </div>
                            <div class="row mt-3 g-3">
                                <div class="col-12 col-lg-6">
                                    <div class="card radius-15 border shadow-none">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <p class="mb-0">
                                                        Total des utilisateurs
                                                    </p>
                                                </div>
                                                <div class="ms-auto text-success">
                                                    <span><i class="ri-user-3-line"></i></span>
                                                </div>
                                            </div>
                                            <h4 class="mb-0">
                                                {{ $totalUser }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="chart3" data-values="@json($inscriptionMonth)"></div>
                        </div>
                    </div>
                </div>
            </div><!--end row-->


            <div class="row">
                <div class="col-12 col-lg-3 d-flex">
                    <div class="card radius-15 w-100">
                        <div class="card-body">
                            <div class="d-lg-flex align-items-center">
                                <div>
                                    <h5 class="mb-4">
                                        Gestion des commandes
                                    </h5>
                                </div>
                                <div class="dropdown ms-auto">
                                    <div class="cursor-pointer font-24 dropdown-toggle dropdown-toggle-nocaret"
                                        data-bs-toggle="dropdown"><i class="bx bx-dots-horizontal-rounded"></i>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-right"> <a class="dropdown-item"
                                            href="javascript:;">Action</a>
                                        <a class="dropdown-item" href="javascript:;">Another action</a>
                                        <div class="dropdown-divider"></div> <a class="dropdown-item"
                                            href="javascript:;">Something else here</a>
                                    </div>
                                </div>
                            </div>
                            @foreach ($statistique_commandes_graph as $stat)
                                <div class="progress-wrapper mb-4">
                                    <p class="mb-1 text-capitalize">
                                        {{ $stat['statut'] }} ( {{ $stat['valeur'] }} )
                                        <span class="float-end">
                                            {{ $stat['pourcentage'] }} %
                                        </span>
                                    </p>
                                    <div class="progress radius-15" style="height:5px;">
                                        <div class="progress-bar bg-wall" role="progressbar"
                                            style="width:  {{ $stat['pourcentage'] }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 ">
                    <!--end row-->
                    <div class="card radius-15">
                        <div class="card-header border-bottom-0">
                            <div class="d-lg-flex align-items-center">
                                <div>
                                    <h5 class="mb-2 mb-lg-0">
                                        Profit net ( <x-devise></x-devise> )
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                              <div id="chart11" data-profilNet='@json($ventesPerMonth)'>
                                </div>
                            
                         {{--    <div id="chart11" data-profilNet='@json($profilNet)'></div> --}}
                        </div>
                    </div>
                </div>
                        <div class="col-12 col-lg-3">
                            <div class="card radius-15">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h5 class="mb-0">
                                            Top des villes avec le plus de commande.
                                        </h5>
                                    </div>
                                    <hr />
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Villes</th>
                                                    <th>Commandes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($top_gouvernorat as $key=>$top)
                                                    <tr>
                                                        <td>
                                                            {{ $key + 1 }}
                                                        </td>
                                                        <td> {{ $top['nom'] }} </td>
                                                        <td>{{ $top['total'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="text-center p-3">
                                                                Aucune donnée disponible !
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


            </div>

        </div>
    </div>

@endsection
@push('scripts')
    <!-- Vector map JavaScript -->
    <script src="/admin/assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
    <script src="/admin/assets/js/index2.js"></script>
    <!-- App JS -->
   
@endpush

<script>
    function resetForm() {
        document.querySelector("input[name='date_debut']").value = '';
        document.querySelector("input[name='date_fin']").value = '';
        document.getElementById("myForm").submit(); // Optionnel : soumettre le formulaire après réinitialisation
    }
</script>
