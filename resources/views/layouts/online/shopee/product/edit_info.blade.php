<div class="fv-row mt-6">
    <div class="alert alert-{{ (($dataApi->internal->status == 0)?'success':'danger') }}">
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-{{ (($dataApi->internal->status == 0)?'success':'danger') }}">Informasi</h4>
            <span>{{ $dataApi->internal->message }}</span>
        </div>
    </div>
</div>
<div class="fv-row mt-6">
    <div class="d-flex mb-7">
        <span class="symbol symbol-200px me-5">
            <img src="{{ trim($dataApi->marketplace->pictures) }}" onerror="this.onerror=null; this.src={{  asset('assets/images/background/part_image_not_found.png') }}"
                alt="{{ trim($dataApi->marketplace->product_id) }}">
        </span>
        <div class="flex-grow-1">
            <div class="row">
                <span class="fs-8 text-gray-400 fw-bolder">Nama Product:</span>
                <p class="fs-6 text-gray-800 fw-bolder descriptionpart">{{ trim($dataApi->marketplace->name) }}</p>
                <span class="fs-8 text-gray-400 fw-bolder mt-4">SKU:</span>
                <span class="fs-6 text-gray-700 fw-bolder">{{ trim($dataApi->marketplace->sku) }}</span>
                <span class="fs-8 text-gray-400 fw-bolder mt-4">Product ID:</span>
                <span class="fs-7 text-danger fw-boldest">{{ trim($dataApi->marketplace->product_id) }}</span>
            </div>
        </div>
    </div>
</div>