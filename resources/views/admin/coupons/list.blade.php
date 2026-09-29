@section('titre', 'Liste des coupons')
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
                                <a href="{{ route('coupons') }}">Les coupon</a>
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
                                Liste des coupons
                            </h5>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-group mb-3 justify-content-end">
                            @can('coupon_add')
                            <button class="btn btn-primary btn-sm  px-5" style="float: right;" onclick="url('{{ route('coupon.add') }}')">
                                Ajouter un coupon
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <hr />

                <div class="card-body">
                    <div class="table-responsive">
                        @if(count($coupons)>0)
                        <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>S.N.</th>
                                    <th>Coupon Code</th>
                                    <th>Commercial</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Expiration</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>S.N.</th>
                                    <th>Coupon Code</th>
                                    <th>Commercial</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Expiration</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($coupons as $coupon)
                                <tr>
                                    <td>{{$coupon->id}}</td>
                                    <td>{{$coupon->code}}</td>
                                    <td>{{ $coupon->commercial ? $coupon->commercial->nom : 'N/A' }} {{ $coupon->commercial ? $coupon->commercial->prenom : 'N/A' }}</td>

                                    <td>
                                        @if($coupon->type=='fixed')
                                        <span class="btn btn-sm btn-primary">{{$coupon->type}}</span>
                                        @else
                                        <span class="btn btn-sm btn-warning">{{$coupon->type}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->type=='fixed')
                                        {{number_format($coupon->value,2)}} <x-devise></x-devise>
                                        @else
                                        {{$coupon->value}}%
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->status=='active')
                                        <span class="btn btn-sm btn-primary">{{$coupon->status}}</span>
                                        @else
                                        <span class="btn btn-sm btn-warning">{{$coupon->status}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('d/m/Y') : 'Illimitée' }}
                                    </td>
                                    <td>@can('coupon_edit')
                                        <a href="{{route('updatecoupon',$coupon->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                        @endcan
                                        @can('coupon_delete')
                                        <form method="POST" action="{{route('coupon.destroy',[$coupon->id])}}">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm dltBtn" data-id={{$coupon->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                        @endcan
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <span style="float:right">{{$coupons->links()}}</span>
                        @else
                        <h6 class="text-center">No Coupon found!!! Please create coupon</h6>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection