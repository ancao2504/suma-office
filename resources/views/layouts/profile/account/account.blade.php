@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','My Account')
@section('container')
<div class="row g-0">
    <form enctype="multipart/form-data" id="usersForm" name="usersForm" autofill="off" autocomplete="off" method="POST" action="{{ route('profile.save-account-profile') }}">
        @csrf
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Account</span>
                    <span class="text-muted fw-bold fs-7">Account login PMO Suma Honda</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="fv-row mb-7">
                    <label class="d-block fw-bold fs-6 mb-5">Photo</label>
                    <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true" style="background-image: url('{{ asset('assets/images/profile/no-image.png') }}')">
                        @if(isset($photo))
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url('{{ $photo }}');"></div>
                        @else
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: none;"></div>
                        @endif
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="" data-bs-original-title="Change avatar">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="photo" accept=".png, .jpg, .jpeg">
                            <input type="hidden" name="photo_remove" value="1">
                        </label>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="" data-bs-original-title="Cancel avatar">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Remove avatar">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>
                    <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                </div>

                <div class="fv-row mb-7">
                    <label class="form-label required">User Id:</label>
                    <input type="text" name="user_id" class="form-control form-control-solid mb-3 mb-lg-0"
                        placeholder="Entry user id pmo" value="{{ isset($user_id) ? trim($user_id) : old('user_id') }}" readonly required>
                </div>
                <div class="fv-row mb-7">
                    <label class="form-label required">Full Name:</label>
                    <input type="text" name="name" class="form-control mb-3 mb-lg-0" placeholder="Entry nama user"
                        value="{{ isset($name) ? trim($name) : old('name') }}" required>
                </div>
                <div class="fv-row mb-7 fv-plugins-icon-container">
                    <label class="form-label required">Email:</label>
                    <input type="email" name="email" class="form-control mb-3 mb-lg-0" placeholder="Entry email user"
                        value="{{ isset($email) ? trim($email) : old('email') }}" required>
                </div>
                <div class="fv-row mb-7 fv-plugins-icon-container">
                    <label class="form-label required">Telepon:</label>
                    <input type="text" name="telepon" class="form-control mb-3 mb-lg-0" placeholder="Entry telepon user"
                        value="{{ isset($telepon) ? trim($telepon) : old('telepon') }}" required>
                </div>
                <button type="submit" class="btn btn-primary" id="btnSimpan" name="btnSimpan">Simpan</button>
                <a href="{{ route('profile.account-profile') }}" role="button" class="btn btn-danger">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

</script>
@endpush
