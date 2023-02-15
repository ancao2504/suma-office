<div class="modal fade" id="modalOptionUpdateHarga" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formOptionUpdateHarga" name="formOptionUpdateHarga" autofill="off" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Data Update Harga</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-muted svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z" fill="currentColor"/>
                                <path d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="fv-row mb-5">
                        <label class="form-label">Cari berdasarkan update harga:</label>
                        <span id="inputKodeLokasi" class="input-group-text" style="width: 100%;" hidden></span>
                        <div class="input-group mt-6">
                            <span class="input-group-text">Pencarian</span>
                            <input id="inputSearchOptionUpdateHarga" type="text" class="form-control" placeholder="Input Data Pencarian">
                            <button id="btnSearchOptionUpdateHarga" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                    <div id="optionUpdateHargaContentModal"></div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('assets/js/suma/option/updateharga.js') }}?v={{ time() }}"></script>
@endpush
