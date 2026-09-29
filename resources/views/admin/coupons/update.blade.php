@section('titre', 'Mise à jour')
@extends('admin.fixe')

@section('body')
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
                                    <a href="{{ route('coupons') }}">Coupons</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    {{ $coupon->code }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="header-title">
                                Modification du coupon
                            </h5>
                        </div>
                        <div class="card-body">

                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="{{ route('coupons.update', $coupon->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <div class="row">

                                            <div class="col-sm-6 form-group mb-3">
                                                <label for="inputTitle" class="col-form-label font-weight-bold">Coupon Code <span class="text-danger">*</span></label>
                                                <input id="inputTitle" type="text" name="code" maxlength="4"
                                                    placeholder="Enter Coupon Code" value="{{ old('code', $coupon->code) }}"
                                                    class="form-control @error('code') is-invalid @enderror">
                                                @error('code')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-sm-6 form-group mb-3">
                                                <label for="type" class="col-form-label font-weight-bold">Type <span class="text-danger">*</span></label>
                                                <select name="type" class="form-control">
                                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                    <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>Percent</option>
                                                </select>
                                                @error('type')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Champ Booléen : Choix d'associer à un commercial -->
                                            <div class="col-sm-12 form-group mb-3">
                                                <div class="form-check form-switch mt-2">
                                                    <input type="hidden" name="is_commercial" value="0">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="is_commercial" 
                                                           name="is_commercial" 
                                                           value="1" 
                                                           {{ old('is_commercial', $coupon->is_commercial) ? 'checked' : '' }}>
                                                    <label class="form-check-label font-weight-bold" for="is_commercial">
                                                        Associer ce code promo à un commercial
                                                    </label>
                                                </div>
                                                @error('is_commercial')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Bloc Sélection du Commercial (Affiché conditionnellement) -->
                                            <div class="col-sm-12 form-group mb-3" id="commercial_select_group" style="display: none;">
                                                <label for="commercial_id" class="form-label font-weight-bold">Commercial <span class="text-danger">*</span></label>
                                                <select name="commercial_id" id="commercial_id" class="form-control @error('commercial_id') is-invalid @enderror">
                                                    <option value="">-- Sélectionnez un commercial --</option>
                                                    @foreach($commercials as $commercial)
                                                        <option value="{{ $commercial->id }}" {{ old('commercial_id', $coupon->commercial_id) == $commercial->id ? 'selected' : '' }}>
                                                            {{ $commercial->nom }} {{ $commercial->prenom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('commercial_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-sm-6 form-group mb-3">
                                                <label for="inputValue" class="col-form-label font-weight-bold">Value <span class="text-danger">*</span></label>
                                                <input id="inputValue" type="number" step="0.01" name="value"
                                                    placeholder="Enter Coupon value" value="{{ old('value', $coupon->value) }}"
                                                    class="form-control @error('value') is-invalid @enderror">
                                                @error('value')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-sm-6 form-group mb-3">
                                                <label for="status" class="col-form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                                                <select name="status" class="form-control">
                                                    <option value="active" {{ old('status', $coupon->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status', $coupon->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Champ Date d'expiration -->
                                            <div class="col-sm-6 form-group mb-3">
                                                <label for="expires_at" class="col-form-label font-weight-bold">Date d'expiration</label>
                                                <input id="expires_at" type="date" name="expires_at" 
                                                    value="{{ old('expires_at', isset($coupon->expires_at) ? \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d') : '') }}" 
                                                    class="form-control @error('expires_at') is-invalid @enderror">
                                                @error('expires_at')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                        </div>
                                        <br>
                                        <div class="form-group mb-3">
                                            <button class="btn btn-success" type="submit">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
            </div> <!-- end row-->
        </div> <!-- container -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxCommercial = document.getElementById('is_commercial');
            const groupSelectCommercial = document.getElementById('commercial_select_group');
            const selectCommercial = document.getElementById('commercial_id');

            // Fonction pour basculer la visibilité du sélecteur
            function toggleCommercialSelect() {
                if (checkboxCommercial.checked) {
                    groupSelectCommercial.style.display = 'block';
                } else {
                    groupSelectCommercial.style.display = 'none';
                    selectCommercial.value = ''; // Réinitialise la sélection si masqué
                }
            }

            // Écouteur sur le changement de la checkbox
            checkboxCommercial.addEventListener('change', toggleCommercialSelect);

            // Exécution initiale pour charger l'état existant du coupon modifié
            toggleCommercialSelect();
        });
    </script>
@endsection