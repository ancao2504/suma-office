function loadDetailPurchaseOrderForm() {
    $nomor_pof = $('#inputNomorPof').val();

    $.ajax({
        url: url_route.pof_detail_daftar,
        method: 'get',
        data: { nomor_pof: $nomor_pof.trim() },

        success: function (response) {
            loading.release();
            $('#daftarDetailPurchaseOrderForm').html(response.data);
        },
        error: function () {
            loading.release();
            Swal.fire({
                text: 'Terjadi kesalahan pada program, lakukan refresh halaman',
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

$(document).ready(function () {
    loadDetailPurchaseOrderForm();

    $('#modalPofPartInputJmlOrder').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        aForm: true,
        vMax: '9999999999999',
        vMin: '0'
    });

    $('#modalPofPartInputHarga').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        aForm: true,
        vMax: '9999999999999',
        vMin: '0'
    });

    function clearModalPofPart() {
        $('#modalPofPartInputPartNumber').val('');
        $('#modalPofPartInputNamaPart').val('');
        $('#modalPofPartInputProduk').val('');
        $('#modalPofPartInputJmlOrder').val(1);
        $('#modalPofPartInputHarga').val(0);
        $('#modalPofPartInputDiscount').val(Number(0).toFixed(2));
    }

    $('#modalPofPartBtnPartNumber').on('click', function (e) {
        e.preventDefault();
        loadDataPartNumber(1, 10, $('#modalPofPartInputPartNumber').val());
        $('#searchPartNumberForm').trigger('reset');
        $('#partNumberSearchModal').modal('show');
    });

    $('#modalPofPartInputPartNumber').on('click', function (e) {
        e.preventDefault();
        loadDataPartNumber(1, 10, $('#modalPofPartInputPartNumber').val());
        $('#searchPartNumberForm').trigger('reset');
        $('#partNumberSearchModal').modal('show');
    });

    $('#modalPofPartInputPartNumber').change(function () {
        var part_number = $('#modalPofPartInputPartNumber').val();
        var _token = $('input[name="_token"]').val();

        if (part_number == "") {
            clearModalPofPart();
        } else {
            loading.block();
            $.ajax({
                url: url_route.validasi_part_number,
                method: "POST",
                dataType: "JSON",
                data: { part_number: part_number, _token: _token },

                success: function (response) {
                    loading.release();
                    if (response.status == false) {
                        $('#messageErrorPofPart').html('<div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row p-5 mb-10">'+
                                '<span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">'+
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">'+
                                        '<path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"></path>'+
                                        '<path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"></path>'+
                                    '</svg>'+
                                '</span>'+
                                '<div class="d-flex flex-column text-light pe-0 pe-sm-10">'+
                                    '<h4 class="mb-2 text-light">Informasi</h4>'+
                                    '<span>'+response.message+'</span>'+
                                '</div>' +
                                '<button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">'+
                                    '<span class="svg-icon svg-icon-2x svg-icon-light">'+
                                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">'+
                                            '<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>'+
                                            '<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>'+
                                        '</svg>'+
                                    '</span>'+
                                '</button>'+
                            '</div>');

                        clearModalPofPart();
                    } else {
                        $('#messageErrorPofPart').html('');
                        $('#modalPofPartInputPartNumber').val(response.data.part_number.trim());
                        $('#modalPofPartInputNamaPart').val(response.data.description.trim());
                        $('#modalPofPartInputProduk').val(response.data.produk.trim());
                        $('#modalPofPartInputHarga').val(response.data.het.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
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



    $('#modalPofPartInputJmlOrder').change(function() {
        if($('#modalPofPartInputJmlOrder').val() == '') {
            $('#modalPofPartInputJmlOrder').val(0);
        }
    });

    $('#modalPofPartInputHarga').change(function() {
        if($('#modalPofPartInputHarga').val() == '') {
            $('#modalPofPartInputHarga').val(0);
        }
    });

    $('#modalPofPartInputDiscount').change(function() {
        if($('#modalPofPartInputDiscount').val() == '') {
            $('#modalPofPartInputDiscount').val(0);
        }
        $('#modalPofPartInputDiscount').val(Number($('#modalPofPartInputDiscount').val()).toFixed(2));
    });

    $('body').on('click', '#btnTambahPartNumber', function () {
        var tpc = $('#selectKodeTpc option:selected').val();
        $('#modalEntryPartTitle').html("Tambah Part Number");

        clearModalPofPart();

        $('#modalEntryPartNumber').trigger('reset');
        $('#modalPofPartBtnPartNumber').attr('disabled', false);
        $('#modalPofPartInputPartNumber').removeClass('form-control-solid');

        if (tpc == '14') {
            $('#modalPofPartInputHarga').attr('readonly', true);
            $('#modalPofPartInputDiscount').attr('readonly', false);

            $('#modalPofPartInputHarga').addClass('form-control-solid');
            $('#modalPofPartInputDiscount').removeClass('form-control-solid');
        } else {
            $('#modalPofPartInputHarga').attr('readonly', false);
            $('#modalPofPartInputDiscount').attr('readonly', true);

            $('#modalPofPartInputHarga').removeClass('form-control-solid');
            $('#modalPofPartInputDiscount').addClass('form-control-solid');
        }

        $('#modalEntryPartNumber').modal('show');

        $('#modalEntryPartNumber').on('shown.bs.modal', function () {
            $("#modalPofPartInputPartNumber").focus();
        });
    });

    $('body').on('click', '#btnEditPofPart', function () {
        var nomor_pof = $('#inputNomorPof').val();
        var part_number = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url_route.pof_detail_edit_part,
            method: "POST",
            data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

            success: function (response) {
                loading.release();
                if (response.status == true) {
                    $('#modalEntryPartTitle').html("Edit Part Number");

                    $('#modalPofPartInputPartNumber').val(response.data.part_number);
                    $('#modalPofPartInputNamaPart').val(response.data.nama_part);
                    $('#modalPofPartInputProduk').val(response.data.produk);
                    $('#modalPofPartInputJmlOrder').val(response.data.jml_order.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
                    $('#modalPofPartInputHarga').val(response.data.harga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','));
                    $('#modalPofPartInputDiscount').val(Number(response.data.disc_detail).toFixed(2));

                    $('#modalPofPartInputPartNumber').attr('readonly', true);
                    $('#modalPofPartBtnPartNumber').attr('disabled', true);
                    $('#modalPofPartInputPartNumber').addClass('form-control-solid');

                    if (response.data.tpc == '14') {
                        $('#modalPofPartInputHarga').attr('readonly', true);
                        $('#modalPofPartInputDiscount').attr('readonly', false);

                        $('#modalPofPartInputHarga').addClass('form-control-solid');
                        $('#modalPofPartInputDiscount').removeClass('form-control-solid');
                    } else {
                        $('#modalPofPartInputHarga').attr('readonly', false);
                        $('#modalPofPartInputDiscount').attr('readonly', true);

                        $('#modalPofPartInputHarga').removeClass('form-control-solid');
                        $('#modalPofPartInputDiscount').addClass('form-control-solid');
                    }

                    $('#modalEntryPartNumber').modal('show');

                    $('#modalEntryPartNumber').on('shown.bs.modal', function () {
                        $("#modalPofPartInputJmlOrder").focus();
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
            error: function() {
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

    $('body').on('click', '#btnDeletePofPart', function () {
        var nomor_pof = $('#inputNomorPof').val();;
        var part_number = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apa anda yakin akan menghapus part number <strong>` + part_number + `</strong> ?`,
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
                    url: url_route.pof_detail_hapus_part,
                    method: "POST",
                    data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

                    success: function (response) {
                        loading.release();
                        if (response.status == true) {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-success"
                                }
                            });
                            loadDetailPurchaseOrderForm();
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
                    error: function() {
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

    $('body').on('click', '#btnSimpanPofPart', function () {
        var nomor_pof = $('#inputNomorPof').val();
        var part_number = $('#modalPofPartInputPartNumber').val();
        var jml_order = $('#modalPofPartInputJmlOrder').val();
        var harga = $('#modalPofPartInputHarga').val();
        var discount = $('#modalPofPartInputDiscount').val();
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url_route.pof_detail_simpan_part,
            method: "POST",
            data: {
                nomor_pof: nomor_pof, part_number: part_number, jml_order: jml_order,
                harga: harga, discount: discount, _token: _token
            },
            success: function (response) {
                loading.release();
                if (response.status == true) {
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-success"
                        }
                    });
                    $('#modalEntryPartNumber').modal('hide');
                    loadDetailPurchaseOrderForm();
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

});
