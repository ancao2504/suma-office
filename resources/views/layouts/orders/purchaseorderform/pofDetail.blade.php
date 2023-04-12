@foreach ($data->data_pof_detail as $data_detail)
<div class="card card-flush mt-5" @if($data_detail->terlayani > 0) id="viewDetailPofTerlayani" type="button" data-kode="{{ trtoupper(trim($data_detail->part_number)) }}" @endif>
    <div class="card-body">
        <div class="d-flex">
            <span class="symbol symbol-100px me-5">
                <span class="symbol-label" style="background-image:url({{config('constants.app.app_images_url')}}/{{strtoupper(trim($data_detail->part_number))}}.jpg), url({{ asset('assets/images/background/part_image_not_found.png') }});"></span>
            </span>
            <div class="flex-grow-1">
                <div class="row">
                    <span class="fs-6 text-dark fw-bolder">{{ strtoupper(trim($data_detail->part_number)) }}</span>
                    <span class="fs-7 text-muted fw-bold descriptionpart">{{ trim($data_detail->nama_part) }}</span>
                    <span class="fs-5 text-dark fw-bolder mt-4">Rp. {{ number_format($data_detail->harga) }}</span>
                    @if((double)$data_detail->harga != (double)$data_detail->het)
                        <div class="d-flex align-items-center">
                            @if((double)$data_detail->disc_detail > 0)
                                <div class="badge badge-light-danger fw-bolder fs-7 p-1">{{ number_format($data_detail->disc_detail, 2) }}%</div>
                            @endif
                            <div class="text-gray-600 fw-bolder fs-7 ms-2">Rp. {{ number_format($data_detail->het) }}</div>
                        </div>
                    @endif
                    <div class="row mt-4">
                        <div class="col-6">
                            <span class="text-muted d-block fw-bold">Order:</span>
                            <div class="align-items-center">
                                <span class="fs-6 fw-bolder text-dark">{{ number_format($data_detail->jml_order) }}</span>
                                <span class="fs-7 fw-bolder text-gray-600 ms-2">PCS</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block fw-bold">Terlayani:</span>
                            <div class="align-items-center">
                                @if((double)$data_detail->terlayani > 0)
                                    @if((double)$data_detail->terlayani >= (double)$data_detail->jml_order)
                                        <span class="fs-6 fw-bolder text-success">{{ number_format($data_detail->terlayani) }}</span>
                                    @else
                                        <span class="fs-6 fw-bolder text-dark">{{ number_format($data_detail->terlayani) }}</span>
                                    @endif
                                @else
                                    <span class="fs-6 fw-bolder text-danger">{{ number_format($data_detail->terlayani) }}</span>
                                @endif
                                <span class="fs-7 fw-bolder text-gray-600 ms-2">PCS</span>
                            </div>
                        </div>
                    </div>
                    <span class="fs-5 text-danger fw-boldest mt-6">Rp. {{ number_format($data_detail->total_detail) }}</span>
                </div>
            </div>
        </div>
        <div class="row justify-content-around">
            <div class="separator my-5"></div>
            <div class="col-4">
                <button class="btn btn-link btn-color-primary btn-active-color-primary me-12" id="btnEditPofPart" type="button" data-kode="06435KPP901">
                    <span class="svg-icon svg-icon-muted svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                        </svg>
                    </span> Edit
                </button>
            </div>
            <div class="col-4">
                <button class="btn btn-link btn-color-danger btn-active-color-danger" id="btnDeletePofPart" type="button" data-kode="06435KPP901">
                    <span class="svg-icon svg-icon-muted svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M6.7 19.4L5.3 18C4.9 17.6 4.9 17 5.3 16.6L16.6 5.3C17 4.9 17.6 4.9 18 5.3L19.4 6.7C19.8 7.1 19.8 7.7 19.4 8.1L8.1 19.4C7.8 19.8 7.1 19.8 6.7 19.4Z" fill="currentColor"></path>
                            <path d="M19.5 18L18.1 19.4C17.7 19.8 17.1 19.8 16.7 19.4L5.40001 8.1C5.00001 7.7 5.00001 7.1 5.40001 6.7L6.80001 5.3C7.20001 4.9 7.80001 4.9 8.20001 5.3L19.5 16.6C19.9 16.9 19.9 17.6 19.5 18Z" fill="currentColor"></path>
                        </svg>
                    </span> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach