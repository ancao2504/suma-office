@foreach($data_tracking as $data)
<div class="card card-flush mt-4">
    <div class="card-body ribbon ribbon-top ribbon-vertical pt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <span class="text-muted fw-bold d-block fs-7">Nomor Faktur :</span>
                    <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->nomor_faktur) }}</span>
                </div>
                <div class="row mt-4">
                    <span class="text-muted fw-bold d-block fs-7">Tanggal Faktur :</span>
                    <span class="text-gray-800 fs-6 fw-bolder">{{ date('j F Y', strtotime($data->tanggal)) }}</span>
                </div>
                <div class="row mt-4">
                    <span class="text-muted fw-bold d-block fs-7">Salesman :</span>
                    <span class="fw-bolder">
                        <span class="fs-6 fw-bolder text-info text-uppercase">{{ trim($data->kode_sales) }}</span>
                        <span class="text-gray-800 fs-6 fw-bolder ms-2">{{ trim($data->nama_sales) }}</span>
                    </span>
                </div>
                <div class="row mt-4">
                    <span class="text-muted fw-bold d-block fs-7">Dealer :</span>
                    <span class="fw-bolder">
                        <span class="fs-7 fw-bolder text-primary text-uppercase">{{ trim($data->kode_dealer) }}</span>
                        <span class="text-gray-800 fs-6 fw-bolder ms-2">{{ trim($data->nama_dealer) }}</span>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="timeline mt-4">
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 2) svg-icon-success @elseif($data->status_pengiriman + 1 == 2) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 2) text-success @elseif($data->status_pengiriman + 1 == 2) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 2) FINISH @elseif($data->status_pengiriman + 1 == 2) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Cetak Faktur</span>
                        </div>

                    </div>
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 3) svg-icon-success @elseif($data->status_pengiriman + 1 == 3) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 3) text-success @elseif($data->status_pengiriman + 1 == 3) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 3) FINISH @elseif($data->status_pengiriman + 1 == 3) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Gudang mempersiapkan barang</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 4) svg-icon-success @elseif($data->status_pengiriman + 1 == 4) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 4) text-success @elseif($data->status_pengiriman + 1 == 4) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 4) FINISH @elseif($data->status_pengiriman + 1 == 4) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Cetak surat jalan</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                    <div class="timeline-item align-items-center mb-7">
                        <div class="timeline-line w-40px mt-6 mb-n12"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 5) svg-icon-success @elseif($data->status_pengiriman + 1 == 5) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 5) text-success @elseif($data->status_pengiriman + 1 == 5) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 5) FINISH @elseif($data->status_pengiriman + 1 == 5) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Serah terima dengan ekspedisi</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                    <div class="timeline-item align-items-center">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon" style="margin-left: 11px">
                            <span class="svg-icon svg-icon-2 @if($data->status_pengiriman >= 6) svg-icon-success @elseif($data->status_pengiriman + 1 == 6) svg-icon-info @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor"></path>
                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="timeline-content m-0">
                            <span class="fs-8 fw-boldest @if($data->status_pengiriman >= 6) text-success @elseif($data->status_pengiriman + 1 == 6) text-info @endif text-uppercase">
                                @if($data->status_pengiriman >= 6) FINISH @elseif($data->status_pengiriman + 1 == 6) ON-PROGRESS @endif
                            </span>
                            <span class="fs-7 fw-bolder d-block text-gray-500">Toko atau ekspedisi terima barang</span>
                            <span class="fs-7 fw-bolder text-gray-500"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="separator my-5"></div>
        <a href="{{ route('orders.tracking-order-view', trim($data->nomor_faktur)) }}" class="btn btn-primary" id="viewPof" role="button">
            <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Detail Tracking
        </a>
    </div>
</div>
@endforeach
