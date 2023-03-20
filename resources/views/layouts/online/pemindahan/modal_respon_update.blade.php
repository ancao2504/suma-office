<div class="modal fade" id="modal_respown" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-fullscreen-sm-down">
    <div class="modal-content">
    <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Informasi</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="document.getElementById('modal_respown').remove()"></button>
    </div>
    <div class="modal-body">
        <h1 class="modal-title fs-5 text-gray-400">Nomer Dokumen :</h1>
        <h1 class="modal-title fs-5">{{ $data_all->nomer_dokumen }}</h1>
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle">
                <thead class="border">
                    <tr class="fs-8 fw-bolder text-muted">
                        <th class="w-20px ps-3 pe-3 text-center">No</th>
                        <th class="w-100px ps-3 pe-3 text-center">Part Number</th>
                        <th class="min-w-150px ps-3 pe-3 text-center">Keterangan</th>
                        <th class="w-100px ps-3 pe-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="border">
                    @php
                        $no = 1;
                    @endphp
                    @if(!empty($data_all->success_list) && collect($data_all->success_list??[])->count() > 0)
                        @foreach($data_all->success_list as $key => $value)
                            @foreach($value as $data)
                                <tr>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $no }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $data->kode_part }}</span>
                                        {{-- @if($key == 'shopee') badge-warning @else badge-success @endif --}}
                                        <span class="fs-8 badge mt-4" style="@if($key == 'shopee') background-color: #ff7f50; @else background-color: #00b050; @endif">{{ $key }}</span>
                                        <span class="fs-8 fw-bolder text-gray-400 d-block">Product ID :</span>
                                        <span class="fs-8 fw-bolder text-gray-600 d-block">
                                            @if (empty($data->product_id) || $data->product_id == '')
                                            <span class="badge badge-light-danger">(Product ID masih kosong)</span>
                                            @else
                                            {{ strtoupper(trim($data->product_id)) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $data->keterangan }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                        <span class="badge badge-success">Success</span>
                                    </td>
                                </tr>
                                @php
                                    $no++;
                                @endphp
                            @endforeach
                        @endforeach
                    @endif
                    @if(!empty($data_all->error_list) && collect($data_all->error_list??[])->count() > 0)
                        @foreach($data_all->error_list as $key => $value)
                            @foreach($value as $data)
                                <tr>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $no }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $data->kode_part }}</span>
                                        <span class="fs-8 badge mt-4" style="@if($key == 'shopee') background-color: #ff7f50; @else background-color: #00b050; @endif">{{ $key }}</span>
                                        <span class="fs-8 fw-bolder text-gray-400 d-block">Product ID :</span>
                                        <span class="fs-8 fw-bolder text-gray-600 d-block">
                                            @if (empty($data->product_id) || $data->product_id == '')
                                            <span class="badge badge-light-danger">(Product ID masih kosong)</span>
                                            @else
                                            {{ strtoupper(trim($data->product_id)) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                        <span class="fs-7 fw-bold text-gray-800">{{ $data->keterangan }}</span>
                                    </td>
                                    <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                        <span class="badge badge-danger">Failed</span>
                                    </td>
                                </tr>
                                @php
                                    $no++;
                                @endphp
                            @endforeach
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer justify-content-between">
        <span class="text-gray-400 w-auto fs-8"><span class="required"></span>Jika status <span class="text-danger">Failed</span> dan tidak paham dengan <span class="text-dark">keterangan</span> yang diberikan maka hubunggi pihak IT Programmer</span>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="document.getElementById('modal_respown').remove()">Close</button>
    </div>
    </div>
</div>
</div>