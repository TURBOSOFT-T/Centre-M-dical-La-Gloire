@include('sweetalert::alert')




<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Création compte</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ Storage::url($config->icon ?? ' ') }}">
    <!-- CSS
    ============================================ -->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/font-awesome.css">
    <link rel="stylesheet" href="assets/css/vendor/flaticon/flaticon.css">
    <link rel="stylesheet" href="assets/css/vendor/slick.css">
    <link rel="stylesheet" href="assets/css/vendor/slick-theme.css">
    <link rel="stylesheet" href="assets/css/vendor/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/css/vendor/sal.css">
    <link rel="stylesheet" href="assets/css/vendor/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/vendor/base.css">
    <link rel="stylesheet" href="assets/css/style.min.css">

</head>


<body>
    <div class="axil-signin-area">

        <!-- Start Header -->
        <div class="signin-header">
            <div class="row align-items-center">
                <div class="">
                    {{-- <a href="{{ url('home') }}" class="site-logo"><img src="{{ Storage::url($config->logo) }}" width="50" height="50" alt="logo"></a>
                    --}}
                </div>


                <div class="col-sm-8 d-flex justify-content-end">
                    <a href="{{ route('home') }}" class="logo logo-dark">
                        <img src="{{ Storage::url($config->logo ?? ' ') }}" width="200" height="200" alt="Site Logo">
                    </a>

                </div>
                <div class="col-md-4">
                    <div class="singin-header-btn">

                        <p>
                            {{ \App\Helpers\TranslationHelper::TranslateText('Vous avez un compte') }}?

                        </p>

                        <a href="{{ url('login') }}" class="axil-btn btn-bg-primary2 sign-up-btn">
                            {{ \App\Helpers\TranslationHelper::TranslateText('Connexion') }}
                        </a>



                        <style>
                            .btn-bg-secondary2 {
                                background-color: #3577f0;
                                ;

                                color: #ffffff;

                                border: none;
                                padding: 10px 20px;

                                border-radius: 5px;

                                text-decoration: none;

                            }

                            .btn-bg-secondary2:hover {
                                background-color: #3577f0;
                                ;

                                color: #ffffff;

                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Header -->

        <div class="row">
            <div class="col-xl-4 col-lg-6">
                <style>
                    .bg_image--10 {
                        background-image: url('{{ Storage::url($config->image_register) }}');
                        background-repeat: no-repeat;
                        background-size: cover;
                        background-position: center center;


                    }

                    .axil-signin-banner .title {
                        font-size: 18px;
                        font-weight: bold;
                        text-transform: uppercase;
                        margin: 0;
                        position: relative;
                        z-index: 2;
                        color: #EFB121;
                        /* Exemple : couleur orange/rouge */
                    }
                </style>
                <div class="axil-signin-banner bg_image bg_image--10">
                    <h3 class="title">
                        {{ \App\Helpers\TranslationHelper::TranslateText('Une energie pure') }}.
                    </h3>
                </div>
            </div>
            <div class="col-lg-6 offset-xl-2">
                <div class="axil-signin-form-wrap">
                    <div class="axil-signin-form">
                        <h3 class="title"> </h3>
                        <br><br><br>

                        @livewire('front.register')

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS
============================================ -->
    <!-- Modernizer JS -->
    <script src="assets/js/vendor/modernizr.min.js"></script>
    <!-- jQuery JS -->
    <script src="assets/js/vendor/jquery.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/js/vendor/popper.min.js"></script>
    <script src="assets/js/vendor/bootstrap.min.js"></script>
    <script src="assets/js/vendor/slick.min.js"></script>
    <script src="assets/js/vendor/js.cookie.js"></script>
    <!-- <script src="assets/js/vendor/jquery.style.switcher.js"></script> -->
    <script src="assets/js/vendor/jquery-ui.min.js"></script>
    <script src="assets/js/vendor/jquery.ui.touch-punch.min.js"></script>
    <script src="assets/js/vendor/jquery.countdown.min.js"></script>
    <script src="assets/js/vendor/sal.js"></script>
    <script src="assets/js/vendor/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/vendor/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/vendor/isotope.pkgd.min.js"></script>
    <script src="assets/js/vendor/counterup.js"></script>
    <script src="assets/js/vendor/waypoints.min.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

</body>

</html>