<div class="modal-dialog modal-dialog-centered {{ $modal->size }}">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ $modal->title }}</h5>
            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </div>
        </div>
        <div class="modal-body" id="modal_body">
            <div class="input-group px-3 mb-3">
                <input type="text" class="form-control" id="cari" name="cari" placeholder="{{ $cari->title }}" value="{{ $cari->value }}">
                <button id="btn_cari" class="btn btn-secondary" type="button">Cari</button>
            </div>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted text-center">
                            <th class="w-25px ps-3 pe-3">No</th>
                            <th class="w-50px text">Dealer</th>
                            <th class="w-50px text">Nomor Klaim</th>
                            <th class="w-50px text">Tanggal Klaim</th>
                            <th class="w-25px text">part Number</th>
                            <th class="w-25px text">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        @if ($data->total == 0)
                            <tr class="fw-bolder fs-8 border">
                                <td colspan=" {{ count($table->thead) + 1 }}" class="text-center">Tidak ada data</td>
                            </tr>
                        @else
                        @php
                            $no = $data->from;
                        @endphp
                            @foreach ($data->data as $row)
                                <tr class="fw-bolder fs-8 border">
                                    <td class="w-25px ps-3 pe-3 text-center">{{ $no++ }}</td>
                                    <td class="w-50px ps-3 pe-3 text-center">{{ $row->kd_dealer }}</td>
                                    <td class="w-50px ps-3 pe-3 text-center">{{ $row->no_retur }}</td>
                                    <td class="w-100px ps-3 pe-3 text-center">{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                                    <td class="w-auto ps-3 pe-3">
                                        @foreach ($row->detail as $item)
                                            {{ $item }}<br>
                                        @endforeach
                                    </td>
                                    <td class="w-auto text-center ps-3 pe-3">
                                        <a class="btn btn-primary me-2 pilih" role="button" data-bs-dismiss="modal" data-a="{{ base64_encode(collect($row)->only(['no_retur','tanggal','kd_dealer'])) }}">Pilih</a>
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
            <button type="button" class="btn btn-light close" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
