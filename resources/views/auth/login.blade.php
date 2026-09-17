 @extends('front.fixe')  



@section('titre', 'Connexion')
@section('body')

    <main>
        @php
            $config = DB::table('configs')->first();
            $service = DB::table('services')->get();
          
        @endphp

      <!-- Content -->

   
  
      
        <body>
       
<br>
<br>
      
        @livewire('connexion')
      
        

   
        </body>
      </html>
   
    </main>
@endsection