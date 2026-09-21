@include('sweetalert::alert')

<!doctype html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <title> @yield('titre') -Centre Médical La Gloire</title>
<link rel="icon" href="{{ Storage::url($config->icon) }}" type="image/png" />
	


    <!-- mobile responsive meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="57x57" href="images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-icon-180x180.png">
      <link rel="manifest" href="images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/Script.js"></script>
</head>


<body class="active-preloader-ovh">


    <div class="top-bar home-one home-three">
        <div class="thm-container clearfix">
            <div class="right-content pull-left">
                <ul class="contact-infos">
                    <li>
                        <p><i class="fa fa-phone"></i>Emergancy Helpline: <span>1-258-985-703</span></p>
                    </li>
                    <li>
                        <p><i class="fa fa-map-marker-alt"></i>322 Willis Avenue, Newyork 33069</p>
                    </li>
                    <li>
                        <p><i class="fa fa-clock"></i>Mon to Sat : 08:00 am - 19:00 pm</p>
                    </li>
                </ul><!-- /.contact-infos -->
            </div><!-- /.right-content -->
            <div class="left-content pull-right">
                <div class="social">
                    <a href="#" class="fab fa-twitter"></a><!--
                    --><a href="#" class="fab fa-google-plus-g"></a><!--
                    --><a href="#" class="fab fa-facebook-square"></a><!--
                    --><a href="#" class="fab fa-instagram"></a><!--
                    --><a href="#" class="fab fa-youtube"></a>
                </div><!-- /.social -->
            </div><!-- /.left-content -->
        </div><!-- /.thm-container -->
    </div><!-- /.top-bar home-one -->



    <header class="header header-home-one header-home-three">
        <nav class="navbar navbar-default header-navigation stricky">
            <div class="thm-container clearfix">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".main-navigation"
                        aria-expanded="false"><i class="fa fa-bars"></i></button>
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ Storage::url($config->logo ?? ' ') }}" alt="Awesome Image" />
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse main-navigation mainmenu " id="main-nav-bar">

                    <ul class="nav navbar-nav navigation-box">
                        <li class="current">
                            <a href="{{ route('home') }}">{{ __('accueil') }}</a>

                        </li>
                        <li><a href="{{ route('about') }}">
                                {{ \App\Helpers\TranslationHelper::TranslateText('A propos de nous') }}
                            </a></li>




                        <li><a href="{{ route('contact') }}">
                                {{ \App\Helpers\TranslationHelper::TranslateText('Contact') }}
                            </a></li>


                        </li>
                        @guest





                        <li>
                            <a href="{{ url('login') }}">Connexion</a>
                        </li>
                        @else
                        @if (auth()->user()->role != 'client')
                        <li><a href="{{ url('dashboard') }}"
                                class="nav-item nav-link">Dashboard</a>
                        </li>
                        @endif





                        @endguest

                    </ul>
                </div><!-- /.navbar-collapse -->
                <div class="right-side-box">
                    <a href="#" class="book-appointment"><i class="fas fa-calendar-alt"></i>appointment</a><!--
                    --><a href="#test-search" class="search-icon popup-with-zoom-anim search-icon"><i class="fa fa-search"></i></a><!--
                    --><a href="#hidden-sidebar" class="sidemenu-icon side-nav-opener"><i class="fa fa-bars"></i></a>
                </div><!-- /.right-side-box -->
            </div><!-- /.container -->
        </nav>
    </header><!-- /.header -->



    <main>



   @section('body')




    </main>
    @livewire('live-chat')
    <!-- Bouton TikTok -->
    @if(!empty($config->tiktok))
    <div class="tiktok-dark">
        <a href="https://www.tiktok.com/@{{ $config->tiktok ?? '' }}" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-tiktok"></i>
        </a>
    </div>
    @endif

    <!-- Bouton Facebook Messenger -->
    @if(!empty($config->messenger))
    <div class="messenger-dark">
        <a href="https://m.me/{{ $config->messenger ?? '' }}" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-facebook-messenger"></i>
        </a>
    </div>
    @endif

    <!-- Bouton WhatsApp -->
    @if(!empty($config->telephone))
    <div class="whatsapp-dark">
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $config->telephone) }}" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
    @endif

    <style>
        /* Style commun pour les boutons flottants */
        .messenger-dark,
        .whatsapp-dark,
        .tiktok-dark {
            position: fixed;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .messenger-dark a,
        .whatsapp-dark a,
        .tiktok-dark a {
            font-size: 30px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Positionnement et styles spécifiques : TikTok */
        .tiktok-dark {
            bottom: 230px;
            /* Au-dessus de Messenger */
            background-color: #010101;
            border: 2px solid #ffffff;
        }

        .tiktok-dark a {
            color: #ffffff;
        }

        .tiktok-dark:hover {
            background-color: #ffffff;
        }

        .tiktok-dark:hover a {
            color: #010101;
        }

        /* Positionnement et styles spécifiques : Messenger */
        .messenger-dark {
            bottom: 160px;
            /* Au-dessus de WhatsApp */
            background-color: #006AFF;
            border: 2px solid #ffffff;
        }

        .messenger-dark a {
            color: #ffffff;
        }

        .messenger-dark:hover {
            background-color: #ffffff;
        }

        .messenger-dark:hover a {
            color: #006AFF;
        }

        /* Positionnement et styles spécifiques : WhatsApp */
        .whatsapp-dark {
            bottom: 90px;
            background-color: #202c33;
            border: 2px solid #25D366;
        }

        .whatsapp-dark a {
            color: #25D366;
        }

        .whatsapp-dark:hover {
            background-color: #25D366;
        }

        .whatsapp-dark:hover a {
            color: #ffffff;
        }
    </style>

    <style>
        /* Bouton d'ouverture */
        .chat-trigger {
            position: fixed;
            right: 20px;
            bottom: 20px;
            /* Aligné tout en bas */
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #ff9800;
            /* Couleur orange dynamique */
            border: 2px solid #ffffff;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s ease, background-color 0.3s ease;
        }

        .chat-trigger:hover {
            transform: scale(1.05);
        }

        .chat-trigger.active {
            background-color: #e65100;
            /* Orange plus foncé quand ouvert */
        }

        /* Fenêtre de discussion */
        .chat-window {
            position: fixed;
            right: 20px;
            bottom: 90px;
            /* Juste au-dessus du bouton */
            width: 350px;
            height: 450px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            font-family: Arial, sans-serif;
        }

        /* En-tête */
        .chat-header {
            background-color: #202c33;
            /* Teinte sombre pro */
            color: #ffffff;
            padding: 15px;
        }

        .chat-title {
            font-weight: bold;
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .online-indicator {
            width: 8px;
            height: 8px;
            background-color: #4caf50;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .chat-subtitle {
            font-size: 11px;
            color: #b0bec5;
            margin-top: 2px;
        }

        /* Zone des messages */
        .chat-body {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #f4f7f6;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-empty {
            text-align: center;
            color: #9e9e9e;
            margin-top: auto;
            margin-bottom: auto;
            padding: 20px;
        }

        .chat-empty i {
            font-size: 40px;
            margin-bottom: 10px;
            color: #cfd8dc;
        }

        /* Bulles de message */
        .chat-bubble-container {
            display: flex;
            width: 100%;
        }

        .chat-bubble-container.client-side {
            justify-content: flex-end;
        }

        .chat-bubble-container.admin-side {
            justify-content: flex-start;
        }

        .chat-bubble {
            max-width: 75%;
            padding: 10px 12px;
            border-radius: 14px;
            font-size: 13.5px;
            line-height: 1.4;
            position: relative;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .client-side .chat-bubble {
            background-color: #ff9800;
            color: #ffffff;
            border-bottom-right-radius: 2px;
        }

        .admin-side .chat-bubble {
            background-color: #ffffff;
            color: #333333;
            border-bottom-left-radius: 2px;
            border: 1px solid #e0e0e0;
        }

        .chat-time {
            display: block;
            font-size: 9px;
            text-align: right;
            margin-top: 4px;
            opacity: 0.7;
        }

        /* Champ de saisie tout en bas */
        .chat-footer {
            display: flex;
            border-top: 1px solid #e0e0e0;
            padding: 10px;
            background-color: #ffffff;
        }

        .chat-footer input {
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            padding: 8px 15px;
            outline: none;
            font-size: 13px;
        }

        .chat-footer button {
            background-color: #ff9800;
            color: #ffffff;
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            margin-left: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .chat-footer button:hover {
            background-color: #e65100;
        }

        /* Responsive mobile */
        @media (max-width: 480px) {
            .chat-window {
                width: calc(100% - 40px);
                height: 70%;
                bottom: 85px;
            }
        }
    </style>
    <style>
        /* Couleur du texte, des icônes et des titres */
        .axil-footer-area.footer-style-2,
        .axil-footer-area.footer-style-2 p,
        .axil-footer-area.footer-style-2 h5.widget-title,
        .axil-footer-area.footer-style-2 i,
        .axil-footer-area.footer-style-2 .quick-link li {
            color: #ffffff !important;
        }

        /* Couleur des liens au repos */
        .axil-footer-area.footer-style-2 a {
            color: #ffffff !important;
        }

        /* Effet au survol des liens (opacité légère ou couleur au choix) */
        .axil-footer-area.footer-style-2 a:hover {
            color: #dddddd !important;
        }
    </style>
    <style>
        /* Adaptation des CTA pour une hauteur et largeur minimale de 48px */
        .chat-footer button {
            background-color: #ff9800;
            border: none;
            color: #ffffff;
            width: 48px;
            /* Passé à 48px minimum */
            height: 48px;
            /* Passé à 48px minimum */
            border-radius: 50%;
            margin-left: 8px;
            /* Respecte l'espacement minimum de 8px */
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            /* Ajusté proportionnellement */
            transition: background-color 0.2s;
        }

        /* Espacement et zone tactile pour les champs et liens associés */
        .chat-footer input {
            flex: 1;
            border: 1px solid #ced4da;
            border-radius: 20px;
            padding: 12px 15px;
            /* Augmentation du padding vertical pour conforter la zone tactile */
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            margin-right: 8px;
            /* Assure au moins 8px de distance avec le bouton voisin */
        }
    </style>


    <section class="site-footer home-four" style="background-image: url(images/footer-bg-4-1.jpg);">
        <div class="footer-top">
            <div class="thm-container">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12 ">
                        <div class="footer-widget about-widget">
                             <a class="logo-img" href="{{ route('home') }}">

                            <img  src="{{ Storage::url($config->logofooter ?? ' ') }}" alt="Logo" />
                        </a>
                            <p>  {!! \App\Helpers\TranslationHelper::TranslateText($config->description) !!}</p>
                            <div class="social">
                                <a href="#" class="fab fa-twitter"></a><!--  
                                 --><a href="#" class="fab fa-google-plus-g"></a><!--  
                                 --><a href="#" class="fab fa-facebook-square"></a><!--  
                                 --><a href="#" class="fab fa-instagram"></a><!--  
                                 --><a href="#" class="fab fa-youtube"></a>
                            </div><!-- /.social -->
                        </div><!-- /.footer-widget about-widget -->
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-3 col-sm-6 col-xs-12 ">
                        <div class="footer-widget links-widget departments-widget">
                            <div class="title">
                                <h3>Departments</h3>
                            </div><!-- /.title -->
                            <ul class="links-list">
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Family Care Solutions</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Dentistry & Oral Surgery</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Neurology & Neurosurgery</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> General Prescriptions</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Children Health Center</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Diabetes Center</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Pediatric Clinic</a></li>
                            </ul>
                        </div><!-- /.footer-widget links-widget -->
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-2 col-sm-6 col-xs-12 ">
                        <div class="footer-widget links-widget quick-links-widget">
                            <div class="title">
                                <h3>Quick Links</h3>
                            </div><!-- /.title -->
                            <ul class="links-list">
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> About ClinMedix</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Departments</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Our Surgeons Team</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Book an Appointment</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Contact</a></li>
                                <li><a href="#"><i class="fa fa-angle-double-right"></i> Latest News</a></li>
                            </ul>
                        </div><!-- /.footer-widget links-widget -->
                    </div><!-- /.col-md-4 -->
                    <div class="col-md-3 col-sm-6 col-xs-12 ">
                        <div class="footer-widget contact-widget">
                            <div class="title">
                                <h3>Our Location</h3>
                            </div><!-- /.title -->
                            <p>322 Willis Avenue, Southern Street <br> Lakepool, Newyork 33069</p>
                            <p>Emergency Helpline: 1-258-985-703</p>
                            <p>Mon to Fri : 09:00 am - 18:00 pm <br> Sat : 08:00 am - 16:00 pm</p>
                        </div><!-- /.footer-widget contact-widget -->
                    </div><!-- /.col-md-4 -->
                </div><!-- /.row -->
            </div><!-- /.thm-container -->
        </div><!-- /.footer-top -->
        <div class="footer-bottom text-center">
            <div class="thm-container">
                <p>Copyrights © 2019 ClinMedix Medical Clinic. All Rights Reserved. </p>
            </div><!-- /.thm-container -->
        </div><!-- /.footer-bottom -->
    </section><!-- /.site-footer -->

    <div class="search_area zoom-anim-dialog mfp-hide" id="test-search">
        <div class="search_box_inner">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search for...">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="bitmex-icon-search"></i></button>
                </span>
            </div>
        </div>
    </div>


    <section class="hidden-sidebar side-navigation">
        <a href="#" class="close-button side-navigation-close-btn fa fa-times"></a><!-- /.close-button -->
        <div class="sidebar-content">
            <h3>Clin Medix Medical<br /> & Health <br /> Html Template</h3>
            <p>Lorem ipsum dolor sit amet adipiscing elitn quis ex et mauris vulputate semper Etiam eget lacus dapibs ultricies diam vel sollicitudin.</p>
            <p class="contact-info">Inquiry@clinMedix.com <br /> 2800 256 508</p><!-- /.contact-info -->
            <div class="social">
                <a href="#" class="fab fa-twitter"></a><!--
                --><a href="#" class="fab fa-facebook"></a><!--
                --><a href="#" class="fab fa-youtube"></a><!--
                --><a href="#" class="fab fa-pinterest"></a>
            </div><!-- /.social -->
        </div><!-- /.sidebar-content -->
    </section><!-- /.hidden-sidebar -->

    <div class="scroll-to-top scroll-to-target home-four" data-target="html"><i class="fa fa-angle-up"></i></div>




    <style>
        .btn-bg-primary2 {
            background-color: #5EA13C;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-bg-secondary2 {
            background-color: #EFB121;
            /* Couleur de fond, bleu dans cet exemple */
            color: #ffffff;
            /* Couleur du texte, blanc dans cet exemple */
            border: none;
            padding: 10px 20px;
            /* Optionnel, ajuste la taille */
            border-radius: 5px;
            /* Optionnel, arrondit les coins */
            text-decoration: none;
            /* Supprime le soulignement */
        }
    </style>

    <style>
        /* Pour les listes de liens (comme dans le footer) */
        .footer-widget ul li {
            margin-bottom: 12px;
            /* Espacement vertical */
        }

        .footer-widget ul li a {
            display: inline-block;
            padding: 6px 0;
            /* Agrandit la zone de touche sans déformer le texte */
        }

        /* Pour les groupes de boutons côte à côte */
        .btn-group,
        .action-buttons {
            display: flex;
            gap: 12px;
            /* Espace minimal entre les boutons */
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {

            /* Rendre les CTA principaux full-width sur mobile */
            .btn-mobile-full {
                width: 100%;
                display: block;
                text-align: center;
                margin-bottom: 10px;
            }
        }
    </style>
    <!-- JS
============================================ -->
    <script src="js/jquery.js"></script>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/isotope.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="plugins/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="js/custom.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTPlX-43R1TpcQUyWjFgiSfL_BiGxslZU"></script>
    <!-- google map helper -->
    <script src="js/gmaps.js"></script>
    <script src="js/map-helper.js"></script>


</body>

</html>