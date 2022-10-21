<form id="formPurchaseOrder" name="formPurchaseOrder" autofill="off" autocomplete="off" method="get" action="{{ route('orders.purchase-order') }}">
    <div class="card card-flush mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th class="w-50px">No</th>
                            <th class="w-100px">No POF</th>
                            <th class="w-100px">Tanggal</th>
                            <th class="w-50px">Salesman</th>
                            <th class="w-50px">Dealer</th>
                            <th class="w-150px">Keterangan</th>
                            <th class="w-50px text-end">Order</th>
                            <th class="w-50px text-end">Terlayani</th>
                            <th class="w-50px text-center">TPC</th>
                            <th class="w-50px text-center">Status</th>
                            <th class="w-50px text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data_pof as $data)
                        <tr>
                            <td>{{ (($page->page * $page->per_page) + $loop->iteration) - 10 }}</td>
                            <td>
                                <a href="{{ route('orders.purchase-order-form', trim($data->nomor_pof)) }}" class="fs-6 fw-bold text-gray-800 text-hover-primary">{{ trim($data->nomor_pof) }}</a>
                            </td>
                            <td class="fs-6 fw-bold text-gray-800">{{ date('d/m/Y', strtotime($data->tanggal_pof)) }}</td>
                            <td><span class="badge badge-light-info fs-7 fw-boldest text-uppercase">{{ trim($data->kode_sales) }}</span></td>
                            <td><span class="badge badge-light-primary fs-7 fw-boldest text-uppercase">{{ trim($data->kode_dealer) }}</span></td>
                            <td class="fs-6 fw-bold text-gray-800">{{ $data->keterangan }}</td>
                            <td class="fs-6 fw-bold text-gray-800 text-end">{{ number_format($data->jml_order) }}</td>
                            <td class="text-end">
                                @if ($data->terlayani > 0)
                                    @if ($data->terlayani == $data->jml_order )
                                    <span class="text-success fs-6 fw-bolder">{{ $data->terlayani }}</span>
                                    @else
                                    <span class="text-info fs-6 fw-bolder">{{ $data->terlayani }}</span>
                                    @endif
                                @else
                                <span class="text-danger fs-6 fw-bolder">{{ $data->terlayani }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($data->kode_tpc == '14')
                                <span class="badge badge-light-primary fs-7 fw-boldest">14</span>
                                @else
                                <span class="badge badge-light-danger fs-7 fw-boldest">20</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($data->on_faktur == 1)
                                <div class="badge badge-light-success badge-lg fs-8 fw-boldest mt-2">TERPROSES</div>
                                @else
                                    @if($data->approve == 1)
                                    <div class="badge badge-light-info badge-lg fs-8 fw-boldest mt-2">APPROVED</div>
                                    @else
                                    <div class="badge badge-light-primary badge-lg fs-8 fw-boldest mt-2">TERKIRIM</div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('orders.purchase-order-form', trim($data->nomor_pof)) }}" class="btn btn-icon btn-primary btn-sm">
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
                            {{ $data_pof->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
