@foreach ($data_faktur as $data)
<div class="card card-flush mt-4">
    <div class="card-body ribbon ribbon-top ribbon-vertical pt-5">
        @if ($data->kode_tpc == '14')
        <div class="ribbon-label fw-bold bg-primary">
            <i class="bi bi-percent fs-2 text-white"></i>
        </div>
        @else
        <div class="ribbon-label fw-bold bg-danger">
            <i class="bi bi-currency-dollar fs-2 text-white"></i>
        </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <table cellspacing="0" width="100%">
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">No Faktur :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->nomor_faktur) }}</span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Tanggal :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ date('j F Y', strtotime($data->tanggal)) }}</span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">No Pof :</span>
                        @if ($data->status_pof == 1)
                        <a href="{{ route('orders.purchase-order-form', trim($data->nomor_pof)) }}" class="fs-6 text-gray-800 fw-bolder d-block text-hover-primary">{{ trim($data->nomor_pof) }}</a>
                        @else
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->nomor_pof) }}</span>
                        @endif
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table cellspacing="0" width="100%">
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Salesman :</span>
                        <span class="fw-bolder">
                            <span class="fs-8 fw-boldest text-info text-uppercase">{{ trim($data->kode_sales) }}</span>
                            <span class="text-gray-800 fs-6 fw-bolder ms-2">{{ trim($data->nama_sales) }}</span>
                        </span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Dealer :</span>
                        <span class="fw-bolder">
                            <span class="fs-8 fw-boldest text-primary text-uppercase">{{ trim($data->kode_dealer) }}</span>
                            <span class="text-gray-800 fs-6 fw-bolder ms-2">{{ trim($data->nama_dealer) }}</span>
                        </span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Keterangan :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->keterangan) }}</span>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table cellspacing="0" width="100%">
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Jenis Order :</span>
                        @if ($data->jenis_order == 'R')
                        <span class="fs-8 fw-boldest text-success text-uppercase">REGULER</span>
                        @else
                        <span class="fs-8 fw-boldest text-danger text-uppercase">HOTLINE</span>
                        @endif
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">BO / Tidak BO :</span>
                        @if ($data->bo == 'B')
                        <span class="fs-8 fw-boldest text-danger text-uppercase">BO</span>
                        @else
                        <span class="fs-8 fw-boldest text-info text-uppercase">TIDAK BO</span>
                        @endif
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Total :</span>
                        <span class="text-danger fs-6 fw-bolder">Rp. {{ number_format($data->total) }}</span>
                    </tr>
                </table>
            </div>
        </div>
        <div class="separator my-5"></div>
        <a href="{{ route('orders.faktur-view', trim($data->nomor_faktur)) }}" class="btn btn-primary mb-2" id="viewFaktur" role="button">
            <span class="svg-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                    <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                </svg>
            </span> Detail Faktur
        </a>
    </div>
</div>
@endforeach
