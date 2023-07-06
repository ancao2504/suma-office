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
                            <th class="w-25px ps-3 pe-3">NO</th>
                        @foreach ($table->thead as $item)
                            <th class="{{ $item->class }}">{{ $item->text }}</th>
                        @endforeach
                        </tr>
                    </thead>
                    <tbody class="border">
                        @if ($data->total == 0)
                            <tr class="fw-bolder fs-8 border">
                                <td colspan=" {{ count(get_object_vars($table->thead)) + 1 }}" class="text-center">Tidak ada data</td>
                            </tr>
                        @else
                        @php
                            $no = $data->from;
                        @endphp
                            @foreach ($data->data as $value)
                                <tr class="fw-bolder fs-8 border">
                                    <td class="w-25px ps-3 pe-3 text-center">{{ $no++ }}</td>
                                    @foreach ($table?->tbody as $item)
                                        @if ($item?->option == 'text')
                                            <td class="{{ $item->class }} ps-3 pe-3">@if(!is_array($item->key)){{ $value->{$item->key} }}@else @foreach ($item->key as $key){{ $value->{$key}.' ' }}@endforeach @endif</td>
                                        @elseif ($item?->option == 'button')
                                            <td class="{{ $item->class }} ps-3 pe-3 text-center">
                                                @foreach ($item?->button as $button)
                                                    <a class="{{ $button->class }}" role="button" data-bs-dismiss="modal"
                                                        @foreach ($button?->data as $dta)
                                                            {{'data-'.$dta->key.'='.(is_array($dta->value)?base64_encode(collect($value)->only($dta->value)):$value->{$dta->value})}}
                                                        @endforeach
                                                        >{{ $button->text }}</a>
                                                @endforeach
                                            </td>
                                        @else
                                            <td colspan="{{ count($table->thead) }}" class="text-center">Maaf terjadi kesalahan</td>
                                        @endif
                                    @endforeach
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
