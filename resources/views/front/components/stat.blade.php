<!-- Start About Area  -->
<style>
    .image-center {
        display: flex;
        justify-content: center;
    }

    .text-justify {
        text-align: justify;
        text-justify: inter-word;
        white-space: normal;
        word-spacing: -1px;
    }
    
    .forced-icon {
        width: 100px;
        height: 100px;
        object-fit: contain; /* Conserve les proportions de l'icône dans le cadre de 100x100 */
    }
</style>

<div class="about-info-area">
    <div class="container">
        <div class="row row--20">
            <!-- Box 1: Satisfaction -->
            <div class="col-lg-4">
                <div class="about-info-box">
                    <div class="thumb image-center">
                        <img src="{{ Storage::url($config->icone_satisfaction ?? '') }}" class="forced-icon" alt="Icone Satisfaction">
                    </div>
                    <div class="content">
                        <h5 class="title text-justify">
                            {!! \App\Helpers\TranslationHelper::TranslateText($config->titre_satisfaction ?? ' ') !!}
                        </h5>
                        <p class="text-justify">
                            {!! \App\Helpers\TranslationHelper::TranslateText($config->des_satisfaction ?? '') !!}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Box 2: Année -->
            <div class="col-lg-4">
                <div class="about-info-box">
                    <div class="thumb image-center">
                        <img src="{{ Storage::url($config->icone_annee ?? ' ') }}" class="forced-icon" alt="Icone Année">
                    </div>
                    <div class="content">
                        <h5 class="title text-justify">
                            {!! \App\Helpers\TranslationHelper::TranslateText($config->titre_annee ?? '') !!}
                        </h5>
                        <p class="text-justify">
                            {!! \App\Helpers\TranslationHelper::TranslateText($config->des_annee ?? ' ') !!}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Box 3: Prix -->
            <div class="col-lg-4">
                <div class="about-info-box">
                    <div class="thumb image-center">
                        <img src="{{ Storage::url($config->icone_prix ?? ' ') }}" class="forced-icon" alt="Icone Prix">
                    </div>
                    <div class="content">
                        <h5 class="title text-justify">
                            {!! \App\Helpers\TranslationHelper::TranslateText($config->titre_prix ?? ' ') !!}
                        </h5>
                        <p class="text-justify">
                            {!! \App\Helpers\TranslationHelper::TranslateText($config->des_prix ?? '') !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>