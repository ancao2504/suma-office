@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Account')
@section('container')
    <div class="card card-flush shadow mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                        <img src="{{ Session::get('app_user_photo') }}" alt="image">
                        {{-- <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div> --}}
                    </div>
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-900 text-hover-primary fs-2 fw-bolder me-1">{{ Session::get('app_user_name') }}</span>
                                <span class="svg-icon svg-icon-1 svg-icon-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                        <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                                        <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                                    </svg>
                                </span>
                            </div>

                            <div class="d-flex flex-wrap fw-bold fs-6 mb-1 pe-2">
                                <div class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z" fill="currentColor"></path>
                                            <path d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z" fill="currentColor"></path>
                                        </svg>
                                    </span>{{ Session::get('app_user_id') }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 mb-1 pe-2">
                                <div class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="currentColor"/>
                                            <path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="currentColor"/>
                                        </svg>
                                    </span>{{ Session::get('app_user_role_id') }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 mb-1 pe-2">
                                <div class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z" fill="currentColor"/>
                                            <path opacity="0.3" d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z" fill="currentColor"/>
                                        </svg>
                                    </span>{{ Session::get('app_user_telepon') }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 mb-1 pe-2">
                                <div class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-5">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="currentColor"></path>
                                            <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="currentColor"></path>
                                        </svg>
                                    </span>{{ Session::get('app_user_email') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Profile Dealer</h3>
            </div>
        </div>

        <div id="kt_account_settings_profile_details" class="collapse show">
            <form id="kt_account_profile_details_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                <div class="card-body border-top p-9">
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Kode Dealer</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $kode_dealer }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Dealer</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $nama_dealer }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Salesman</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $kode_sales }}" readonly>
                                </div>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="lname" class="form-control form-control-lg form-control-solid" value="{{ $nama_sales }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Area</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $kode_area }}" readonly>
                                </div>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="lname" class="form-control form-control-lg form-control-solid" value="{{ $nama_area }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator my-10"></div>
                    <div class="row mb-6">
                        @if ($keterangan_limit == 'Limit Piutang')
                        <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4">
                            <span class="svg-icon svg-icon-2tx svg-icon-success me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                                </svg>
                            </span>
                            <div class="d-flex flex-grow-1">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">{{ $keterangan_limit }}</h4>
                                    <div class="fs-4 text-gray-700">{{ number_format($sisa_limit) }}</div>
                                </div>
                            </div>
                        </div>
                        @elseif ($keterangan_limit == 'Limit Sales')
                        <div class="notice d-flex bg-light-warning rounded border-success border border-dashed p-4">
                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                                </svg>
                            </span>
                            <div class="d-flex flex-grow-1">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">{{ $keterangan_limit }}</h4>
                                    <div class="fs-4 text-gray-700">{{ number_format($sisa_limit) }}</div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4">
                            <span class="svg-icon svg-icon-2tx svg-icon-danger me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                                </svg>

                            </span>
                            <div class="d-flex mt-2">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">{{ $keterangan_limit }}</h4>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="separator my-10"></div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">NIK & NPWP</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $ktp }}" readonly>
                                </div>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="lname" class="form-control form-control-lg form-control-solid" value="{{ $npwp }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $alamat }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Kabupaten</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $kabupaten }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Karesidenan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $karesidenan }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Kota</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $kota }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Telepon</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $telepon }}" readonly>
                        </div>
                    </div>
                    <div class="separator my-10"></div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Surat Jalan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $nama_dealer_sj }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat Surat Jalan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $alamat_dealer_sj }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Kota Surat Jalan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $kota_dealer_sj }}" readonly>
                        </div>
                    </div>
                    {{-- <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Lokasi Dealer</label>
                        <div class="col-md-6 ps-lg-10">
                            <iframe width="100%" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=40.7127837,-74.0059413&amp;key=AIzaSyBqUHdD9RniI_lnMPRQ5QhuA6e7ElKxd1c"></iframe>
                        </div>
                    </div> --}}
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">

        </script>
    @endpush
@endsection
