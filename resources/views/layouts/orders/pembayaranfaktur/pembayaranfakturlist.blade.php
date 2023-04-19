@if(strtoupper(trim($data_device->device)) == 'DESKTOP')
<div class="card card-flush shadow mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle">
                <thead class="border">
                    <tr class="fs-8 fw-bolder text-muted">
                        <th class="w-50px ps-3 pe-3 text-center">No</th>
                        <th class="w-200px ps-3 pe-3 text-center">No Faktur</th>
                        <th class="w-50px ps-3 pe-3 text-center">Sales</th>
                        <th class="w-50px ps-3 pe-3 text-center">Dealer</th>
                        <th class="min-w-150px ps-3 pe-3 text-center">Keterangan</th>
                        <th class="w-100px ps-3 pe-3 text-center">Jatuh Tempo</th>
                        <th class="w-100px text-end ps-3 pe-3 text-center">Total</th>
                        <th class="w-100px text-end ps-3 pe-3 text-center">Terbayar</th>
                        <th class="w-50px ps-3 pe-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="border">
                    @forelse($data_pembayaran as $data)
                    <tr>
                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                            <span class="fs-7 fw-bolder text-gray-800">{{ ((($data_page->current_page * $data_page->per_page) - $data_page->per_page) + $loop->iteration) }}</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                            <div class="row align-items-start">
                                <a href="{{ route('orders.faktur.form', trim($data->nomor_faktur)) }}" class="fs-7 fw-boldest text-gray-800 text-hover-primary">{{ trim($data->nomor_faktur) }}</a>
                                <span class="fs-7 fw-bold text-gray-600">{{ date('d F Y', strtotime($data->tanggal_faktur)) }}</span>
                            </div>
                            <div class="row align-items-end">
                                <div class="mt-6"></div>
                            </div>
                            <div class="row align-items-end">
                                <div class="d-flex">
                                    @if($data->status == 'LUNAS')
                                    <span class="badge badge-light-success fs-8 fw-boldest">
                                        <i class="fa fa-check text-success me-2"></i> Finish
                                    </span>
                                    @else
                                    @if($data->status_sisa_hari == 'LEBIH')
                                    <span class="badge badge-light-danger fs-8 fw-boldest">
                                        <i class="bi bi-clock-fill text-danger pe-2"></i>Lebih {{ number_format($data->sisa_hari) }} Hari
                                    </span>
                                    @else
                                    <span class="badge badge-info fs-8 fw-boldest">
                                        <i class="bi bi-clock-fill text-white pe-2"></i>Kurang {{ number_format($data->sisa_hari) }} Hari
                                    </span>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                            <span class="fs-8 fw-boldest text-primary">{{ strtoupper(trim($data->kode_sales)) }}</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                            <span class="fs-8 fw-boldest text-danger">{{ strtoupper(trim($data->kode_dealer)) }}</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                            <span class="fs-7 fw-bolder text-gray-800">{{ trim($data->keterangan_faktur) }}</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                            <span class="fs-8 fw-boldest text-danger">{{ date('j F Y', strtotime($data->tanggal_jtp)) }}</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                            <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->total_faktur) }}</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                            <span class="fs-7 fw-bolder text-gray-800">{{ number_format($data->total_pembayaran) }}</span>
                        </td>
                        <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                            <button class="btn btn-icon @if($data->status == 'LUNAS') btn-success @else @if($data->status_sisa_hari == 'LEBIH') btn-danger @else btn-info @endif @endif btn-sm mb-2"
                                id="btnFormPembayaranFaktur" type="button" data-kode="{{ $data->nomor_faktur }}" data-status="{{ $data->status }}">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
