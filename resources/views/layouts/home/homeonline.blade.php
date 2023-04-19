@extends('layouts.main.index')
@section('title','Home')
@section('subtitle','Dashboard')
@section('container')
    <div class="card card-flush shadow p-12">
        <div class="row">
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                        <img src="{{ Session::get('app_user_photo') }}" alt="image">
                        <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-900 text-hover-primary fs-2 fw-bolder me-1">{{ Session::get('app_user_name') }}</span>
                                <div>
                                    <span class="svg-icon svg-icon-1 svg-icon-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                            <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                                            <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="d-flex">
                                    <span class="badge badge-success">{{ Session::get('app_user_role_id') }}</span>
                                </div>
                            </div>
                            <div class="row mt-8">
                                <div class="d-flex align-items-center fw-bolder text-gray-600 text-hover-primary me-5 mb-2">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="currentColor"/>
                                            <path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="currentColor"/>
                                        </svg>
                                    </span>{{ Session::get('app_user_jabatan') }}
                                </div>
                                <div class="d-flex align-items-center fw-bolder text-gray-600 text-hover-primary me-5 mb-2">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z" fill="currentColor"/>
                                            <path opacity="0.3" d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z" fill="currentColor"/>
                                        </svg>
                                    </span>{{ Session::get('app_user_telepon') }}
                                </div>
                                <div class="d-flex align-items-center fw-bolder text-gray-600 text-hover-primary mb-2">
                                    <!--begin::Svg Icon | path: icons/duotune/communication/com011.svg-->
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21 18H3C2.4 18 2 17.6 2 17V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V17C22 17.6 21.6 18 21 18Z" fill="currentColor"/>
                                            <path d="M11.4 13.5C11.8 13.8 12.3 13.8 12.6 13.5L21.6 6.30005C21.4 6.10005 21.2 6 20.9 6H2.99998C2.69998 6 2.49999 6.10005 2.29999 6.30005L11.4 13.5Z" fill="currentColor"/>
                                        </svg>
                                    </span>{{ Session::get('app_user_email') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <h1 class="text-gray-800 mt-10 mb-10">Pemindahan</h1>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Tokopedia</h1>
                    <div class="fs-4 fw-bold text-gray-600">Update stok pemindahan antar gudang ke marketplace tokopedia</div>
                    <a href="{{ route('online.pemindahan.tokopedia.daftar') }}" class="btn btn-lg btn-flex btn-link btn-color-success">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Shopee</h1>
                    <div class="fs-4 fw-bold text-gray-600">Update stok pemindahan antar gudang ke marketplace shopee</div>
                    <a href="{{ route('online.pemindahan.shopee.daftar') }}" class="btn btn-lg btn-flex btn-link btn-color-warning">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <h1 class="text-gray-800 mt-10 mb-10">Orders</h1>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Tokopedia</h1>
                    <div class="fs-4 fw-bold text-gray-600">Proses order invoice/faktur marketplace tokopedia</div>
                    <a href="{{ route('online.orders.tokopedia.single') }}" class="btn btn-lg btn-flex btn-link btn-color-success">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Shopee</h1>
                    <div class="fs-4 fw-bold text-gray-600">Proses order invoice/faktur marketplace Shopee</div>
                    <a href="#" class="btn btn-lg btn-flex btn-link btn-color-warning">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <h1 class="text-gray-800 mt-10 mb-10">Update Harga</h1>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Tokopedia</h1>
                    <div class="fs-4 fw-bold text-gray-600">Proses update harga part number marketplace tokopedia</div>
                    <a href="{{ route('online.updateharga.tokopedia.daftar') }}" class="btn btn-lg btn-flex btn-link btn-color-success">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Shopee</h1>
                    <div class="fs-4 fw-bold text-gray-600">Proses update harga part number marketplace shopee</div>
                    <a href="{{ route('online.updateharga.shopee.daftar') }}" class="btn btn-lg btn-flex btn-link btn-color-warning">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <h1 class="text-gray-800 mt-10 mb-10">Products</h1>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/tokopedia_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Tokopedia</h1>
                    <div class="fs-4 fw-bold text-gray-600">List produk marketplace tokopedia berdasarkan part number</div>
                    <a href="{{ route('online.product.tokopedia.index') }}" class="btn btn-lg btn-flex btn-link btn-color-success">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="col-lg-6 mb-10">
                <div class="bg-light bg-opacity-50 rounded-3 pe-10 py-10 px-10">
                    <img src="{{ asset('assets/images/logo/shopee_lg.png') }}" class="h-75px mb-10" />
                    <h1 class="mb-2">Shopee</h1>
                    <div class="fs-4 fw-bold text-gray-600">List produk marketplace shopee berdasarkan part number</div>
                    <a href="{{ route('online.product.shopee.daftar') }}" class="btn btn-lg btn-flex btn-link btn-color-warning">Lihat</a>
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                    <span class="svg-icon ms-2 svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    @endpush
@endsection
