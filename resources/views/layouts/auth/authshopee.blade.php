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
                    <input type="text" id="inputCodeShopee" name="code" class="form-control" placeholder="Code"
                        value="@if(isset($code)){{ $code }}@else {{ $code }} @endif">
                    <button id="btnGenerateCodeShopee" name="btnGenerateCodeShopee" class="btn btn-primary" type="button">Generate Code</button>
                </div>
            </div>
            <div class="fv-row mt-8">
                <label class="form-label required">Access Token:</label>
                <input type="text" id="inputAccessToken" name="access_token" class="form-control" placeholder="Code"
                    value="@if(isset($access_token)){{ $access_token }}@else {{ $access_token }} @endif">
            </div>
            <div class="fv-row mt-8">
                <label class="form-label required">Refresh Token:</label>
                <input type="text" id="inputRefreshToken" name="refresh_token" class="form-control" placeholder="Code"
                    value="@if(isset($refresh_token)){{ $refresh_token }}@else {{ $refresh_token }} @endif">
            </div>
        </div>
    </div>
</div>
@endsection
