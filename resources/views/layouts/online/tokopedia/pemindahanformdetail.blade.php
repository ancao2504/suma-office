<div class="table-responsive">
    <table class="table table-row-dashed table-row-gray-300 align-middle">
        <thead class="border">
            <tr class="fs-8 fw-bolder text-muted">
                <th rowspan="2" class="w-50px ps-3 pe-3 text-center">No</th>
                <th rowspan="2" class="w-200px ps-3 pe-3 text-center">Part Number</th>
                <th rowspan="2" class="min-w-150px ps-3 pe-3 text-center">Nama Part</th>
                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Status</th>
                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Pindah</th>
                <th colspan="3" class="w-100px ps-3 pe-3 text-center">Stock</th>
                <th rowspan="2" class="w-100px ps-3 pe-3 text-center">Action</th>
            </tr>
            <tr class="fs-8 fw-bolder text-muted">
                <th class="w-100px ps-3 pe-3 text-center">Suma</th>
                <th class="w-100px ps-3 pe-3 text-center">Tokopedia</th>
                <th class="w-100px ps-3 pe-3 text-center">Total</th>
            </tr>
        </thead>
        <tbody class="border">
            @forelse($data_detail as $detail)
            <tr>
                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                    <span class="fs-7 fw-bold text-gray-800">{{ $loop->iteration }}</span>
                </td>
                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                    <span class="fs-7 fw-boldest text-gray-800 d-block">{{ strtoupper(trim($detail->part_number)) }}</span>
                    <span class="fs-8 fw-bolder text-gray-600 d-block">{{ trim($detail->product_id) }}</span>
                    <span class="fs-8 fw-boldest text-dark mt-10 d-block">MARKETPLACE:</span>
                    <span class="fs-8 fw-bold text-gray-600">
                        SKU :<span class="fs-7 fw-bolder text-danger ms-2">@if(isset($detail->marketplace->sku)) {{ strtoupper(trim($detail->marketplace->sku)) }} @endif</span>
                        <br>
                        ProductID :<span class="fs-7 fw-bolder text-danger ms-2">@if(isset($detail->marketplace->productID)) {{ strtoupper(trim($detail->marketplace->productID)) }} @endif</span>
                    </span>
                    <div class="mt-4">
                        @if(isset($detail->marketplace->sku))
                            @if(strtoupper(trim($detail->part_number)) != strtoupper(trim($detail->marketplace->sku)))
                            <span class="badge badge-danger fs-8 fw-boldest animation-blink">PART NUMBER DAN SKU TIDAK SAMA</span>
                            @endif
                        @else
                        <span class="badge badge-danger fs-8 fw-boldest animation-blink">PART NUMBER DAN SKU TIDAK SAMA</span>
                        @endif

                        @if(isset($detail->marketplace->productID))
                            @if(strtoupper(trim($detail->product_id)) != strtoupper(trim($detail->marketplace->productID)))
                            <span class="badge badge-danger fs-8 fw-boldest animation-blink mt-2">PRODUCT ID MASTER DAN MARKETPLACE TIDAK SAMA</span>
                            @endif
                        @else
                        <span class="badge badge-danger fs-8 fw-boldest animation-blink mt-2">PRODUCT ID MASTER DAN MARKETPLACE TIDAK SAMA</span>
                        @endif
                    </div>
                </td>
                <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                    <span class="fs-7 fw-bolder text-gray-800">{{ trim($detail->nama_part) }}</span>
                </td>
                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                    @if((int)$detail->status_mp == 1)
                    <i class="fa fa-check text-success"></i>
                    @endif
                </td>
                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                    <span class="fs-7 fw-bolder text-gray-800">{{ $detail->pindah }}</span>
                </td>
                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                    <span class="fs-7 fw-bolder text-gray-800">{{ number_format($detail->stock_suma) }}</span>
                </td>
                <td class="ps-3 pe-3" style="text-align:right;vertical-align:top;">
                    <span class="fs-7 fw-bolder text-gray-800">
                        @if(isset($detail->marketplace->stock))
                        {{ number_format($detail->marketplace->stock) }}
                        @else 0
                        @endif
                    </span>
                </td>
                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                    @if($detail->status_mp == 0)
                    @if($detail->indicator == 'INCREMENT')
                    <span class="fs-7 fw-boldest text-success">
                        <i class="fa fa-arrow-up me-2 text-success" aria-hidden="true"></i>
                        @if(isset($detail->marketplace->stock))
                        {{ number_format((double)$detail->marketplace->stock + (double)$detail->pindah) }}
                        @else {{ $detail->pindah }}
                        @endif
                    </span>
                    @else
                    <span class="fs-7 fw-boldest text-danger">
                        <i class="fa fa-arrow-down me-2 text-danger" aria-hidden="true"></i>
                        @if(isset($detail->marketplace->stock))
                        {{ number_format((double)$detail->marketplace->stock - (double)$detail->pindah) }}
                        @else 0
                        @endif
                    </span>
                    @endif
                    @endif
                </td>
                <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                    @if($detail->status_mp == 0)
                    <button id="btnUpdatePerPartNumber" class="btn btn-icon btn-sm btn-primary" type="button"
                        data-nomor_dokumen="{{ strtoupper(trim($data->nomor_dokumen)) }}"
                        data-part_number="{{ strtoupper(trim($detail->part_number)) }}"
                        data-jumlah="{{ strtoupper(trim($detail->pindah)) }}"
                        data-action="{{ trim($detail->indicator) }}">
                        <i class="fa fa-check text-white"></i>
                    </button>
                    @else
                    <span class="fs-8 fw-boldest text-success">SUDAH DI PROSES</span>
                    @endif
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
