$(document).ready(function () {
    // ===============================================================
    // Daftar Cetak Ulang
    // ===============================================================
    function loadDataCetakUlang(page = 1, per_page = 1, year = data.year, month = data.month) {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year='+year+'&month='+month+'&per_page='+per_page+'&page='+page;
    }

    $('#selectPerPage').change(function() {
        var start_record = data.start_record;
        var per_page = $('#selectPerPage').val();
        var page = Math.ceil(start_record / per_page);
        var year = data.year;
        var month = data.month;

        loading.block();
        loadDataCetakUlang(page, per_page, year, month);
    });

    $(document).on('click', '.page-item a', function (e) {
        var page_link = $(this)[0].getAttribute('data-page');
        var page = page_link.split('?page=')[1];

        var per_page = $('#selectPerPage').val();
        var year = data.year;
        var month = data.month;

        loading.block();
        loadDataCetakUlang(page, per_page, year, month);
    });

    // ===============================================================
    // Form Cetak Ulang
    // ===============================================================
    function clearDataModal() {
        $('#selectDivisi').val('HONDA').change();
        $('#selectTransaksi').val('FAKTUR');
        $('#inputNomorDokumen').val('');
        $('#inputKodeCabang').val('');
        $('#inputCompanyCabang').val('');
        $('#inputKeterangan').val('');
        $('#inputAlasan').val('');
        $('#selectStatusApprove').val(1).change();
        $('#selectStatusEdit').val(0).change();

        $('#selectStatusApprove').addClass('form-control-solid');
        $('#selectStatusEdit').addClass('form-control-solid');

        $('#selectStatusApprove').attr('disabled', true);
        $('#selectStatusEdit').attr('disabled', true);
    }

    $('#modalEntryCetakUlang').on('shown.bs.modal', function() {
        $('#formEntryCetakUlang').focus();
        $('#modalEntryCetakUlang').find('input').on('keydown', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                var index = $('#modalEntryCetakUlang').find('input').index(this) + 1;
                $('#modalEntryCetakUlang').find('input').eq(index).focus();
            }
        });
    });

    $('#btnTambah').on('click', function() {
        clearDataModal();
        $('#modalEntryCetakUlang').on('shown.bs.modal', function () {
            $('#inputNomorDokumen').focus();
        });
    });

    $('#selectDivisi').on('change', function() {
        $('#inputNomorDokumen').val('');
        $('#inputKodeCabang').val('');
        $('#inputCompanyCabang').val('');
        $('#inputKeterangan').val('');
        $('#inputAlasan').val('');
        $('#selectStatusApprove').val(1).change();
        $('#selectStatusEdit').val(0).change();

        $('#selectStatusApprove').addClass('form-control-solid');
        $('#selectStatusEdit').addClass('form-control-solid');

        $('#selectStatusApprove').attr('disabled', true);
        $('#selectStatusEdit').attr('disabled', true);
    });

    $('#selectTransaksi').on('change', function() {
        $('#inputNomorDokumen').val('');
        $('#inputKodeCabang').val('');
        $('#inputCompanyCabang').val('');
        $('#inputKeterangan').val('');
        $('#inputAlasan').val('');
        $('#selectStatusApprove').val(1).change();
        $('#selectStatusEdit').val(0).change();

        $('#selectStatusApprove').addClass('form-control-solid');
        $('#selectStatusEdit').addClass('form-control-solid');

        $('#selectStatusApprove').attr('disabled', true);
        $('#selectStatusEdit').attr('disabled', true);
    });

    $('#inputNomorDokumen').on('change', function() {
        var divisi = $('#selectDivisi').val();
        var nomor_dokumen = $('#inputNomorDokumen').val();
        var jenis_transaksi = $('#selectTransaksi').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: url.cek_dokumen,
            method: 'post',
            data: { nomor_dokumen: nomor_dokumen, jenis_transaksi: jenis_transaksi,
                    divisi: divisi, _token: _token },

            success: function (response) {
                loading.release();
                if (response.status == true) {
                    $('#inputNomorDokumen').val(response.data.no_transaksi);
                    $('#inputKodeCabang').val(response.data.kode_cabang);
                    $('#inputCompanyCabang').val(response.data.companyid);
                    $('#inputKeterangan').val(response.data.informasi);

                    $('#selectStatusApprove').addClass('form-control-solid');
                    $('#selectStatusApprove').attr('disabled', true);

                    $('#selectStatusEdit').addClass('form-control-solid');
                    $('#selectStatusEdit').attr('disabled', true);

                    if(jenis_transaksi == 'FAKTUR') {
                        if(response.data.kantor_pusat == 0) {
                            $('#selectStatusEdit').removeClass('form-control-solid');
                            $('#selectStatusEdit').attr('disabled', false);
                        }

                        if(response.data.kantor_pusat == 1) {
                            if(response.data.status_approve == 1) {
                                $('#selectStatusApprove').removeClass('form-control-solid');
                                $('#selectStatusApprove').attr('disabled', false);
                            }
                        }
                    }
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: 'warning',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-warning'
                        }
                    });
                }
            },
            error: function () {
                loading.release();
                console.log('ERROR');
            }
        })
    });
});
