@forelse($data_part as $data)
<div class="col-md-6 col-lg-3 col-xl-3">
    <div id="btnPartNumberCart" class="card card-flush shadow ribbon ribbon-top ribbon-vertical pt-5 h-380px mb-5" type="button" role="button" data-kode="{{ $data->part_number }}">
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
            <img src="{{ config('constants.app.app_images_url') }}/parts/{{ trim($data->part_number) }}.jpg"
                class="card-img-top imgpartlist imgparthelper" alt="{{ trim($data->part_number) }}"
                onerror="this.onerror=null; this.src='{{ URL::asset('assets/images/background/part_image_not_found.png') }}'">
        </div>
        <div class="row p-3">
            <h5 class="card-title descriptionpart">{{ trim($data->part_number) }}</h5>
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
@empty
<div class="col-md-12">
    <div class="card card-flush shadow">
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
</div>
@endforelse
