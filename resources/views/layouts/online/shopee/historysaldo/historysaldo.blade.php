@extends('layouts.main.index')
@section('title','Tokopedia')
@section('subtitle','History Saldo')
@section('container')
<div class="row g-0">
    <div class="card card-flush shadow mb-6">
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="fw-bolder mb-2 text-dark">History Saldo</span>
                <span class="text-muted fw-bold fs-7">Daftar history saldo marketplace shopee</span>
            </h3>
            <div class="card-toolbar">
                <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px" />
            </div>
        </div>
        <div class="card-header">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
                <li class="nav-item">
                    <a class="nav-link text-active-primary ms-0 me-10 @if($data_filter->view == 'GROUP') active @endif" href="{{ route('online.historysaldo.shopee.daftar-group') }}">Saldo Saya</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary ms-0 me-10 @if($data_filter->view == 'DETAIL') active @endif" href="{{ route('online.historysaldo.shopee.daftar-detail') }}">Detail Saldo</a>
                </li>
            </ul>
        </div>
    </div>
</div>

@yield('containerhistorysaldo')

@endsection

