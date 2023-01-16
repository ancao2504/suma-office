@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Change Password')
@section('container')
<div class="row g-0">
    <form enctype="multipart/form-data" id="usersForm" name="usersForm" autofill="off" autocomplete="off" method="POST" action="{{ route('profile.account.change-password') }}">
        @csrf
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Change Password</span>
                    <span class="text-muted fw-bold fs-7">Change password login PMO Suma Honda</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-10 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                    <label class="form-label mb-3 required">Password Lama</label>
                    <input type="password" class="form-control form-control-lg" name="old_password" placeholder="" value="" required>
                </div>
                <div class="mb-10 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                    <label class="form-label mb-3 required">Password Baru</label>
                    <input type="password" class="form-control form-control-lg" name="password" placeholder="" value="" required>
                </div>
                <div class="mb-10 fv-row fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                    <label class="form-label mb-3 required">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control form-control-lg" name="password_confirmation" placeholder="" value="" required>
                </div>
                <button type="submit" class="btn btn-primary" id="btnSimpan" name="btnSimpan">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

</script>
@endpush
