@extends('layouts.main.index')
@section('title','Access')
@section('subtitle','Disable Access')
@section('container')
    <div class="row g-0">
        <form action="#" method="get">
            <div class="card card-flush">
                <div class="card-body">
                    <div class="d-flex flex-column flex-column-fluid text-center p-10 py-lg-15">
                        <a href="{{ route('dashboard.dashboard') }}" class="mb-10 pt-lg-10">
                            <img alt="Logo" src="{{ asset('assets/images/logo/suma_login.png') }}" class="h-40px mb-5">
                        </a>
                        <div class="pt-lg-10 mb-10">
                            <h1 class="fw-bolder fs-2qx text-gray-800 mb-10">Access Not Allowed</h1>
                            <div class="fw-bold fs-3 text-muted mb-15">Akses tidak di ijinkan!
                                <br>Akses user anda tidak di ijinkan
                                <br>untuk membukan menu ini
                            </div>
                            <div class="text-center">
                                <a href="{{ route('dashboard.dashboard') }}" class="btn btn-lg btn-danger fw-bolder">Go to homepage</a>
                            </div>
                        </div>
                        <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-350px"
                            style="background-image: url({{ asset('assets/images/background/background_not_allowed.png') }})">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @push('scripts')
        <script type="text/javascript">

        </script>
    @endpush
@endsection
