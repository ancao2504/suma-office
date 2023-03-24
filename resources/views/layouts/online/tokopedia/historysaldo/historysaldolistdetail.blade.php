<div class="row g-0">
    <div class="card card-flush">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableHistorySaldo" class="table table-row-dashed table-row-gray-300 align-middle">
                    <thead class="border">
                        <tr class="fs-8 fw-bolder text-muted">
                            <th class="w-100px ps-3 pe-3 text-center">Tanggal</th>
                            <th class="w-200px ps-3 pe-3 text-center">Description</th>
                            <th class="min-w-100px ps-3 pe-3 text-center">Keterangan</th>
                            <th class="w-100px ps-3 pe-3 text-center">Amount</th>
                            <th class="w-100px ps-3 pe-3 text-center">Saldo</th>
                            <th class="w-100px ps-3 pe-3 text-center">Faktur</th>
                            <th class="w-100px ps-3 pe-3 text-center">No Faktur</th>
                            <th class="w-200px ps-3 pe-3 text-center">No Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="border">
                        <!--Start List History Saldo-->
                        @foreach($data_saldo as $data)
                            @php
                                $total_faktur = 0;
                            @endphp

                            @foreach ($data->faktur as $data_faktur)
                            @php
                                $total_faktur = (double)$total_faktur + (double)$data_faktur->total
                            @endphp
                            @endforeach

                            <tr id="postOrder">
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @endif">
                                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ date('d/m/Y', strtotime($data->create_time)) }}</span>
                                    <span class="fs-8 fw-bolder text-gray-600">{{ date('h:i:s', strtotime($data->create_time)) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @endif">
                                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ trim($data->type_description) }}</span>
                                    <div class="fs-8 fw-boldest
                                        @if((int)$data->type == 1001)
                                            text-success
                                        @elseif((int)$data->type == 7001)
                                            text-info
                                        @else
                                            text-danger
                                        @endif d-flex align-items-center flex-wrap">
                                        <span class="pe-2">{{ trim($data->class) }}</span>
                                        <span class="fs-7
                                            @if((int)$data->type == 1001)
                                                text-success
                                            @elseif((int)$data->type == 7001)
                                                text-info
                                            @else
                                                text-danger
                                            @endif d-flex align-items-center">
                                            <span class="bullet bullet-dot
                                                @if((int)$data->type == 1001)
                                                    bg-success
                                                @elseif((int)$data->type == 7001)
                                                    bg-info
                                                @else
                                                    bg-danger
                                                @endif me-2">
                                            </span>{{ trim($data->type) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @endif">
                                    <span class="fs-7 fw-bolder text-gray-800 d-block">{{ trim($data->note) }}</span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @endif">
                                    <span class="fs-7 fw-boldest
                                        @if((int)$data->type == 1001)
                                            text-success
                                        @elseif((int)$data->type == 7001)
                                            text-info
                                        @else
                                            text-danger
                                        @endif d-block">{{ number_format($data->amount) }}
                                    </span>
                                </td>
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @endif">
                                    <span class="fs-7 fw-bolder text-dark d-block">{{ number_format($data->saldo) }}</span>
                                </td>

                                @if((int)$data->type == 1001)
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @endif">
                                    @if((double)$total_faktur != (double)$data->amount)
                                    <span class="fs-7 fw-boldest text-danger d-block">{{ number_format($total_faktur) }}</span>
                                    @else
                                    <span class="fs-7 fw-bolder text-dark d-block">{{ number_format($total_faktur) }}</span>
                                    @endif
                                </td>
                                @elseif((int)$data->type == 7001)
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;background-color: #7239ea;"></td>
                                @else
                                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;background-color: #f1416c;"></td>
                                @endif

                                @if((int)$data->type == 1001)
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @endif">
                                    @if((double)$total_faktur != (double)$data->amount)
                                        @foreach($data->faktur as $data_faktur)
                                        <span class="fs-7 fw-boldest text-danger d-block">{{ strtoupper(trim($data_faktur->nomor_faktur)) }}</span>
                                        @endforeach
                                    @else
                                        @foreach($data->faktur as $data_faktur)
                                        <span class="fs-7 fw-bolder text-gray-800 d-block">{{ strtoupper(trim($data_faktur->nomor_faktur)) }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                @elseif((int)$data->type == 7001)
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;background-color: #7239ea;"></td>
                                @else
                                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;background-color: #f1416c;"></td>
                                @endif
                                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;@if((int)$data->type == 1001) background-color: #fffe85; @elseif((int)$data->type == 7001) background-color: #7239ea; @else background-color: #f1416c; @endif">
                                    @if((int)$data->type == 1001)
                                        <span class="fs-7 fw-bolder text-gray-800 d-block">{{ Str::substr($data->note,-27) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <!--End List History Saldo-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
