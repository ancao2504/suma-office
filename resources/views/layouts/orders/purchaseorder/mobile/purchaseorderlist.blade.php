@foreach($data_pof as $data)
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
                        <span class="text-muted fw-bold d-block fs-7 mt-4">No POF :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ trim($data->nomor_pof) }}</span>
                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Tanggal :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ date('j F Y', strtotime($data->tanggal_pof)) }}</span>
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
                </table>
            </div>
            <div class="col-md-4">
                <table cellspacing="0" width="100%">
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Order / Terlayani :</span>
                        <span class="text-gray-800 fs-6 fw-bolder">{{ $data->jml_order }}</span>
                        <span class="text-gray-800 fs-6 fw-bolder"> / </span>
                        @if ($data->terlayani > 0)
                            @if ($data->terlayani == $data->jml_order )
                            <span class="text-success fs-6 fw-bolder">{{ $data->terlayani }}</span>
                            @else
                            <span class="text-primary fs-6 fw-bolder">{{ $data->terlayani }}</span>
                            @endif
                        @else
                        <span class="text-danger fs-6 fw-bolder">{{ $data->terlayani }}</span>
                        @endif

                    </tr>
                    <tr>
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Status</span>
                        <div>
                            <div class="badge badge-primary badge-lg fs-8 fw-boldest mt-2">TERKIRIM</div>
                            @if($data->approve == 1)
                            <div class="badge badge-info badge-lg fs-8 fw-boldest mt-2">APPROVED</div>
                            @endif
                            @if($data->on_faktur == 1)
                            <div class="badge badge-success badge-lg fs-8 fw-boldest mt-2">SUDAH DIPROSES</div>
                            @endif
                        </div>
                    </tr>
                </table>
            </div>
        </div>
        <div class="separator my-5"></div>
        <a href="{{ route('orders.purchase-order-form', trim($data->nomor_pof)) }}" class="btn btn-primary mb-2" id="viewPof" role="button">
            <i class="bi bi-view-stacked text-white"></i> Lihat Purchase Order
        </a>
    </div>
</div>
@endforeach
