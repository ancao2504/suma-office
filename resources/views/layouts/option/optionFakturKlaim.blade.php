<div class="modal-dialog modal-dialog-centered {{ $modal->size }}">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ $modal->title }}</h5>
            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#detail_modal" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </div>
        </div>
        <div class="modal-body" id="modal_body">
            <div class="input-group px-3 mb-3">
                <input type="text" class="form-control" id="no_faktur" name="no_faktur" placeholder="{{ $cari->title }}" value="{{ $cari->value }}">
                <button id="btn_cari" class="btn btn-secondary" type="button">Cari</button>
            </div>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border border-2">
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th rowspan="2" class="w-25px ps-3 pe-3">NO</th>
                            <th rowspan="2" class="min-w-100px ps-3 pe-3">No Faktur</th>
                            <th rowspan="2" class="w-100px ps-3 pe-3">Tanggal</th>
                            <th colspan="2" class="ps-3 pe-3">Part</th>
                            <th rowspan="2" class="min-w-100px ps-3 pe-3">Action</th>
                        </tr>
                        <tr class="fs-8 fw-bolder border-start-0 border-end-0 text-muted text-center">
                            <th class="text-center max-w-150px">Part</th>
                            <th class="text-center maxw-150px">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        @if ($data->total == 0)
                            <tr class="fw-bolder fs-8 border">
                                <td colspan="99" class="text-center">Tidak ada data</td>
                            </tr>
                        @else
                        @php
                            $no = $data->from;
                        @endphp
                            @foreach ($data->data as $value)
                                <tr class="fw-bolder fs-8 border border-2">
                                    <td class="ps-3 pe-3 text-center">{{ $no++ }}</td>
                                    <td class="ps-3 pe-3">{{ $value->no_faktur }}</td>
                                    <td class="ps-3 pe-3 text-center">{{ date('d/m/Y', strtotime($value->tgl_faktur)) }}</td>
                                    <td class="p-0 m-0" colspan="2">
                                        <table class="table table-row-dashed table-row-gray-300 align-middle p-0 m-0">
                                            <tbody>
                                                @foreach ($value->detailPart as $detailPart)
                                                    <tr class="fw-bolder fs-8">
                                                        <td class="ps-3 pe-3 text-start">{{ (string)$detailPart->kd_part }}</td>
                                                        <td class="pe-3 text-end">{{ (string)$detailPart->jml_jual }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="w-50px ps-3 pe-3 text-center">
                                        <button type="button" class="btn btn-sm btn-primary pilih" data-a="{{
                                            base64_encode(
                                                json_encode(
                                                    (object)[
                                                        'no_faktur' => $value->no_faktur,
                                                        'tgl_faktur' => $value->tgl_faktur
                                                    ]
                                                )
                                            )
                                        }}">Pilih</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer justify-content-center">
                <div colspan="8">
                    <div class="form-group mb-2 w-80px">
                        <select class="form-select form-select-sm" id="per_page">
                            <option value="10" {{ $per_page->value == 10 ? 'selected' : '' }}>10</option>
                            <option value="50" {{ $per_page->value == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $per_page->value == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    @php
                        $paginator = new Illuminate\Pagination\LengthAwarePaginator(
                            $data->data,
                            $data->total,
                            $data->per_page,
                            $data->current_page,
                            [
                                'path' => '#',
                            ]
                        );
                    @endphp
                    {{ $paginator->links() }}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light close" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#detail_modal">Close</button>
        </div>
    </div>
</div>
