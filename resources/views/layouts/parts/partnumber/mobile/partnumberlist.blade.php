@foreach ($data_part as $data)
<div class="col-6 col-sm-3 col-sm-2">
    <div class="card ribbon ribbon-top ribbon-vertical pt-5 h-380px mb-5" id="addToCart" role="button" data-bs-toggle="modal" data-bs-target="#modalPartNumberCart" data-kode="{{ $data->part_number }}">
        @if($data->discount_plus > 0)
        <div class="ribbon-label fw-bold bg-primary">
            <div class="row">
                <span class="fs-6 fw-bold">@if(str_contains(trim(number_format($data->discount_plus, 2)), '.00')){{ number_format($data->discount_plus) }}@else{{ number_format($data->discount_plus, 2) }}@endif%
                    <br>
                    <span class="fs-8 fw-bolder">OFF</span>
                </span>
            </div>
        </div>
        @endif
        <div class="imgpartframe">
            <img src="{{ config('constants.app.app_images_url') }}/{{ trim($data->part_number) }}.jpg"
            class="card-img-top imgpartlist imgparthelper" alt="{{ trim($data->part_number) }}"
            onerror="this.onerror=null; this.src='{{ URL::asset('assets/images/background/part_image_not_found.png') }}'">
        </div>
        <div class="row p-3">
            <h5 class="card-title">{{ trim($data->part_number) }}</h5>
            <div class="h-15px">
                <p class="form-label descriptionpart">{{ trim($data->nama_part) }}</p>
            </div>
            <div class="d-flex align-items-center pt-8 mb-4">
                <div class="row d-flex flex-column">
                    @if($data->status_discount == 1)
                    <div class="fw-bolder text-dark fs-3">Rp. {{ number_format($data->harga_netto) }}</div>
                        @if($data->discount > 0)
                        <div class="d-flex align-items-center">
                            <div class="badge badge-light-danger fw-bolder fs-7 p-1">@if(str_contains(trim(number_format($data->discount, 2)), '.00')){{ number_format($data->discount) }}@else{{ number_format($data->discount, 2) }}@endif%</div>
                            <del class="text-gray-600 fw-bold fs-7 ms-2">Rp. {{ number_format($data->het) }}</del>
                        </div>
                        @endif
                    @else
                    <div class="fw-bolder text-danger fs-6">Rp. {{ number_format($data->harga_netto) }}</div>
                    <div class="d-flex align-items-center">
                        <br>
                    </div>
                    @endif
                </div>
            </div>
            <div class="d-flex">
                @if($data->stock > 0)
                <div class="badge badge-light-success fs-7 fw-bolder mb-2">Available</div>
                @else
                <div class="badge badge-light-danger fs-7 fw-bolder mb-2">Not Available</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
