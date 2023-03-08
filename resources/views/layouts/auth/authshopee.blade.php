@extends('layouts.main.index')
@section('title','Orders')
@section('subtitle','Authentication Shopee')
@section('container')
<div class="row g-0">
    <div class="card card-flush">
        <div class="card-header align-items-center border-0 mt-4 mb-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">Shopee</span>
                <span class="text-muted fw-bold fs-7">Code authentication marketplace shopee</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-body">
            <div class="fv-row">
                <label class="form-label required">Code:</label>
                <div class="input-group">
                    <input type="text" id="inputAccessCode" name="access_code" class="form-control" placeholder="Code"
                        value="@if(isset($code)){{ $code }}@else {{ $code }} @endif">
                    <button id="btnGenerateLinkAuth" name="btnGenerateLinkAuth" class="btn btn-primary" type="button">Generate Code</button>
                </div>
            </div>
            <div class="fv-row mt-8">
                <label class="form-label required">Access Token:</label>
                <input type="text" id="inputAccessToken" name="access_token" class="form-control form-control-solid" placeholder="Access Token" readonly
                    value="@if(isset($access_token)){{ $access_token }}@else {{ $access_token }} @endif">
            </div>
            <div class="fv-row mt-8">
                <label class="form-label required">Refresh Token:</label>
                <input type="text" id="inputRefreshToken" name="refresh_token" class="form-control form-control-solid" placeholder="Refresh Token" readonly
                    value="@if(isset($refresh_token)){{ $refresh_token }}@else {{ $refresh_token }} @endif">
            </div>
            <div class="fv-row mt-8">
                <button id="btnGenerateAccessToken" class="btn btn-success">Generate Access Token</button>
            </div>
            <div class="separator my-10"></div>
            <div class="fv-row mt-8">
                <label class="form-label required">Date Process:</label>
                <input type="text" id="inputDateProcess" name="date_process" class="form-control form-control-solid" placeholder="Date Process" readonly
                    value="@if(isset($date_process)){{ $date_process }}@else {{ $date_process }} @endif">
            </div>
            <div class="fv-row mt-8">
                <label class="form-label required">User Process:</label>
                <input type="text" id="inputUserProcess" name="user_process" class="form-control form-control-solid" placeholder="User Process" readonly
                    value="@if(isset($user_process)){{ $user_process }}@else {{ $user_process }} @endif">
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        const url = {
            'generate_link_authorization': "{{ route('online.auth.shopee.generate-link') }}",
            'simpan_access_code': "{{ route('online.auth.shopee.simpan') }}",
        }
    </script>
    <script src="{{ asset('assets/js/suma/auth/shopee.js') }}?v={{ time() }}"></script>
@endpush
@endsection
