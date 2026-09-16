@section('titre', 'Liste des produits')
@extends('admin.fixe')

@section('body')
    <!--page-content-wrapper-->
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
                                    <a href="{{ route('testimonials') }}">Les témognages</a>
                                </li>
                                <li class="breadcrumb-item active">Liste</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->


            <div class="card radius-15">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card-title">
                                <h5 class="mb-0 my-auto">
                                    Modifier  le témoignage
                                </h5>
                            </div>
                        </div>
                     
                    </div>
                    <hr />
                    <div class="container">
                      
                    
                        <form action="{{ route('testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                    
                            <div class="form-group">
                                <label for="photo">Photo actuelle</label><br>
                                <img src="{{ asset('uploads/testimonials/' . $testimonial->photo) }}" alt="Photo Témoignage" width="100" height="100">
                            </div>
                    
                            <div class="form-group">
                                <label for="photo">Télécharger une nouvelle photo</label>
                                <input type="file" name="photo" class="form-control" id="photo">
                            </div>
                    
                            <div class="form-group">
                                <label for="message">Message du témoignage</label>
                                <textarea name="message" class="form-control" id="message">{{ $testimonial->message }}</textarea>
                            </div>
                    
                            <button type="submit" class="btn btn-primary">Sauvegarder</button>
                            <a href="{{ route('testimonials') }}" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
             

                </div>
            </div>
        </div>
    </div>
<!-- Modal Modifier -->




@endsection
