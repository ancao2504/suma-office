<form id="formFaktur" name="formFaktur" autofill="off" autocomplete="off" method="get" action="{{ route('orders.faktur') }}">
    <div class="card card-flush mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th class="w-50px">No</th>
                            <th class="w-100px">No Faktur</th>
                            <th class="w-100px">Tanggal</th>
                            <th class="w-50px">Salesman</th>
                            <th class="w-50px">Dealer</th>
                            <th class="min-w-150px">Keterangan</th>
                            <th class="w-50px text-center">TPC</th>
                            <th class="w-50px text-center">BO</th>
                            <th class="w-100px text-center">Jenis</th>
                            <th class="min-w-100px text-end">Total</th>
                            <th class="w-50px text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data_faktur as $data)
                        <tr>
                            <td>{{ (($page->page * $page->per_page) + $loop->iteration) - 10 }}</td>
                            <td>
                                <a href="{{ route('orders.faktur-view', trim($data->nomor_faktur)) }}" class="fs-7 fw-bold text-gray-800 text-hover-primary">{{ trim($data->nomor_faktur) }}</a>
                            </td>
                            <td class="fs-7 fw-bold text-gray-800">{{ date('d/m/Y', strtotime($data->tanggal)) }}</td>
                            <td><span class="badge badge-light-info fs-7 fw-bolder text-uppercase">{{ trim($data->kode_sales) }}</span></td>
                            <td><span class="badge badge-light-primary fs-7 fw-bolder text-uppercase">{{ trim($data->kode_dealer) }}</span></td>
                            <td class="fs-7 fw-bold text-gray-800">{{ $data->keterangan }}</td>
                            <td class="text-center">
                                @if($data->kode_tpc == '14')
                                <span class="badge badge-light-primary fs-7 fw-boldest">14</span>
                                @else
                                <span class="badge badge-light-danger fs-7 fw-boldest">20</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($data->bo == 'B')
                                <span class="badge badge-light-danger fs-7 fw-boldest">BO</span>
                                @else
                                <span class="badge badge-light-info fs-7 fw-boldest">TBO</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($data->jenis_order == 'H')
                                <span class="badge badge-light-danger fs-7 fw-boldest">HOTLINE</span>
                                @elseif($data->jenis_order == 'P')
                                <span class="badge badge-light-info fs-7 fw-boldest">PMO</span>
                                @else
                                <span class="badge badge-light-success fs-7 fw-boldest">REGULER</span>
                                @endif
                            </td>
                            <td class="fs-7 fw-bold text-gray-800 text-end">{{ number_format($data->total) }}</td>
                            <td class="text-end">
                                <a href="{{ route('orders.faktur-view', trim($data->nomor_faktur)) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                    <span class="svg-icon svg-icon-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                                            <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="fs-7 fw-bolder text-gray-500 text-center pt-10 pb-10">- TIDAK ADA DATA YANG DITAMPILKAN -</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">
                <div class="row">
                    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="dataTables_length">
                            <label>
                                <select id="selectPerPageForm" name="per_page" class="form-select form-select-sm" data-control="select2" data-hide-search="true"
                                    onchange="this.form.submit()">
                                    <option value="10" @if($page->per_page == '10') {{'selected'}} @endif>10</option>
                                    <option value="25" @if($page->per_page == '25') {{'selected'}} @endif>25</option>
                                    <option value="50" @if($page->per_page == '50') {{'selected'}} @endif>50</option>
                                    <option value="100" @if($page->per_page == '100') {{'selected'}} @endif>100</option>
                                </select>
                            </label>
                        </div>
                        <div class="dataTables_info" id="selectPerPageDealerInfo" role="status" aria-live="polite">Showing <span id="startRecordSalesman"> {{ $page->from }}</span> to {{ $page->to }} of {{ $page->total }} records</div>
                    </div>
                    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                        <div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_example_5_paginate">
                            {{ $data_faktur->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
