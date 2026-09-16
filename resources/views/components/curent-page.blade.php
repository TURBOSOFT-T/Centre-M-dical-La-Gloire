<div class="bg-body-secondary py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-center">
            <li class="breadcrumb-item">
                <a class="text-decoration-none text-body" href="{{ route('home') }}">
                    Accueil
                </a>
            </li>
            <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">
                {{ $page }}
            </li>
        </ol>
    </nav>
</div>