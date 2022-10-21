@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Users')
@section('container')
<div class="row g-0">
    <form enctype="multipart/form-data" id="usersForm" name="usersForm" autofill="off" autocomplete="off" method="POST" action="{{ route('profile.save-users') }}">
        @csrf
        <div class="card card-flush">
            <div class="card-header ribbon ribbon-top ribbon-vertical pt-5">
                @if (trim($status_form) == 'ADD')
                <div class="ribbon-label fw-bold bg-primary">
                    <span class="fw-bolder text-white fs-4 p-3">{{ $status_form }}</span>
                    <input id="status_form" type="hidden" name="status_form" value="{{ $status_form }}">
                </div>
                @else
                <div class="ribbon-label fw-bold bg-danger">
                    <span class="fw-bolder text-white fs-4 p-3">{{ $status_form }}</span>
                    <input id="status_form" type="hidden" name="status_form" value="{{ $status_form }}">
                </div>
                @endif
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder mb-2 text-dark">Users</span>
                    <span class="text-muted fw-bold fs-7">Form tambah atau edit data users</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="fv-row mb-7">
                    <label class="d-block fw-bold fs-6 mb-5">Photo</label>
                    <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true" style="background-image: url('{{ asset('assets/images/profile/no-image.png') }}')">
                        @if (isset($photo))
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
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="fv-plugins-icon-container">
                                <label class="form-label required">User Id:</label>
                                <input type="text" name="user_id" class="form-control @if(trim($status_form) != 'ADD') form-control-solid @endif mb-3 mb-lg-0"
                                    placeholder="Entry user id pmo" value="{{ isset($user_id) ? trim($user_id) : old('user_id') }}" required
                                    @if(trim($status_form) != 'ADD') readonly @endif>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="fv-plugins-icon-container">
                                <label class="form-label required">Full Name:</label>
                                <input type="text" name="name" class="form-control mb-3 mb-lg-0" placeholder="Entry nama user"
                                    value="{{ isset($name) ? trim($name) : old('name') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fv-row mb-7 fv-plugins-icon-container">
                    <label class="form-label required">Email:</label>
                    <input type="email" name="email" class="form-control mb-3 mb-lg-0" placeholder="Entry email user"
                        value="{{ isset($email) ? trim($email) : old('email') }}" required>
                </div>
                <div class="fv-row mb-7">
                    <label class="form-label required mb-5">Role:</label>
                    @foreach ($data_role as $list)
                    <div class="d-flex fv-row">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input me-3" name="user_role" type="radio" value="{{ trim($list->role_id) }}" id="{{ trim($list->role_id) }}"
                            @if(old('user_role') != "")
                                @if(old('user_role') == trim($list->role_id))
                                    {{"checked"}}
                                @endif
                            @else
                                @if(isset($user_role) && trim($user_role) == trim($list->role_id))
                                    {{"checked"}}
                                @endif
                            @endif>
                            <label class="form-check-label" for="{{ trim($list->role_id) }}">
                                <div class="fw-bolder text-gray-800">{{ trim($list->role_id) }}</div>
                                <div class="text-gray-600">{{ trim($list->keterangan) }}</div>
                            </label>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-5"></div>
                    @endforeach
                </div>
                <div class="fv-row mb-7 fv-plugins-icon-container">
                    <label class="form-label required">Jabatan:</label>
                    <input type="text" name="jabatan" class="form-control mb-3 mb-lg-0" placeholder="Entry jabatan user"
                        value="{{ isset($jabatan) ? trim($jabatan) : old('jabatan') }}" required>
                </div>
                <div class="fv-row mb-7 fv-plugins-icon-container">
                    <label class="form-label required">Telepon:</label>
                    <input type="text" name="telepon" class="form-control mb-3 mb-lg-0" placeholder="Entry telepon user"
                        value="{{ isset($telepon) ? trim($telepon) : old('telepon') }}" required>
                </div>
                <div class="fv-row mb-7">
                    <label class="form-label required">Company Login:</label>
                    <select id="selectCompany" name="companyid" class="form-select" data-control="select2" data-hide-search="true" required>
                    @foreach ($data_company as $list)
                        <option value="{{ trim($list->companyid) }}"
                            @if(old('companyid') != "")
                                @if(old('companyid') == trim($list->companyid))
                                    {{"selected"}}
                                @endif
                            @else
                                @if(isset($companyid) && trim($companyid) == trim($list->companyid))
                                    {{"selected"}}
                                @endif
                            @endif>{{ trim($list->companyid) }} - {{ trim($list->nama_company) }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="fv-row mb-7 rounded border-gray-300 border-1 border-gray-300 border-dashed px-7 py-3 mb-6">
                    <div class="fv-row mt-4 mb-7 fv-plugins-icon-container">
                        <label class="form-label required">Password:</label>
                        <input type="password" name="password" class="form-control @if(trim($status_form) != 'ADD') form-control-solid @endif mb-3 mb-lg-0" placeholder="Entry password user"
                            @if(trim($status_form) == 'ADD') required @else readonly @endif>
                    </div>
                    <div class="fv-row mb-7 fv-plugins-icon-container">
                        <label class="form-label required">Konfirmasi Password:</label>
                        <input type="password" name="password_confirmation" class="form-control @if(trim($status_form) != 'ADD') form-control-solid @endif mb-3 mb-lg-0" placeholder="Ulangi password user"
                            @if(trim($status_form) == 'ADD') required @else readonly @endif>
                    </div>
                </div>
                <div class="fv-row mb-7">
                    <label class="required fs-6 mb-5">Status User:</label>
                    <div class="d-flex fv-row">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input me-3" name="status_user" type="radio" value="1" id="active"
                            @if(old('status_user') != "")
                                @if(old('status_user') == 1)
                                    {{"checked"}}
                                @endif
                            @else
                                @if(isset($status_user) && trim($status_user) == 1)
                                    {{"checked"}}
                                @endif
                            @endif>
                            <label class="form-check-label" for="active">
                                <div class="fw-bolder text-gray-600">Aktif</div>
                            </label>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-5"></div>
                    <div class="d-flex fv-row">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input me-3" name="status_user" type="radio" value="0" id="non_active"
                            @if(old('status_user') != "")
                                @if(old('status_user') == 0)
                                    {{"checked"}}
                                @endif
                            @else
                                @if(isset($status_user) && trim($status_user) == 0)
                                    {{"checked"}}
                                @endif
                            @endif>
                            <label class="form-check-label" for="non_active">
                                <div class="fw-bolder text-gray-600">Non-AKtif</div>
                            </label>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-5"></div>
                </div>
                <button type="submit" class="btn btn-primary" id="btnSimpan" name="btnSimpan">Simpan</button>
                <a href="{{ route('profile.users') }}" role="button" class="btn btn-danger">Close</a>
            </div>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

</script>
@endpush
