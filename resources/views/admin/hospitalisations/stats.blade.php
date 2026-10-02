@section('titre', 'Statistiques, Stocks et Rapports')
@extends('admin.fixe')

@section('body')
<div class="page-content-wrapper">
    <!-- 🔹 Zone globale de contenu -->
    <div class="page-content" id="printable-section">
<div class="container-fluid px-4 py-4">
    {{-- Appel du composant Livewire des statistiques --}}
    @livewire('hospitalisation-stats')
</div>
    </div>
</div>
@endsection