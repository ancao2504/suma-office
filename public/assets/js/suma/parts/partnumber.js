$(document).ready(function () {
    // ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, group_level = '', group_produk = '', type_motor = '', part_number = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?group_level=' + group_level.trim() + '&group_produk=' + group_produk.trim() +
            '&type_motor=' + type_motor.trim() + '&part_number=' + part_number.trim() + '&per_page=' + per_page + '&page=' + page;
    }

    $('#selectPerPageMasterData').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPageMasterData').val();
        var group_level = $('#selectFilterGroupLevel').val();
        var group_produk = $('#inputFilterKodeProduk').val();
        var type_motor = $('#inputFilterTipeMotor').val();
        var part_number = $('#inputFilterPartNumber').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, group_level, group_produk, type_motor, part_number);
    });

    $(document).on('click', '#paginationMasterData .page-item a', function () {
        var page = $(this)[0].getAttribute('data-page');
        var per_page = $('#selectPerPageMasterData').val();
        var group_level = $('#selectFilterGroupLevel').val();
        var group_produk = $('#inputFilterKodeProduk').val();
        var type_motor = $('#inputFilterTipeMotor').val();
        var part_number = $('#inputFilterPartNumber').val();

        loadMasterData(page, per_page, group_level, group_produk, type_motor, part_number);
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $(document).on('keydown', '#formFilter', function(event) {
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();

        $('#inputFilterTipeMotor').val(data_filter.type_motor);
        $('#selectFilterGroupLevel').val(data_filter.kode_level).trigger("change");
        $('#inputFilterKodeProduk').val(data_filter.kode_produk);
        $('#inputFilterPartNumber').val(data_filter.part_number);

        $('#modalFilter').modal('show');
    });

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();
        var per_page = $('#selectPerPagePartNumber').val();
        var group_level = $('#selectFilterGroupLevel').val();
        var group_produk = $('#inputFilterKodeProduk').val();
        var type_motor = $('#inputFilterTipeMotor').val();
        var part_number = $('#inputFilterPartNumber').val();

        loadMasterData(1, per_page, group_level, group_produk, type_motor, part_number);
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        $('#inputFilterTipeMotor').val('');
        $('#selectFilterGroupProduk').prop('selectedIndex', 0).change();
        $('#selectFilterGroupLevel').prop('selectedIndex', 0).change();
        $('#inputFilterPartNumber').val('');
    });

    // ===============================================================
    // Filter Produk
    // ===============================================================
    $('#selectFilterGroupLevel').on('change', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val('');
    });

    $('#inputFilterKodeProduk').on('click', function (e) {
        e.preventDefault();

        var filterLevelProduk = $('#selectFilterGroupLevel').val();
        loadDataOptionProduk(1, 10, filterLevelProduk, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();

        var filterLevelProduk = $('#selectFilterGroupLevel').val();
        loadDataOptionProduk(1, 10, filterLevelProduk, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('body').on('click', '#optionProdukContentModal #selectedOptionProduk', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
        $('#modalOptionGroupProduk').modal('hide');
    });

    // ===============================================================
    // Filter Tipe Motor
    // ===============================================================
    $('#inputFilterTipeMotor').on('click', function (e) {
        e.preventDefault();

        loadDataOptionTipeMotor(1, 10, '');
        $('#formOptionTipeMotor').trigger('reset');
        $('#modalOptionTipeMotor').modal('show');
    });

    $('#btnFilterPilihTipeMotor').on('click', function (e) {
        e.preventDefault();

        loadDataOptionTipeMotor(1, 10, '');
        $('#formOptionTipeMotor').trigger('reset');
        $('#modalOptionTipeMotor').modal('show');
    });

    $('body').on('click', '#optionTipeMotorContentModal #selectedOptionTipeMotor', function (e) {
        e.preventDefault();
        $('#inputFilterTipeMotor').val($(this).data('kode'));
        $('#modalOptionTipeMotor').modal('hide');
    });

    // ===============================================================
    // Filter Part Number
    // ===============================================================
    $('#inputFilterPartNumber').keyup(function(e){
        e.preventDefault();
        if(e.keyCode == 13) {
            var per_page = $('#selectPerPagePartNumber').val();
            var group_level = $('#selectFilterGroupLevel').val();
            var group_produk = $('#inputFilterKodeProduk').val();
            var type_motor = $('#inputFilterTipeMotor').val();
            var part_number = $('#inputFilterPartNumber').val();

            loadMasterData(1, per_page, group_level, group_produk, type_motor, part_number);
        }
    });

    // ===============================================================
    // Form Part Number Cart
    // ===============================================================
    $('body').on('click', '#btnPartNumberCart', function () {
        var part_number = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.tambah_cart,
            method: "POST",
            data: { part_number: part_number, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    $('#formCartPartNumber').trigger('reset');
                    $('#modalPartNumberCartTitle').html(part_number);

                    $('#btnOrder').attr('disabled', false);
                    $('#btnOrder').show();
                    $('#modalPartNumberCartImages').html(response.view_images_part);
                    $('#modalPartNumberCartTextPartNumber').html(response.view_part_number);
                    $('#modalPartNumberCartTextDescription').html(response.view_nama_part);
                    $('#modalPartNumberCartTextProduk').html(response.view_produk);
                    $('#modalPartNumberCartTextHargaNetto').html(response.view_harga_netto);
                    $('#modalPartNumberCartTextDiscount').html(response.view_discount);
                    $('#modalPartNumberCartTextHet').html(response.view_het);
                    $('#modalPartNumberCartListTypeMotor').html(response.view_type_motor);
                    $('#modalPartNumberCartTextKeteranganBo').html(response.view_keterangan_bo);

                    $('#modalPartNumberCart').modal('show');

                    $('#modalPartNumberCart').on('shown.bs.modal', function () {
                        $("#modalPartNumberCartInputJumlahOrder").focus();
                    });
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                }
            },
            error: function () {
                loading.release();
                Swal.fire({
                    text: 'Server tidak merespon, coba lagi',
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        })
    });

    // ===============================================================
    // Proses Part Number Cart
    // ===============================================================
    $('#btnOrder').click(function (e) {
        e.preventDefault();

        var part_number = $('#modalPartNumberCartTextPartNumber').html();
        var jml_order = $('#modalPartNumberCartInputJumlahOrder').val();
        var _token = $('input[name="_token"]').val();

        if (jml_order == '' || jml_order <= 0) {
            Swal.fire({
                text: "Jumlah order harus lebih besar dari 0 (nol)",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        } else {
            loading.block();
            $.ajax({
                url: url.proses_cart,
                method: "POST",
                data: {
                    part_number: part_number, jumlah_order: jml_order, _token: _token
                },
                success: function (response) {
                    loading.release();

                    if (response.status == false) {
                        if (response.message == 'PILIH_DEALER') {
                            $('#modalPartNumberCart').modal('hide');
                            cekSalesmanDealerIndex();
                        } else {
                            Swal.fire({
                                text: response.message,
                                icon: "warning",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-warning"
                                }
                            });
                        }
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        });
                        $('#modalPartNumberCart').modal('hide');
                        estimasiTotalCart();
                    }
                },
                error: function () {
                    loading.release();
                    Swal.fire({
                        text: 'Server tidak merespon, coba lagi',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            })
        }
    });
});
