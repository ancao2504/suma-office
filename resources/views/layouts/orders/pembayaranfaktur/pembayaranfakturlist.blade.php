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
                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Detail Pembayaran
            </button>
            @endif
        </div>
    </div>
    @endforeach
