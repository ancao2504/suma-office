@forelse ($detail_pof as $data)
<div class="card card-flush mt-5" @if($data->terlayani > 0) id="viewDetailPofTerlayani" type="button" data-bs-toggle="modal"  data-bs-target="#modalPofPartTerlayani" data-kode="{{ trim($data->part_number) }}" @endif>
    <div class="card-body">
        <div class="d-flex">
            <div class="symbol symbol-50px me-5">
                <span class="symbol-label" style="background-image:url({{ $data->image_part }}), url({{ URL::asset('assets/images/background/part_image_not_found.png') }});"></span>
            </div>
            <div class="flex-grow-1">
                <span class="text-dark fw-bolder fs-6">{{ trim($data->part_number) }}</span>
                <span class="text-muted d-block fw-bold descriptionpart">{{ $data->nama_part }}</span>
                <div class="flex-grow-1 mt-4">
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted d-block fw-bold">Order :</span>
                            <span class="fw-bolder text-dark">{{ number_format($data->jml_order) }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block fw-bold">Terlayani :</span>
                            @if ($data->jml_order > $data->terlayani)
                                @if($data->terlayani > 0)
                                <span class="fw-bolder text-primary">{{ number_format($data->terlayani) }}</span>
                                @else
                                <span class="fw-bolder text-danger">{{ number_format($data->terlayani) }}</span>
                                @endif
                            @else
                            <span class="fw-bolder text-success">{{ number_format($data->terlayani) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 mt-4">
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted d-block fw-bold">Harga :</span>
                            <span class="fw-bolder text-danger">Rp. {{ number_format($data->harga) }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block fw-bold">Disc(%) :</span>
                            <span class="fw-bolder text-dark">{{ number_format($data->disc_detail, 2) }}</span>
                        </div>
                    </div>
                </div>
                <span class="text-muted d-block fw-bold mt-4">Total :</span>
                <span class="fw-bolder text-danger">Rp. {{ number_format($data->total_detail) }}</span>
                @if($data->keterangan_bo != '')
                <div class="row mt-2">
                    <div class="d-flex">
                        <span class="badge badge-primary mt-2">{{ $data->keterangan_bo }}</span>
                    </div>
                </div>
                @endif
                @if($data->keterangan_disc_produk != '')
                <div class="row mt-2">
                    <div class="d-flex">
                        <span class="badge badge-success mt-2">{{ $data->keterangan_disc_produk }}</span>
                    </div>
                </div>
                @endif
                @if($data->keterangan_penjualan_rugi != '')
                <div class="row mt-2">
                    <div class="d-flex">
                        <span class="badge badge-danger mt-2">{{ $data->keterangan_penjualan_rugi }}</span>
                    </div>
                </div>
                @endif
                @if($data->keterangan_harga != '')
                <div class="row mt-2">
                    <div class="d-flex">
                        <span class="badge badge-info mt-2">{{ $data->keterangan_harga }}</span>
                    </div>
                </div>
                @endif
                @if($data->keterangan_disc_tpc20 != '')
                <div class="row">
                    <div class="d-flex">
                        <span class="badge badge-danger mt-2">{{ $data->keterangan_disc_tpc20 }}</span>
                    </div>
                </div>
                @endif
                @if($data->keterangan_disc_2x != '')
                <div class="row">
                    <div class="d-flex">
                        <span class="badge badge-danger mt-2">{{ $data->keterangan_disc_2x }}</span>
                    </div>
                </div>
                @endif
                @if($status_faktur != 1)
                    @if($approve != 1)
                    <div class="separator my-5"></div>
                    <div class="flex-grow-1 mt-4">
                        <button class="btn btn-primary" id="btnEditPofPart" type="button" data-bs-toggle="modal" data-kode="{{ $data->part_number }}" data-bs-target="#modalEntryPartNumber">
                            <span class="svg-icon svg-icon-muted svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"/>
                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"/>
                                </svg>
                            </span> Edit
                        </button>
                        <button class="btn btn-danger" id="btnDeletePofPart" type="button" data-kode="{{ $data->part_number }}">
                            <span class="svg-icon svg-icon-muted svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M6.7 19.4L5.3 18C4.9 17.6 4.9 17 5.3 16.6L16.6 5.3C17 4.9 17.6 4.9 18 5.3L19.4 6.7C19.8 7.1 19.8 7.7 19.4 8.1L8.1 19.4C7.8 19.8 7.1 19.8 6.7 19.4Z" fill="currentColor"/>
                                    <path d="M19.5 18L18.1 19.4C17.7 19.8 17.1 19.8 16.7 19.4L5.40001 8.1C5.00001 7.7 5.00001 7.1 5.40001 6.7L6.80001 5.3C7.20001 4.9 7.80001 4.9 8.20001 5.3L19.5 16.6C19.9 16.9 19.9 17.6 19.5 18Z" fill="currentColor"/>
                                </svg>
                            </span> Hapus
                        </button>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<center>
    <tr>
        <img src="{{ asset('assets/media/illustrations/sketchy-1/4.png') }}" alt="" class="mw-100 mh-150px mb-7">
        <div class="fw-bolder fs-3 text-gray-600 text-hover-primary">Data Not Found</div>
    </tr>
    </center>
@endforelse
<div class="card card-flush mt-5">
    <div class="card-body">
        <div class="d-flex">
            <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                    <span class="text-muted d-block fw-bold">Total Detail</span>
                </div>
                <div class="text-end py-lg-0 py-2">
                    <span class="text-gray-800 fw-boldest fs-6">Rp. {{ number_format($sub_total) }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex mt-2">
            <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                    @if($status_faktur == 1)
                    <span class="text-muted d-block fw-bold">Discount(%)</span>
                    @else
                        @if($approve == 1)
                        <span class="text-muted d-block fw-bold">Discount(%)</span>
                        @else
                        <div class="d-flex">
                            <button class="btn btn-sm btn-primary" id="btnEditPofDiscount" type="button" data-bs-toggle="modal" data-bs-target="#modalEntryDiscount">Discount (%)</button>
                        </div>
                        @endif
                    @endif
                </div>
                <div class="text-end py-lg-0 py-2">
                    <span class="text-gray-800 fw-boldest fs-6">{{ number_format($disc_header, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex mt-2">
            <div class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1">
                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                    <span class="text-muted d-block fw-bold">Grand Total</span>
                </div>
                <div class="text-end py-lg-0 py-2">
                    <span class="text-danger fw-boldest fs-6">Rp. {{ number_format($grand_total) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
