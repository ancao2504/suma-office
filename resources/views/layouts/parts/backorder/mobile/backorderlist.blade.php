@foreach ($data_bo as $data)
<div class="col-6 col-sm-3 col-sm-2">
    <div class="card ribbon ribbon-top ribbon-vertical h-380px mb-5">
        @if ($data->kode_tpc == '14')
        <div class="ribbon-label fw-bold bg-danger">
            <i class="bi bi-percent fs-2 text-white"></i>
        </div>
        @else
        <div class="ribbon-label fw-bold bg-success">
            <i class="bi bi-currency-dollar fs-2 text-white"></i>
        </div>
        @endif
        <div class="imgpartframe">
            <img src="{{ config('constants.app.app_images_url') }}/{{ trim($data->part_number) }}.jpg"
            class="card-img-top imgpartlist imgparthelper" alt="{{ trim($data->part_number) }}"
            onerror="this.onerror=null; this.src='{{ URL::asset('assets/images/background/part_image_not_found.png') }}'">
        </div>
        <div class="row p-3">
            <h5 class="card-title">{{ trim($data->part_number) }}</h5>
            <div class="h-20px">
                <p class="form-label descriptionpart">{{ trim($data->nama_part) }}</p>
            </div>
            <div class="d-flex align-items-center pt-8 mb-4">
                <div class="row d-flex flex-column">
                    @if ($data->kode_tpc == '14')
                        @if($data->status_discount == 1)
                        <div class="fw-bolder text-dark fs-3">Rp. {{ number_format($data->harga) }}</div>
                        <div class="d-flex align-items-center">
                            @if($data->discount_1 > 0)
                            <div class="badge badge-light-danger fw-bolder fs-7 p-1">-{{ number_format($data->discount_1) }}%</div>
                            @endif
                            @if($data->discount_2 > 0)
                            <div class="badge badge-light-danger fw-bolder fs-7 p-1">-{{ number_format($data->discount_2) }}%</div>
                            @endif
                            <del class="text-gray-600 fw-bold fs-7 ms-2">Rp. {{ number_format($data->het) }}</del>
                        </div>
                        @else
                        <div class="fw-bolder text-danger fs-6">Rp. {{ number_format($data->het) }}</div>
                        @endif
                    @else
                    <span class="fw-bolder text-dark fs-3">Rp. {{ number_format($data->harga) }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex">
                <span class="badge badge-light-primary fs-7 fw-bolder me-2">{{ trim($data->kode_sales) }}</span>
                <span class="badge badge-light-danger fs-7 fw-bolder">{{ trim($data->kode_dealer) }}</span>
            </div>
            <span class="text-muted fw-bold d-block fs-7 mt-6">Jumlah Back Order :</span>
            <span class="text-gray-800 fs-6 fw-bolder mb-2">{{ number_format($data->jumlah_bo) }}
                <span class="text-gray-600 fs-7 fw-bolder ms-2">PCS</span>
            </span>
        </div>
    </div>
</div>
@endforeach