@forelse($data_pembayaran as $data)
<div class="card card-flush shadow mt-4">
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
            <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
                <a href="{{ route('orders.faktur.form', trim($data->nomor_faktur)) }}" class="fs-5 text-gray-800 text-hover-primary fw-bolder">{{ trim($data->nomor_faktur) }}</a>
                <span class="text-muted fw-bold fs-7">{{ date('j F Y', strtotime($data->tanggal_faktur)) }}</span>
                <div class="d-flex mt-2">
                    <span class="badge badge-light-primary fs-7 fw-bolder me-2">{{ trim($data->kode_sales) }}</span>
                    <span class="badge badge-light-danger fs-7 fw-bolder">{{ trim($data->kode_dealer) }}</span>
                </div>
                <span class="text-muted fw-bold d-block fs-7 mt-4">Jatuh Tempo :</span>
                <span class="text-danger fs-6 fw-bolder">{{ date('j F Y', strtotime($data->tanggal_jtp)) }}</span>
                <div class="row">
                    <div class="col-lg-4">
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Total :</span>
                        <span class="text-danger fs-6 fw-bolder">Rp. {{ number_format($data->total_faktur) }}</span>
                    </div>
                    <div class="col-lg-4">
                        <span class="text-muted fw-bold d-block fs-7 mt-4">Terbayar :</span>
                        @if($data->total_pembayaran >= $data->total_faktur)
                        <span class="text-success fs-6 fw-bolder">Rp. {{ number_format($data->total_pembayaran) }}</span>
                        @else
                        <span class="text-danger fs-6 fw-bolder">Rp. {{ number_format($data->total_pembayaran) }}</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex mt-4">
                @if($data->status != 'LUNAS')
                    @if($data->status_sisa_hari == 'LEBIH')
                    <span class="badge badge-danger fs-base">
                        <i class="bi bi-clock-fill fs-2 text-white pe-2"></i>Lebih {{ number_format($data->sisa_hari) }} Hari
                    </span>
                    @else
                    <span class="badge badge-info fs-base">
                        <i class="bi bi-clock-fill fs-2 text-white pe-2"></i>Kurang {{ number_format($data->sisa_hari) }} Hari
                    </span>
                    @endif
                @endif
                </div>
            </div>
        </div>
        @if ($data->total_pembayaran > 0)
        <div class="separator my-5"></div>
        <button class="btn @if($data->status == 'LUNAS') btn-success @else @if($data->status_sisa_hari == 'LEBIH') btn-danger @else btn-info @endif @endif mb-2"
                id="btnFormPembayaranFaktur" type="button" data-kode="{{ $data->nomor_faktur }}" data-status="{{ $data->status }}">
                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i> Detail Pembayaran
        </button>
        @endif
    </div>
</div>
@empty
<div class="card card-flush shadow mt-4">
    <div class="card-body d-flex flex-column justify-content-center pe-0 h-300px">
        <div class="row text-center pe-10">
            <span class="svg-icon svg-icon-muted">
                <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                    <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                </svg>
            </span>
        </div>
        <div class="row text-center pt-8">
            <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
        </div>
    </div>
</div>
@endforelse
@endif

<div class="row">
    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-8">
        <div class="dataTables_length">
            <label>
                <select id="selectPerPageMasterData" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                    <option value="10" @if($data_page->per_page == '10') {{'selected'}} @endif>10</option>
                    <option value="25" @if($data_page->per_page == '25') {{'selected'}} @endif>25</option>
                    <option value="50" @if($data_page->per_page == '50') {{'selected'}} @endif>50</option>
                    <option value="100" @if($data_page->per_page == '100') {{'selected'}} @endif>100</option>
                </select>
            </label>
        </div>
        <div class="dataTables_info" id="selectPerPageMasterDataInfo" role="status" aria-live="polite">Showing <span id="startRecordMasterData">{{ $data_page->from }}</span> to {{ $data_page->to }} of {{ $data_page->total }} records</div>
    </div>
    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end mt-8">
        <div class="dataTables_paginate paging_simple_numbers" id="paginationMasterData">
            <ul class="pagination">
                @foreach ($data_page->links as $link)
                <li class="page-item @if($link->active == true) active @endif
                    @if($link->url == '') disabled @endif
                    @if($data_page->current_page == $link->label) active @endif">
                    @if($link->active == true)
                    <span class="page-link">{{ $link->label }}</span>
                    @else
                    <a href="#" class="page-link" data-page="@if(trim($link->url) != ''){{ explode("?page=" , $link->url)[1] }}@endif"
                        @if(trim($link->url) == '') disabled @endif>
                        @if(Str::contains(strtolower($link->label), 'previous'))
                        <i class="previous"></i>
                        @elseif(Str::contains(strtolower($link->label), 'next'))
                        <i class="next"></i>
                        @else
                        {{ $link->label }}
                        @endif
                    </a>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>


