@php
$config = DB::table('configs')->select('icon', 'logo', 'telephone', 'email', 'addresse')->first();

// Détermination de l'image à utiliser pour le logo
$logoPath = public_path('/icons/logo.jpg');
if ($config && !empty($config->logo) && file_exists(storage_path('app/public/' . $config->logo))) {
$logoPath = storage_path('app/public/' . $config->logo);
} elseif ($config && !empty($config->icon) && file_exists(storage_path('app/public/' . $config->icon))) {
$logoPath = storage_path('app/public/' . $config->icon);
}

$logoBase64 = '';
if (file_exists($logoPath)) {
$logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
}
@endphp
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> @yield('titre') - (Admin) {{ config('app.name') }}</title>
    <!--favicon-->
    <link rel="icon" href="{{ Storage::url($config->icon) }}" type="image/png" />
    <!--plugins-->
    <link href="/admin/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="/admin/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="/admin/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="/admin/assets/css/pace.min.css" rel="stylesheet" />
    <script src="/admin/assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/admin/assets/css/bootstrap.min.css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Roboto&display=swap" />
    <!-- Icons CSS -->
    <link rel="stylesheet" href="/admin/assets/css/icons.css" />
    <!-- App CSS -->
    <link rel="stylesheet" href="/admin/assets/css/app.css" />
    <link rel="stylesheet" href="/admin/assets/css/dark-sidebar.css" />
    <link rel="stylesheet" href="/admin/assets/css/dark-theme.css" />
    <link rel="stylesheet" href="{{ asset('admin-css.css?v=') . time() }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @yield('header')



    <script>
        function url(url) {
            document.location.href = url;
        }

        function url2(url) {
            window.open(url, '_blank');
        }

        function toggle_confirmation(productId) {
            const confirmBtn = document.getElementById('confirmBtn' + productId);
            if (!confirmBtn.classList.contains('d-none')) {
                confirmBtn.classList.add('d-none');
            } else {
                // Masquer tous les autres boutons de confirmation s'ils sont visibles
                document.querySelectorAll('.confirm-btn').forEach(btn => {
                    if (!btn.classList.contains('d-none')) {
                        btn.classList.add('d-none');
                    }
                });
                confirmBtn.classList.remove('d-none');
            }
        }



        function preview_illustration(key) {
            const fileInput = document.getElementById('file-input-' + key);
            fileInput.click();
        }



        var old_total = 0;

        function fetchNotificationsAndUpdateComponent() {
            // Appel AJAX pour récupérer les données du contrôleur
            fetch('{{ route("live_notifications") }}')
                .then(response => response.json())
                .then(data => {
                    const total = data.total;
                    // ,set value in msg-count span select by class name
                    document.querySelector('.msg-count').textContent = total;
                    // Vérifier si le total est supérieur à l'ancien total
                    if (total > old_total) {
                        // Jouer l'audio uniquement s'il y a une nouvelle notification
                        const audio = new Audio('/icons/system-notification-199277.wav');

                        audio.play();
                        // Actualiser le composant Livewire
                        Livewire.dispatch('notificationUpdated');
                        // Mettre à jour l'ancien total avec le nouveau total
                        old_total = total;
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des notifications :', error);
                });
        }

        // Exécuter la fonction toutes les 5 secondes
        setInterval(fetchNotificationsAndUpdateComponent, 5000);
    </script>


</head>

<body>
    <!-- wrapper -->
    <div class="wrapper">
        <!--sidebar-wrapper-->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div class="">
                    <img src="{{ Storage::url($config->icon) }}" class="logo-icon-2" alt="" />

                     @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="logo" class="logo-icon-2">
                        @endif
                </div>
                <div>
                    <h4 class="logo-text">
                        {{ config('app.name') }}
                    </h4>
                </div>
                <a href="javascript:;" class="toggle-btn ms-auto"> <i class="bx bx-menu"></i>
                </a>
            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                @can('dashboard')
                <li>
                    <a href="{{ route('dashboard') }}">
                        <div class="parent-icon icon-color-1">
                            <img src="/icons/diagramme-circulaire.svg" height="20" width="20" alt="icon"
                                srcset="">
                        </div>
                        <div class="menu-title">My Dashboard</div>
                    </a>
                </li>
                @endcan
                <li class="menu-label">Web Apps</li>
                @can('category_view')
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon icon-color-3"> <i class="ri-layout-grid-line"></i>

                        </div>
                        <div class="menu-title">
                            Categories
                        </div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('categories') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Liste des categories
                            </a>
                        </li>
                        @can('category_add')
                        <li>
                            <a href="{{ route('category.add') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Créer une categorie
                            </a>
                        </li>
                        @endcan


                    </ul>
                </li>
                @endcan

                @can('marque_view')
                <li>
                    <a href="{{ route('laboratoires') }}">
                        <div class="parent-icon icon-color-3">
                            <i class="bx bx-test-tube"></i>
                        </div>
                        <div class="menu-title">
                            Laboratoire
                        </div>
                    </a>
                </li>
                @endcan

                @role('admin')
                <li>
                    <a href="{{ route('shops') }}">
                        <div class="parent-icon icon-color-6">
                            <i class="bx bx-capsule"></i>
                        </div>
                        <div class="menu-title">
                            Gestion des pharmacies
                        </div>
                    </a>
                </li>
                @endrole



                <li>
                    <a href="{{ route('assurances') }}">
                        <div class="parent-icon icon-color-3">
                            <i class="bx bx-shield-quarter"></i>
                        </div>
                        <div class="menu-title">
                            Assurances Santé
                        </div>
                    </a>
                </li>



                <li>
                    <a href="{{ route('examens') }}">
                        <div class="parent-icon icon-color-3">
                            <i class="bx bx-test-tube"></i>
                        </div>
                        <div class="menu-title">
                            Examens
                        </div>
                    </a>
                </li>


                @can('product_view')
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon icon-color-3"> <i class="bx bx-conversation"></i>
                        </div>
                        <div class="menu-title">
                            Produits
                        </div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('produits') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Liste des produits
                            </a>
                        </li>
                        @can('product_add')
                        <li>
                            <a href="{{ route('produit.add') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Créer un produit
                            </a>
                        </li>
                        @endcan


                    </ul>
                </li>
                @endcan




                @can('order_view')
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon icon-color-4">
                            <i class="ri-truck-line"></i>
                        </div>
                        <div class="menu-title">
                            Commandes
                        </div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('commandes') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Toutes les commandes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('commandes_en_lives') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Commandes en cours
                            </a>
                        </li>






                    </ul>
                </li>
                @endcan

{{-- MODULE 1 : ACCUEIL & ADMINISTRATION --}}
<li>
    <a href="javascript:;" class="has-arrow">
        <div class="parent-icon icon-color-5">
            <i class="ri-user-shared-line"></i>
        </div>
        <div class="menu-title">
            Accueil & Admissions
        </div>
    </a>
    <ul>
        @role('admin')
        <li>
            <a href="{{ route('personnels') }}">
                <i class="bx bx-right-arrow-alt"></i>
                Gestion du Personnel
            </a>
        </li>
        @endrole
        <li>
            <a href="{{ route('patients') }}">
                <i class="bx bx-right-arrow-alt"></i>
                Gestion des Patients
            </a>
        </li>
        <li>
            <a href="{{ route('visites') }}">
                <i class="bx bx-right-arrow-alt"></i>
                Registre des Visiteurs
            </a>
        </li>
    </ul>
</li>




                {{-- Module Suivi & Gestion Médicale --}}
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon icon-color-4">
                            <i class="ri-hospital-line"></i>
                        </div>
                        <div class="menu-title">
                            Gestion Médicale
                        </div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('dossiersmedicaux') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Dossiers Médicaux
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('consultations') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Consultations
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hospitalisations') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Hospitalisations
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rendez-vous') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Rendez-vous
                            </a>
                        </li>
                    </ul>
                </li>










                {{-- 🔹 Menu Statistiques & Rapports --}}
                @role('admin')
                <li>
                    <a href="{{ route('admin.statistiques') }}">
                        <div class="parent-icon icon-color-6">
                            <i class="ri-bar-chart-box-line"></i>
                        </div>
                        <div class="menu-title">
                            Statistiques & Bilans
                        </div>
                    </a>
                </li>
                @endcan



                @can('setting_view')
                <li class="menu-label">
                    Settings
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon icon-color-6">
                            <i class="ri-settings-2-line"></i>
                        </div>
                        <div class="menu-title">
                            Configurations
                        </div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('contact-admin') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Clinique
                            </a>
                        </li>



                    </ul>


                </li>
                @endcan


            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar-wrapper-->
        <!--header-->
        <header class="top-header">
            <nav class="navbar navbar-expand">
                <div class="left-topbar d-flex align-items-center">
                    <a href="javascript:;" class="toggle-btn"> <i class="bx bx-menu"></i>
                    </a>
                </div>

                <!-- MENU DROPDOWN MESSAGES (Intégré ici) -->

                <div class="flex-grow-1 search-bar">
                    <div class="input-group">
                        <button class="btn btn-search-back search-arrow-back" type="button"><i
                                class="bx bx-arrow-back"></i></button>
                        <input type="text" class="form-control" placeholder="search" />
                        <button class="btn btn-search" type="button"><i class="lni lni-search-alt"></i></button>
                    </div>
                </div>
                <div class="right-topbar ms-auto">
                    <ul class="navbar-nav">
                        <!-- <li class="nav-item search-btn-mobile">
                            <a class="nav-link position-relative" href="javascript:;"> <i
                                    class="bx bx-search vertical-align-middle"></i>
                            </a>
                        </li> -->

                        <li class="nav-item dropdown dropdown-lg">
                            @livewire('admin-messages-dropdown')
                        </li>
                        <li class="nav-item dropdown dropdown-lg">
                            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                                href="javascript:;" data-bs-toggle="dropdown"> <i
                                    class="bx bx-bell vertical-align-middle"></i>
                                <span class="msg-count">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:;">
                                    <div class="msg-header">
                                        <h6 class="msg-header-title">
                                            <span class="msg-count">0</span>
                                            Notifications
                                        </h6>
                                        <p class="msg-header-subtitle">
                                            Notifications sur {{ config('app.name') }}.
                                        </p>
                                    </div>
                                </a>
                                @livewire('AdminNotifications')
                            </div>
                        </li>

                        <li class="nav-item dropdown dropdown-user-profile">
                            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                                data-bs-toggle="dropdown">
                                <div class="d-flex user-box align-items-center">
                                    <div class="user-info">
                                        <p class="user-name mb-0">
                                            {{ Auth::user()->nom }}
                                        </p>
                                        <p class="designattion mb-0">
                                            Connecté
                                        </p>
                                    </div>
                                    <img src="{{ Auth::user()->avatar() }}" class="user-img" alt="user avatar">
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">

                                <a class="dropdown-item {{ request()->routeIs('home') ? 'active' : '' }}"
                                    href="{{ route('home') }}">
                                    <i class="bx bx-home"></i>
                                    <span>Accueil</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('parametres') }}">
                                    <i class="bx bx-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="bx bx-tachometer"></i>
                                    <span>Dashboard</span>
                                </a>
                                <div class="dropdown-divider mb-0"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    <i class="bx bx-power-off"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>





        </header>

        <!-- Bulle de messagerie des messages clients -->

        <!--end header-->
        <!--page-wrapper-->
        <div class="page-wrapper">

            @yield('body')


        </div>
        <!-- END wrapper -->


        <!--end page-content-wrapper-->
    </div>
    <!--end page-wrapper-->
    <!--start overlay-->
    <div class="overlay toggle-btn-mobile"></div>
    <!--end overlay-->
    <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i
            class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
    <!--footer -->
    <div class="footer">
        <p class="mb-0">@ {{ date('Y') }} | Developed By :
            <a href="https://turbosoft-services.com" target="_blank" style="color: #c71f17 !important;">
                <strong>
                    TUROSOFT
                </strong>
            </a>
        </p>
    </div>
    <!-- end footer -->
    </div>
    <!-- end wrapper -->
    <!-- Bootstrap JS -->
    <script src="/admin/assets/js/bootstrap.bundle.min.js"></script>

    <!--plugins-->
    <script src="/admin/assets/js/jquery.min.js"></script>

    <script src="/admin/assets/js/jquery.min.js"></script>
    <script src="/admin/assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="/admin/assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="/admin/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <!-- App JS -->
    <script src="/admin/assets/js/app.js"></script>
    @stack('scripts')

    @if (auth()->user()->is_admin)
    <script>
        function sendMarkRequest(id = null) {
            return $.ajax("{{ route('markNotification') }}", {
                method: 'POST',
                data: {
                    _token,
                    id
                }
            });
        }
        $(function() {
            $('.mark-as-read').click(function() {
                let request = sendMarkRequest($(this).data('id'));
                request.done(() => {
                    $(this).parents('div.alert').remove();
                });
            });
            $('#mark-all').click(function() {
                let request = sendMarkRequest();
                request.done(() => {
                    $('div.alert').remove();
                })
            });
        });
    </script>
    @endif
</body>

</html>