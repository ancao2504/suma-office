// dokumen ready
$(document).ready(function () {


    // jika terdapat form submit
    $('form').submit(function () {
        loading.block();
    });
    // end jika terdapat form submit

    // pagination scroll
    var pages = 1;

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                const params = new URLSearchParams(window.location.search)
                for (const param of params) {
                    var tipe_motor = params.get('tipe_motor');
                    var group_level = params.get('group_level');
                    var group_produk = params.get('group_produk');
                    var part_number = params.get('part_number');
                }
                pages++;
                loadMoreData(tipe_motor, group_level, group_produk, part_number, pages);
            }
        }
    });


    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    async function loadMoreData(tipe_motor, group_level, group_produk, part_number, pages) {
        loading.block();

        $.ajax({
            url: url.part_number,
            type: "get",
            data: {
                page: pages, tipe_motor: tipe_motor, group_level: group_level,
                group_produk: group_produk, part_number: part_number,
            },
            success: function (response) {
                if (response.html == '') {
                    $('#dataLoadPartNumber').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                    loading.release();
                    return;
                }
                $("#dataPartNumber").append(response.html);
                loading.release();
            },
            error: function () {
                loading.release();
                pages = pages - 1;

                Swal.fire({
                    text: "Gagal mengambil data ke dalam server, Coba lagi",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    }

    $('#btnFilterPartNumber').on('click', function (e) {
        e.preventDefault();

        $('#inputFilterTipeMotor').val(data_filter.tipe_motor);
        $("#selectFilterGroupLevel").val(data_filter.kode_level).trigger("change");
        $('#inputFilterKodeProduk').val(data_filter.kode_produk);
        $('#inputFilterPartNumber').val(data_filter.part_number);

        $('#modalFilter').modal('show');
    });

    $('#inputFilterKodeProduk').on('click', function (e) {
        e.preventDefault();

        var selectFilterGroupLevel = $('#selectFilterGroupLevel').val();
        loadDataProduk(1, 10, '', selectFilterGroupLevel);
        $('#searchProdukForm').trigger('reset');
        $('#produkSearchModal').modal('show');
    });

    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();

        var selectFilterGroupLevel = $('#selectFilterGroupLevel').val();
        loadDataProduk(1, 10, '', selectFilterGroupLevel);
        $('#searchProdukForm').trigger('reset');
        $('#produkSearchModal').modal('show');
    });

    $('body').on('click', '#produkContentModal #selectProduk', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
        $('#produkSearchModal').modal('hide');
    });

    $('#inputFilterTipeMotor').on('click', function (e) {
        e.preventDefault();

        loadDataTipeMotor();
        $('#searchTipeMotorForm').trigger('reset');
        $('#tipeMotorSearchModal').modal('show');
    });

    $('#btnFilterPilihTipeMotor').on('click', function (e) {
        e.preventDefault();

        loadDataTipeMotor();
        $('#searchTipeMotorForm').trigger('reset');
        $('#tipeMotorSearchModal').modal('show');
    });

    $('body').on('click', '#tipeMotorContentModal #selectTipeMotor', function (e) {
        e.preventDefault();
        $('#inputFilterTipeMotor').val($(this).data('kode'));
        $('#tipeMotorSearchModal').modal('hide');
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        $('#inputFilterTipeMotor').val('');
        $('#selectFilterGroupProduk').prop('selectedIndex', 0).change();
        $('#selectFilterGroupLevel').prop('selectedIndex', 0).change();
        $('#inputFilterPartNumber').val('');
    });

    $('body .addToCart').on('click', function () {
        var part_number = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url.view_cart_part_number,
            method: "POST",
            data: { part_number: part_number, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    $('#modalTitle').html(part_number);

                    $('#btnOrder').attr('disabled', false);
                    $('#btnOrder').show();
                    $('#modalImgParts').html(response.view_images_part);
                    $('#modalTextPartNumber').html(response.view_part_number);
                    $('#modalTextDescription').html(response.view_nama_part);
                    $('#modalTextProduk').html(response.view_produk);
                    $('#modalTextHargaNetto').html(response.view_harga_netto);
                    $('#modalTextDiscount').html(response.view_discount);
                    $('#modalTextHet').html(response.view_het);
                    $('#modalListTypeMotor').html(response.view_type_motor);
                    $('#modalTextKeteranganBo').html(response.view_keterangan_bo);

                    $('#modalPartNumberCart').modal('show');

                    $('#modalPartNumberCart').on('shown.bs.modal', function () {
                        $("#inputJumlahOrder").focus();
                    });
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            },
            error: function () {
                loading.release();
            }
        })
    });

    $('#btnOrder').click(function (e) {
        e.preventDefault();

        var part_number = $('#modalTextPartNumber').html();
        var jml_order = $('#inputJumlahOrder').val();
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
                url: url.add_cart_part_number,
                method: "POST",
                data: {
                    part_number: part_number, jumlah_order: jml_order, _token: _token
                },
                success: function (response) {
                    loading.release();

                    if (response.status == false) {
                        if (response.message == 'PILIH_DEALER') {
                            $('#modalPartNumberCart').modal('hide');
                            openModalSalesDealer();
                        } else {
                            Swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-danger"
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
                        getInfoDataCart();
                    }
                },
                error: function () {
                    loading.release();
                }
            })
        }
    });
});
