<form id="formFaktur" name="formFaktur" autofill="off" autocomplete="off" method="get" action="{{ route('orders.faktur') }}">
    <div class="card card-flush mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300">
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
                            <th class="w-100px text-center">Jenis Order</th>
                            <th class="min-w-100px text-end">Total</th>
                            <th class="w-50px text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_faktur as $data)
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
                                <span class="badge badge-light-primary fs-7 fw-bolder">14</span>
                                @else
                                <span class="badge badge-light-danger fs-7 fw-bolder">20</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($data->bo == 'B')
                                <span class="badge badge-light-danger fs-7 fw-bolder">BO</span>
                                @else
                                <span class="badge badge-light-info fs-7 fw-bolder">Tidak BO</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($data->jenis_order == 'H')
                                <span class="badge badge-light-danger fs-7 fw-bolder">HOTLINE</span>
                                @elseif($data->jenis_order == 'P')
                                <span class="badge badge-light-info fs-7 fw-bolder">PMO</span>
                                @else
                                <span class="badge badge-light-success fs-7 fw-bolder">REGULER</span>
                                @endif
                            </td>
                            <td class="fs-7 fw-bolder text-gray-800 text-end">Rp. {{ number_format($data->total) }}</td>
                            <td class="text-end">
                                <a href="{{ route('orders.faktur-view', trim($data->nomor_faktur)) }}" class="btn btn-icon btn-bg-primary btn-sm">
                                    <i class="bi bi-view-stacked text-white"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
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
