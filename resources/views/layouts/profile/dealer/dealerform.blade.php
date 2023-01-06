@extends('layouts.main.index')
@section('title','Profile')
@section('subtitle','Dealer')
@section('container')
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-80px symbol-fixed position-relative">
                        @if ($data->status_limit == 'LIMIT_PIUTANG')
                        <div class="symbol symbol-40px me-2">
                            <div class="symbol-label fs-2 fw-bold bg-light-success text-success">{{ trim($data->kode_dealer) }}</div>
                        </div>
                        @elseif ($data->status_limit == 'LIMIT_SALES')
                        <div class="symbol symbol-40px me-2">
                            <div class="symbol-label fs-2 fw-bold bg-light-warning text-warning">{{ trim($data->kode_dealer) }}</div>
                        </div>
                        @else
                        <div class="symbol symbol-40px me-2">
                            <div class="symbol-label fs-2 fw-bold bg-light-danger text-danger">{{ trim($data->kode_dealer) }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-900 fs-2 fw-bolder me-1">{{ $data->kode_dealer }}</span>
                                @if ($data->status_limit == 'LIMIT_PIUTANG')
                                <span class="svg-icon svg-icon-1 svg-icon-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                        <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                                        <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                                    </svg>
                                </span>
                                @elseif ($data->status_limit == 'LIMIT_SALES')
                                <span class="svg-icon svg-icon-1 svg-icon-warning pb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                                        <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"/>
                                        <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"/>
                                    </svg>
                                </span>
                                @else
                                <span class="svg-icon svg-icon-1 svg-icon-danger pb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                                        <rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="currentColor"/>
                                        <rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="currentColor"/>
                                    </svg>
                                </span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 pe-2 mb-1">
                                <div class="d-flex align-items-center fs-7 fw-bolder text-gray-400 me-5">
                                    <span class="svg-icon svg-icon-1 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z" fill="currentColor"></path>
                                            <path d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z" fill="currentColor"></path>
                                        </svg>
                                    </span>{{ $data->nama_dealer }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 pe-2 mb-1">
                                <div class="d-flex align-items-center fs-7 fw-bolder text-gray-400 me-5">
                                    <span class="svg-icon svg-icon-1 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21 11H18.9C18.5 7.9 16 5.49998 13 5.09998V3C13 2.4 12.6 2 12 2C11.4 2 11 2.4 11 3V5.09998C7.9 5.49998 5.50001 8 5.10001 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H5.10001C5.50001 16.1 8 18.4999 11 18.8999V21C11 21.6 11.4 22 12 22C12.6 22 13 21.6 13 21V18.8999C16.1 18.4999 18.5 16 18.9 13H21C21.6 13 22 12.6 22 12C22 11.4 21.6 11 21 11ZM12 17C9.2 17 7 14.8 7 12C7 9.2 9.2 7 12 7C14.8 7 17 9.2 17 12C17 14.8 14.8 17 12 17Z" fill="currentColor"/>
                                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" fill="currentColor"/>
                                        </svg>
                                    </span>{{ $data->alamat }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 pe-2 mb-1">
                                <div class="d-flex align-items-center fs-7 fw-bolder text-gray-400 me-5">
                                    <span class="svg-icon svg-icon-1 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z" fill="currentColor"/>
                                            <path opacity="0.3" d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z" fill="currentColor"/>
                                        </svg>
                                    </span>{{ $data->telepon }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 pe-2 mb-1">
                                <div class="d-flex align-items-center fs-7 fw-bolder text-gray-400 me-5">
                                    <span class="svg-icon svg-icon-1 me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="currentColor"></path>
                                            <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="currentColor"></path>
                                        </svg>
                                    </span>{{ $data->email }}
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
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Surat Jalan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->nama_dealer_sj }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat Surat Jalan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->alamat_dealer_sj }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Kota Surat Jalan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->kota_dealer_sj }}" readonly>
                        </div>
                    </div>
                    <div class="separator my-10"></div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Salesman</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $data->kode_sales }}" readonly>
                                </div>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="lname" class="form-control form-control-lg form-control-solid" value="{{ $data->nama_sales }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Area</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $data->kode_area }}" readonly>
                                </div>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="lname" class="form-control form-control-lg form-control-solid" value="{{ $data->nama_area }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator my-10"></div>
                    @if(Session::get('app_user_role_id') != "D_H3")
                    <div class="row mb-6">
                        @if ($data->status_limit == 'LIMIT_PIUTANG')
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
                                    <h4 class="text-gray-900 fw-bolder">{{ $data->keterangan_limit }}</h4>
                                    <div class="fs-4 text-gray-700">{{ number_format($data->sisa_limit) }}</div>
                                </div>
                            </div>
                        </div>
                        @elseif ($data->status_limit == 'LIMIT_SALES')
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
                                    <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
                                    <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
                                </svg>
                            </span>
                            <div class="d-flex flex-grow-1">
                                <div class="fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">{{ $data->keterangan_limit }}</h4>
                                    <div class="fs-4 text-gray-700">{{ number_format($data->sisa_limit) }}</div>
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
                                    <h4 class="text-gray-900 fw-bolder">{{ $data->keterangan_limit }}</h4>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="separator my-10"></div>
                    @endif
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">NIK & NPWP</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="fname" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $data->ktp }}" readonly>
                                </div>
                                <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                    <input type="text" name="lname" class="form-control form-control-lg form-control-solid" value="{{ $data->npwp }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->alamat }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Kabupaten</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->kabupaten }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Karesidenan</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->karesidenan }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Kota</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->kota }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Telepon</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->telepon }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Email</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->email }}" readonly>
                        </div>
                    </div>
                    <div class="separator my-10"></div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Status</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->status }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Sts</label>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            <input type="text" name="company" class="form-control form-control-lg form-control-solid" value="{{ $data->sts }}" readonly>
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
