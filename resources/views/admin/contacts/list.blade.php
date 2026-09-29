@section('titre', 'Liste des contact')
@extends('admin.fixe')

@section('body')


    <div class="page-content-wrapper">
        <div class="page-content">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box">

                    </div>
                </div>
            </div>

            <!-- end page title -->
            <div class="card radius-15">
                <div class="card-body">

                    <div class="card-title">
                        <div class="d-flex justify-content-between">
                            <h5 class="header-title">
                                Liste des contacts
                            </h5>

                        </div>
                    </div>
                    <hr>

                    <div>

                     

                        @include('components.alert')

                        <div class="table-responsive-sm">
                            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                <thead class="table-dark cusor">
                                    <tr>

                                        <th>Nom </th>
                                        <th>Numéro de téléphone</th>
                                        <th>E-Mail</th>
                                        <th>Sujet</th>
                                        <th> Date</th>
                                        <th style="text-align: right;">
                                            <span wire:loading>
                                                <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20"
                                                    class="rounded shadow" alt="">
                                            </span>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($contacts as $cat)
                                        <tr>
                                            <td>
                                                {{ $cat->nom }}
                                                @include('admin.contacts.Modal-message', ['cat' => $cat])
                                            </td>

                                            <td>
                                                {{ $cat->telephone ?? '' }}
                                            </td>

                                            <td>
                                                {{ $cat->email }}
                                            </td>

                                            <td>
                                                {{ $cat->sujet }}
                                            </td>
                                            <td>{{ $cat->created_at->format('d/m/Y') }} </td>
                                            <td style="text-align: right;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-dark"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#message-{{ $cat->id }}">
                                                        <i class='bx bx-message-dots'></i> Voir
                                                    </button>
                                                    @can('category_delete')
                                                        <a href="/admin/supprimer_messages/{{ $cat->id }}"
                                                            class="btn btn-sm btn-danger">
                                                            <i class='bx bx-comment-x' ></i>
                                                            Supprimer
                                                        </a>
                                                    @endcan
                                                </div>

                                               
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-5 text-center">
                                                <img width="100" height="100"
                                                    src="https://img.icons8.com/dotty/100/578b07/contact-card.png"
                                                    alt="contact-card" />
                                                <br>
                                                <h6>
                                                    Aucun contact trouvé.
                                                </h6>
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>


                            </table>
                        </div>
                        {{ $contacts->links('pagination::bootstrap-4') }}



                    </div>


                </div>
            </div>


        </div>
    </div>


    
@endsection
