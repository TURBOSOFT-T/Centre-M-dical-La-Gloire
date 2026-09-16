@section('titre', 'Liste des images')
@extends('admin.fixe')

@section('body')





    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">










            <div class="content-wrapper">


                <div class="container-xxl flex-grow-1 container-p-y">



                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">
                                                <a href="javascript: void(0);">{{ config('app.name') }}</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{ route('testimonials') }}">Témoignages</a>
                                            </li>
                                            <li class="breadcrumb-item active">Liste</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-6 mb-6">

                        </div>
                        <!-- Sponsors List Table -->

                        <div class="card radius-15">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="card-title">
                                        <h5 class="mb-0 my-auto">
                                            Liste des témoignages
                                        </h5>
                                    </div>

                                </div>
                                <hr />
                                @include('components.alert')

                                <div class="table-responsive-sm">
                                    <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead class="table-dark cusor">
                                            <tr>

                                                <th>Nom </th>


                                                <th>Message</th>
                                                <th> Date</th>
                                                <th style="text-align: right;">
                                                    <span wire:loading>
                                                        <img src="https://i.gifer.com/ZKZg.gif" width="20"
                                                            height="20" class="rounded shadow" alt="">
                                                    </span>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($testimonials as $cat)
                                                <tr>
                                                    <td>
                                                        {{ $cat->name }}
                                                        {{--    @include('admin.testimonials.Modal-message', ['cat' => $cat]) --}}
                                                    </td>
                                                    <td>
                                                        {{ $cat->message }}

                                                    </td>





                                                    <td>{{ $cat->created_at->format('d/m/Y') }} </td>
                                                    <td style="text-align: right;">
                                                        <div class="btn-group">

                                                             <form action="{{ route('testimonials.destroy',$cat->id) }}" method="POST">
                                                                @csrf
                                                                <input name="_method" type="hidden" value="DELETE">
                                                                <button type="submit" class="btn btn-xs btn-danger btn-flat show_confirm" data-toggle="tooltip"
                                                                    title='Delete'><svg xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 24 24" width="20"
                                                                    style="background-color: #e0610d22; fill:#dbd7d7;"
                                                                    height="22" fill="currentColor">
                                                                    <path
                                                                        d="M6.45455 19L2 22.5V4C2 3.44772 2.44772 3 3 3H21C21.5523 3 22 3.44772 22 4V18C22 18.5523 21.5523 19 21 19H6.45455ZM13.4142 11L15.8891 8.52513L14.4749 7.11091L12 9.58579L9.52513 7.11091L8.11091 8.52513L10.5858 11L8.11091 13.4749L9.52513 14.8891L12 12.4142L14.4749 14.8891L15.8891 13.4749L13.4142 11Z">
                                                                    </path>
                                                                </svg></button>
                                            
                                                                </form> 
                                                    {{--         <button class="btn btn-danger btn-sm"
                                                                onclick="confirmDelete('{{ route('testimonials.destroy', $cat->id) }}')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button> --}}

                                                        </div>
                                                        @if ($cat->active)
                                                            <button class="btn btn-warning btn-sm"
                                                                onclick="confirmAction('{{ route('temoignages.disapprove', $cat->id) }}', 'Disapprove', 'Really disapprove this testimonial?')">
                                                                <i class="fas fa-times"></i> Disapprove
                                                            </button>
                                                        @else
                                                            <button class="btn btn-success btn-sm"
                                                                onclick="confirmAction('{{ route('temoignages.approve', $cat->id) }}', 'Approve', 'Really approve this testimonial?')">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        @endif


                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="p-5 text-center">
                                                        <img width="100" height="100"
                                                            src="https://img.icons8.com/dotty/100/578b07/testimonial-card.png"
                                                            alt="testimonial-card" />
                                                        <br>
                                                        <h6>
                                                            Aucun témoignage trouvé.
                                                        </h6>
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>


                                    </table>
                                </div>
                                {{ $testimonials->links('pagination::bootstrap-4') }}



                            </div>
                        </div>


                    </div>



                </div>

            </div>




        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>



        function confirmAction(url, action, message) {
            Swal.fire({
                title: action,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, ' + action,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        function confirmDelete(url) {
            Swal.fire({
                title: 'Delete',
                text: 'Are you sure you want to delete this testimonial?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
 <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
 <script type="text/javascript">
     $('.show_confirm').click(function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault();
         swal({
                 title: `Are you sure you want to delete this record?`,
                 text: "If you delete this, it will be gone forever.",
                 icon: "warning",
                 buttons: true,
                 dangerMode: true,
             })
             .then((willDelete) => {
                 if (willDelete) {
                     form.submit();
                 }
             });
     });
 </script>

@endsection
