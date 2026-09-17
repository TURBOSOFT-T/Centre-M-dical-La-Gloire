@extends('front.fixe')
@section('titre', 'Création compte')
@section('body')

    <main>
        @php
    $config = DB::table('configs')->first();
    $service = DB::table('services')->get();
  
@endphp




  <body>
    <br><br>
    
    <!-- Content -->

   @livewire('register')

 
  </body>



    </main>
@endsection
