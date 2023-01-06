@forelse($data_bo as $data)
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
@empty
<div class="col-md-12">
    <div class="card card-flush">
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


