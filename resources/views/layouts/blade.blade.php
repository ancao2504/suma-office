<div id="formPembayaranNomorBPK" type="button" data-bs-toggle="modal" data-bs-target="#modalPembayaranBpk"
    data-kode="'.strtoupper(trim($result->nomor_bpk)).'"
    class="fv-row mb-4 rounded border-gray-300 border-2 border-gray-300 border-dashed px-7 py-3 mb-4">
    <div class="fv-row mt-4 mb-4 fv-plugins-icon-container">
        <div class="d-flex align-items-center">
            <div class="d-flex justify-content-start flex-column">
                <span class="text-dark fw-bolder text-hover-primary fs-6">'.strtoupper(trim($result->nomor_bpk)).'</a>
                <span class="text-gray-600 fw-bold text-muted d-block fs-6">'.date('d F Y', strtotime($result->tanggal_input)).'</span>
            </div>
        </div>
    </div>
    <div class="fv-row mb-7 fv-plugins-icon-container">
        <div class="fw-bolder fs-6 text-gray-800 d-flex align-items-center flex-wrap">
            <span class="pe-2">'.strtoupper(trim($result->nomor_giro)).'</span>
            <span class="fs-7 text-danger d-flex align-items-center">
            <span class="bullet bullet-dot bg-danger me-2"></span>'.strtoupper(trim($result->nama_bank)).'</span>
        </div>
    </div>
    <div class="fv-row text-dark fw-bolder fs-6 mt-2">Rp. '.number_format((double)$result->jumlah_pembayaran).'</div>';
</div>';
