<form id="formFaktur" name="formFaktur" autofill="off" autocomplete="off" method="get" action="{{ route('orders.faktur') }}">
    <div class="card card-flush mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fw-bolder text-muted">
                            <th class="w-50px ps-3 pe-3 text-center">No</th>
                            <th class="w-100px ps-3 pe-3 text-center">No Faktur</th>
                            <th class="w-100px ps-3 pe-3 text-center">Tanggal</th>
                            <th class="w-50px ps-3 pe-3 text-center">Salesman</th>
                            <th class="w-50px ps-3 pe-3 text-center">Dealer</th>
                            <th class="min-w-150px ps-3 pe-3 text-center">Keterangan</th>
                            <th class="w-50px text-center ps-3 pe-3 text-center">TPC</th>
                            <th class="w-50px text-center ps-3 pe-3 text-center">BO</th>
                            <th class="w-100px text-center ps-3 pe-3 text-center">Jenis</th>
                            <th class="min-w-100px text-end ps-3 pe-3 text-center">Total</th>
                            <th class="w-50px ps-3 pe-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        @forelse($data_faktur as $data)
                        <tr>
                            <td class="ps-3 pe-3 fs-7 fw-bolder text-gray-800 text-center">{{ (($page->page * $page->per_page) + $loop->iteration) - 10 }}</td>
                            <td>
                                <a href="{{ route('orders.faktur-view', trim($data->nomor_faktur)) }}" class="fs-7 fw-bolder text-gray-800 text-hover-primary">{{ trim($data->nomor_faktur) }}</a>
                            </td>
                            <td class="fs-7 fw-bolder text-gray-800 text-center">{{ date('d/m/Y', strtotime($data->tanggal)) }}</td>
                            <td class="ps-3 pe-3"><span class="ps-3 pe-3 badge badge-light-info fs-8 fw-boldest text-uppercase">{{ trim($data->kode_sales) }}</span></td>
                            <td class="ps-3 pe-3"><span class="badge badge-light-primary fs-8 fw-boldest text-uppercase">{{ trim($data->kode_dealer) }}</span></td>
                            <td class="ps-3 pe-3 fs-7 fw-bold text-gray-800">{{ $data->keterangan }}</td>
                            <td class="ps-3 pe-3 text-center">
                                @if($data->kode_tpc == '14')
                                <span class="badge badge-light-primary fs-8 fw-boldest">14</span>
                                @else
                                <span class="badge badge-light-danger fs-8 fw-boldest">20</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3 text-center">
                                @if($data->bo == 'B')
                                <span class="badge badge-light-danger fs-8 fw-boldest">BO</span>
                                @else
                                <span class="badge badge-light-info fs-8 fw-boldest">TBO</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3 text-center">
                                @if($data->jenis_order == 'H')
                                <span class="badge badge-light-danger fs-8 fw-boldest">HOTLINE</span>
                                @elseif($data->jenis_order == 'P')
                                <span class="badge badge-light-info fs-8 fw-boldest">PMO</span>
                                @else
                                <span class="badge badge-light-success fs-8 fw-boldest">REGULER</span>
                                @endif
                            </td>
                            <td class="ps-3 pe-3 fs-7 fw-bolder text-gray-800 text-end">{{ number_format($data->total) }}</td>
                            <td class="ps-3 pe-3 text-center">
                                <a href="{{ route('orders.faktur-view', trim($data->nomor_faktur)) }}" class="btn btn-icon btn-primary btn-sm">
                                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
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
