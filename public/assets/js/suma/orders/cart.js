// dokumen ready
$(document).ready(function () {
    var harga = document.getElementById("modalEditCartInputHarga");
    harga.addEventListener("keyup", function (e) {
        harga.value = formatRupiah(this.value, "");
    });

    function formatRupiah(nominal, prefix) {
        var number_string = nominal.replace(/[^.\d]/g, "").toString(),
            split = number_string.split("."),
            sisa = split[0].length % 3,
            angka = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? "," : "";
            angka += separator + ribuan.join(",");
        }

        angka = split[1] != undefined ? angka + "." + split[1] : angka;
        return prefix == undefined ? angka : angka ? "" + angka : "";
    }

    var discount = document.getElementById("modalEditCartInputDiscount");
    discount.addEventListener("keyup", function (e) {
        discount.value = formatDiscount(this.value, "");
    });

    var discountPlus = document.getElementById("modalEditCartInputDiscountPlus");
    discountPlus.addEventListener("keyup", function (e) {
        discountPlus.value = formatDiscount(this.value, "");
    });

    var btnSimpanHeader = document.querySelector("#btnSimpanHeaderCart");
    btnSimpanHeader.addEventListener("click", function (e) {
        e.preventDefault();
        loading.block();
        document.getElementById("formModalCartHeader").submit();
    });

    var btnTambahCart = document.querySelector("#btnTambahCart");
    btnTambahCart.addEventListener("click", function (e) {
        loading.block();
    });

    function formatDiscount(nominal, prefix) {
        var number_string = nominal.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 2,
            angka = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{2}/gi);

        if (ribuan) {
            separator = sisa ? "." : "";
            angka += separator + ribuan.join(".");
        }

        angka = split[1] != undefined ? angka + "." + split[1] : angka;
        return prefix == undefined ? angka : angka ? "" + angka : "";
    }

    $('form').on('keydown', 'input, select', function (e) {
        if (e.key === "Enter") {
            var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
            focusable = form.find('input,a,select,textarea').filter(':visible:not([readonly]):enabled');
            next = focusable.eq(focusable.index(this) + 1);

            if (next.length) {
                next.focus();
            }
            return false;
        }
    });

    var xInputKeyUser = document.getElementById("inputKodeCart").value;

    function daftarDetailCart() {
        loading.block();
        $.ajax({
            url: url_route.daftar_cart_detail,
            method: "GET",

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    $('#tableDetailCart').html(response.data);
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
    }

    daftarDetailCart();

    $('#btnEditCart').click(function () {
        var salesman = $('#inputViewSalesman').val();
        var dealer = $('#inputViewDealer').val();
        var back_order = $('#inputViewBackOrder').val();
        var back_order_val = 'B';
        if (back_order == 'BO') {
            back_order_val = 'B';
        } else {
            back_order_val = 'T';
        }
        var keterangan = $('#inputViewKeterangan').val();

        $('#inputSalesman').val(salesman);
        $('#inputDealer').val(dealer);
        $('#selectBo option[value="' + back_order_val + '"]').prop('selected', true);
        $('#inputKeterangan').val(keterangan);

        $('#modalCartHeader').modal({ backdrop: 'static', keyboard: false });
        $('#modalCartHeader').modal('show');
    });

    $('body').on('click', '#btnPilihSalesman', function (e) {
        e.preventDefault();
        loadDataSalesman();
        $('#searchSalesmanForm').trigger('reset');
        $('#salesmanSearchModal').modal('show');
    });

    $('body').on('click', '#salesmanContentModal #selectSalesman', function (e) {
        e.preventDefault();
        $('#inputSalesman').val($(this).data('kode_sales'));
        $('#inputDealer').val('');
        $('#salesmanSearchModal').modal('hide');
    });

    $('#btnPilihDealer').on('click', function (e) {
        e.preventDefault();

        var salesman = $('#inputSalesman').val();

        if (salesman == null || salesman == '') {
            Swal.fire({
                text: response.message,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        } else {
            loadDataDealerSalesman(salesman, 1, 10, '');
            $('#searchDealerSalesmanForm').trigger('reset');
            $('#dealerSalesmanSearchModal').modal('show');
        }
    });

    $('body').on('click', '#dealerSalesmanContentModal #selectDealerSalesman', function (e) {
        e.preventDefault();
        $('#inputDealer').val($(this).data('kode_dealer'));
        $('#dealerSalesmanSearchModal').modal('hide');
    });

    $('body').on('click', '#btnEditPartCart', function () {
        var part_number = $(this).data('kode');
        var tpc = $('#selectKodeTpc').val();
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url_route.cart_detail_edit,
            method: "POST",
            data: { part_number: part_number, _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == true) {
                    $('#modalTitle').html("Edit Data Cart");
                    var picture = response.data.images;
                    var part = '<center><img src="' + picture + '" class="overlay-layer card-rounded bg-gray-400 bg-opacity-25 img-thumbnail" onerror="this.onerror=null; this.src=' + image_notfound + ';"' +
                        'style="width: auto;height: 200px;">' +
                        '</center>';
                    $('#modalEditCartPicturePart').html(part);
                    $('#modalEditCartInputPartNumber').val(response.data.part_number);
                    $('#modalEditCartInputProduk').val(response.data.produk);
                    $('#modalEditCartInputDescription').val(response.data.description);
                    $("#modalEditCartSelectTpc").val(response.data.tpc).trigger("change");
                    $('#modalEditCartInputJmlOrder').val(response.data.jml_order);
                    $('#modalEditCartInputHarga').val(formatRupiah(response.data.harga.toString(), ""));
                    $('#modalEditCartInputDiscount').val(formatDiscount(response.data.disc1.toString(), ""));
                    $('#modalEditCartInputDiscountPlus').val(formatDiscount(response.data.disc2.toString(), ""));

                    if (response.data.tpc == 14) {
                        $('#modalEditCartInputHarga').attr('readonly', 'true');
                        $('#modalEditCartInputHarga').addClass('form-control-solid');

                        $('#modalEditCartInputDiscount').attr('readonly', 'false');
                        $('#modalEditCartInputDiscount').removeClass('form-control-solid');

                        $('#modalEditCartInputDiscountPlus').attr('readonly', 'false');
                        $('#modalEditCartInputDiscountPlus').removeClass('form-control-solid');
                    } else {
                        $('#modalEditCartInputHarga').attr('readonly', 'false');
                        $('#modalEditCartInputHarga').removeClass('form-control-solid');

                        $('#modalEditCartInputDiscount').attr('readonly', 'true');
                        $('#modalEditCartInputDiscount').addClass('form-control-solid');

                        $('#modalEditCartInputDiscountPlus').attr('readonly', 'true');
                        $('#modalEditCartInputDiscountPlus').addClass('form-control-solid');
                    }

                    $('#modalCartDetail').modal('show');
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

    $('#modalEditCartSelectTpc').on('change', function () {
        loading.block();

        var part_number = $('#modalEditCartInputPartNumber').val();
        var tpc = this.value;
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: url_route.cart_detail_edit,
            method: "POST",
            data: { part_number: part_number, _token: _token },

            success: function (response) {
                loading.release();

                if (tpc == 14) {
                    $('#modalEditCartInputHarga').val(formatRupiah(response.data.het.toString(), ""));
                    $('#modalEditCartInputDiscount').val(formatDiscount(response.data.disc1.toString(), ""));
                    $('#modalEditCartInputDiscountPlus').val(formatDiscount(response.data.disc2.toString(), ""));

                    $('#modalEditCartInputHarga').attr('readonly', 'true');
                    $('#modalEditCartInputHarga').addClass('form-control-solid');

                    $('#modalEditCartInputDiscount').removeAttr('readonly');
                    $('#modalEditCartInputDiscount').removeClass('form-control-solid');

                    $('#modalEditCartInputDiscountPlus').removeAttr('readonly');
                    $('#modalEditCartInputDiscountPlus').removeClass('form-control-solid');
                } else {
                    $('#modalEditCartInputHarga').val(formatRupiah(response.data.harga.toString(), ""));
                    $('#modalEditCartInputDiscount').val(0);
                    $('#modalEditCartInputDiscountPlus').val(0);

                    $('#modalEditCartInputHarga').removeAttr('readonly');
                    $('#modalEditCartInputHarga').removeClass('form-control-solid');

                    $('#modalEditCartInputDiscount').attr('readonly', 'true');
                    $('#modalEditCartInputDiscount').addClass('form-control-solid');

                    $('#modalEditCartInputDiscountPlus').attr('readonly', 'true');
                    $('#modalEditCartInputDiscountPlus').addClass('form-control-solid');
                }
            },
            error: function () {
                loading.release();
            }
        });
    });

    $('#btnSimpanDetailCart').click(function (e) {
        var part_number = $('#modalEditCartInputPartNumber').val();
        var tpc = $('#modalEditCartSelectTpc').val();
        var jml_order = $('#modalEditCartInputJmlOrder').val();
        var harga = $('#modalEditCartInputHarga').val();
        var discount = $('#modalEditCartInputDiscount').val();
        var discount_plus = $('#modalEditCartInputDiscountPlus').val();
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url_route.insert_cart_detail,
            method: "POST",
            data: {
                part_number: part_number, tpc: tpc, jml_order: jml_order, harga: harga,
                discount: discount, discount_plus: discount_plus, _token: _token
            },
            success: function (response) {
                loading.release();

                if (response.status == false) {
                    Swal.fire({
                        text: response.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                    $('#modalCartDetail').modal('hide');
                    daftarDetailCart();
                }
            },
            error: function () {
                loading.release();
            }
        });
    });
    $('body').on('click', '#btnDeletePartCart', function (event) {
        var part_number = $(this).data("kode");
        var _token = $('input[name="_token"]').val();

        event.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            html: `Data yang <strong>dihapus</strong> tidak dapat dikembalikan.
                                Apa anda yakin akan <strong>menghapus data</strong> part number
                                <strong>`+ part_number + `</strong> ?`,
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: 'No',
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: 'btn btn-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                loading.block();
                $.ajax({
                    url: url_route.delete_cart_detail,
                    method: "POST",
                    data: { part_number: part_number, _token: _token },

                    success: function (response) {
                        loading.release();

                        if (response.status == true) {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                }
                            });
                            daftarDetailCart();
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
            }
        });
    });

    $('#btnResetCart').click(function (e) {
        var _token = $('input[name="_token"]').val();
        e.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            html: `Data yang telah <strong>di hapus tidak dapat dikembalikan lagi.</strong>
                                Apa anda yakin akan <strong>mengosongkan data cart anda</strong> ?`,
            icon: "info",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: 'No',
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: 'btn btn-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                loading.block();

                $.ajax({
                    url: url_route.reset_cart,
                    method: "POST",
                    data: { _token: _token },

                    success: function (response) {
                        loading.release();

                        if (response.status == true) {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                }
                            });
                            daftarDetailCart();
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
            }
        });
    });

    $('#btnImportExcelCart').click(function (e) {
        var salesman = $('#inputViewSalesman').val();
        var dealer = $('#inputViewDealer').val();

        $('#inputImportCartSalesman').val(salesman);
        $('#inputImportCartDealer').val(dealer);
        $('#optionPartNumber').prop('checked', true);
        $('#selectFileExcel').val('');
        $('#modalCartImportExcel').modal('show');
    });

    $('#btnImportExcelCartProses').click(function (e) {
        e.preventDefault();
        var salesman = $('#inputViewSalesman').val();
        var dealer = $('#inputViewDealer').val();
        var fileExcel = $('#selectFileExcel').val();


        if (salesman == '' || dealer == '') {
            Swal.fire({
                text: "Kode sales dan kode dealer tidak boleh kosong",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        } else if (fileExcel == '') {
            Swal.fire({
                text: "Pilih file excel yang akan di import terlebih dahulu",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        } else {
            loading.block();
            var formData = new FormData(document.getElementById("formModalCartImportExcel"));
            $.ajax({
                type: 'POST',
                url: url_route.import_excel_cart,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,

                success: function (response) {
                    loading.release();

                    if (response.status == false) {
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } else {
                        if (response.message == 'SUCCESS_WITH_MESSAGE') {
                            daftarDetailCart();
                            $('#modalCartImportExcel').modal('hide');

                            $('#tableImportExcelResult').html('');
                            $('#tableImportExcelResult').html(response.data);
                            $("#tableImportExcelResultCart").DataTable();
                            $('#modalCartImportExcelResult').modal('show');

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

                            daftarDetailCart();
                            $('#modalCartImportExcel').modal('hide');
                        }
                    }
                },
                error: function () {
                    loading.release();
                }
            });
        }
    });

    $('#btnSampleExcelCart').click(function (e) {
        $('#modalCartSampleExcel').modal('show');
    });

    $('#btnCheckOut').click(function (e) {
        e.preventDefault();
        loading.block();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: url_route.cart_check_out_cek_aturan_harga,
            method: "POST",
            data: { _token: _token },

            success: function (response) {
                loading.release();

                if (response.status == false) {
                    Swal.fire({
                        text: response.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                } else {
                    loading.release();

                    if (response.message == 'STATUS_OK') {
                        $('#modalCartCheckOut').modal('show');
                    } else {
                        Swal.fire({
                            html: `Harga cart kurang dari <strong>Rp. 500,000</strong>. Apakah anda tetap
                                                akan melanjutkan transaksi ini ?`,
                            icon: "info",
                            buttonsStyling: false,
                            showCancelButton: true,
                            confirmButtonText: "Yes",
                            cancelButtonText: 'No',
                            customClass: {
                                confirmButton: "btn btn-danger",
                                cancelButton: 'btn btn-primary'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalCartCheckOut').modal('show');
                            }
                        });
                    }
                }
            },
            error: function () {
                loading.release();
            }
        });
    });
});
