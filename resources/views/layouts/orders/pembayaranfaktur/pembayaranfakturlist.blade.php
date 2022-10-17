@foreach($data_pembayaran as $data)
<div class="card card-flush mt-4">
    <div class="card-body ribbon ribbon-top ribbon-vertical pt-5">
        @if($data->status == 'LUNAS')
        <div class="ribbon-label fw-bold bg-success">
            <i class="bi bi-check-circle-fill fs-2 text-white"></i>
        </div>
        @else
            @if($data->status_sisa_hari == 'LEBIH')
            <div class="ribbon-label fw-bold bg-danger">
                <i class="bi bi-exclamation-circle-fill fs-2 text-white"></i>
            </div>
            @else
            <div class="ribbon-label fw-bold bg-info">
                <i class="bi bi-bell-fill fs-2 text-white"></i>
            </div>
            @endif
        @endif
        <div class="d-flex mb-7">
            <div class="d-flex">
                <div class="symbol symbol-60px symbol-2by3 flex-shrink-0 me-4">
                    @if($data->status == 'LUNAS')
                    <span class="symbol-label fs-5 fw-bolder bg-light-success text-success">{{ trim($data->urut_faktur) }}</span>
                    @else
                        @if($data->status_sisa_hari == 'LEBIH')
                        <span class="symbol-label fs-5 fw-bolder bg-light-danger text-danger">{{ trim($data->urut_faktur) }}</span>
                        @else
                        <span class="symbol-label fs-5 fw-bolder bg-light-info text-info">{{ trim($data->urut_faktur) }}</span>
                        @endif
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                    <a href="{{ route('orders.faktur-view', trim($data->nomor_faktur)) }}" class="fs-5 text-gray-800 text-hover-primary fw-bolder">{{ trim($data->nomor_faktur) }}</a>
                    <span class="text-muted fw-bold fs-7">{{ date('j F Y', strtotime($data->tanggal_faktur)) }}</span>
                    <div class="d-flex mt-2">
                        <span class="badge badge-light-primary fs-7 fw-bolder me-2">{{ trim($data->kode_sales) }}</span>
                        <span class="badge badge-light-danger fs-7 fw-bolder">{{ trim($data->kode_dealer) }}</span>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <table cellspacing="0" width="100%">
                                    <tr>
                                        <span class="text-muted fw-bold d-block fs-7 mt-4">Jatuh Tempo :</span>
                                        <span class="text-danger fs-6 fw-bolder">{{ date('j F Y', strtotime($data->tanggal_jtp)) }}</span>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2">
                                <table cellspacing="0" width="100%">
                                    <tr>
                                        <span class="text-muted fw-bold d-block fs-7 mt-4">Total :</span>
                                        <span class="text-danger fs-6 fw-bolder">Rp. {{ number_format($data->total_faktur) }}</span>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-2">
                                <table cellspacing="0" width="100%">
                                    <tr>
                                        <span class="text-muted fw-bold d-block fs-7 mt-4">Terbayar :</span>
                                        @if($data->total_pembayaran >= $data->total_faktur)
                                        <span class="text-success fs-6 fw-bolder">Rp. {{ number_format($data->total_pembayaran) }}</span>
                                        @else
                                        <span class="text-danger fs-6 fw-bolder">Rp. {{ number_format($data->total_pembayaran) }}</span>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-4">
                    @if($data->status != 'LUNAS')
                        @if($data->status_sisa_hari == 'LEBIH')
                        <span class="badge badge-danger fs-base">
                            <i class="bi bi-clock-fill fs-2 text-white pe-2"></i>
                            Lebih {{ number_format($data->sisa_hari) }} Hari
                        </span>
                        @else
                        <span class="badge badge-info fs-base">
                            <i class="bi bi-clock-fill fs-2 text-white pe-2"></i>
                            Kurang {{ number_format($data->sisa_hari) }} Hari
                        </span>
                        @endif
                    @endif
                    </div>
                </div>
            </div>
        </div>
        @if ($data->total_pembayaran > 0)
        <div class="separator my-5"></div>
        <button class="btn @if($data->status == 'LUNAS') btn-success @else @if($data->status_sisa_hari == 'LEBIH') btn-danger @else btn-info @endif @endif mb-2"
                id="viewPembayaranFaktur" type="button" data-bs-toggle="modal" data-bs-target="#modalPembayaranPerFaktur" data-kode="{{ $data->nomor_faktur }}" data-status="{{ $data->status }}">
                <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                        <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                    </svg>
                </span> Detail Pembayaran
        </button>
        @endif
    </div>
</div>
@endforeach
