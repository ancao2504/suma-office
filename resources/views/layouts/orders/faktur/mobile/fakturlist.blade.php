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
            <i class="bi bi-view-stacked text-white"></i> Detail Faktur
        </a>
    </div>
</div>
@endforeach
