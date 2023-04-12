// jika terdapat ajax maka loading
$(document).ajaxStart(function () {
    loading.block();
});
// ajax selesai maka loading di release
$(document).ajaxStop(function () {
    loading.release();
});
$(document).ready(function () {

    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');

    $('#modalEntryPartNumber').modal({
        backdrop: 'static', keyboard: false
    });

    $('input').on('keydown', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            $('input')[$('input').index(this) + 1].focus();
        }
    });

    $('#modalPofDiscountInputDiscount').change(function() {
        if($('#modalPofDiscountInputDiscount').val() == '') {
            $('#modalPofDiscountInputDiscount').val(0);
        }
        $('#modalPofDiscountInputDiscount').val(Number($('#modalPofDiscountInputDiscount').val()).toFixed(2));
    });

    $('body').on('click', '#selectPartNumber', function (e) {
        e.preventDefault();
        var part_number = $(this).data('part_number');
        var description = $(this).data('description');
        var produk = $(this).data('produk');
        var het = $(this).data('het');

        $('#modalPofPartInputPartNumber').val(part_number);
        $('#modalPofPartInputNamaPart').val(description);
        $('#modalPofPartInputProduk').val(produk);
        $('#modalPofPartInputHarga').val(het);

        $('#partNumberSearchModal').modal('hide');
        $('#modalPofPartInputPartNumber').focus();
    });

    $('body').on('click', '#btnEditPofDiscount', function () {
        var nomor_pof = $('#inputNomorPof').val();
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url_route.pof_edit_discount,
            method: "POST",
            data: { nomor_pof: nomor_pof, _token: _token },

            success: function (response) {
                if (response.status == true) {
                    $('#modalDiscountTitle').html("Edit Discount");
                    $('#modalPofDiscountInputDiscount').val(Number(response.data.discount).toFixed(2));

                    $('#modalEntryDiscount').modal('show');

                    $('#modalEntryDiscount').on('shown.bs.modal', function () {
                        $("#modalPofDiscountInputDiscount").focus();
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
                Swal.fire({
                    text: 'Server tidak merespon, coba lagi',
                    icon: "danger",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        })
    });

    $('body').on('click', '#btnSimpanPofDiscount', function () {
        var nomor_pof = $('#inputNomorPof').val();
        var discount = $('#modalPofDiscountInputDiscount').val();
        var _token = $('input[name="_token"]').val();

        loading.block();
        $.ajax({
            url: url_route.pof_update_discount,
            method: "POST",
            data: { nomor_pof: nomor_pof, discount: discount, _token: _token },

            success: function (response) {
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
                    $('#modalEntryDiscount').modal('hide');
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
                Swal.fire({
                    text: 'Server tidak merespon, coba lagi',
                    icon: "danger",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        })
    });

    $('#selectKodeTpc').change(function (e) {
        e.preventDefault();
        var nomor_pof = $('#inputNomorPof').val();
        var tpc = $('#selectKodeTpc option:selected').val();
        var _token = $('input[name="_token"]').val();

        var tpc_sebelumnya = '19';

        if (tpc == '14') {
            tpc_sebelumnya = '20';
        } else {
            tpc_sebelumnya = '14';
        }

        Swal.fire({
            html: `Apa anda yakin akan mengubah <strong>Kode TPC ` + tpc_sebelumnya + `</strong> akan
                                diubah menjadi <strong>Kode TPC `+ tpc + `</strong> ?`,
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
                $.ajax({
                    url: url_route.pof_update_tpc,
                    method: "POST",
                    data: {
                        nomor_pof: nomor_pof, tpc: tpc, _token: _token
                    },
                    success: function (response) {
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
                            if (tpc_sebelumnya == '14') {
                                $("#selectKodeTpc").html('<option value="14" selected>14</option>' +
                                    '<option value="20">20</option>');
                            } else {
                                $("#selectKodeTpc").html('<option value="14">14</option>' +
                                    '<option value="20" selected>20</option>');
                            }
                        }
                    },
                    error: function() {
                        Swal.fire({
                            text: 'Server tidak merespon, coba lagi',
                            icon: "danger",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    }
                })
            } else {
                if (tpc_sebelumnya == '14') {
                    $("#selectKodeTpc").html('<option value="14" selected>14</option>' +
                        '<option value="20">20</option>');
                } else {
                    $("#selectKodeTpc").html('<option value="14">14</option>' +
                        '<option value="20" selected>20</option>');
                }
            }
        });
    });

    $("#btnBatalApprovePof").click(function (e) {
        e.preventDefault();
        var nomor_pof = $('#inputNomorPof').val();
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apa anda yakin akan membatalkan <strong>Status Approve nomor pof ` + nomor_pof + `</strong> ini ?`,
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
                $.ajax({
                    url: url_route.pof_batal_approve,
                    method: "POST",
                    data: { nomor_pof: nomor_pof, _token: _token },

                    success: function (response) {
                        if (response.status == true) {
                            Swal.fire({
                                text: response.message,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-success"
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    loading.block();
                                    window.location.href = window.location.href;
                                }
                            })
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
                        Swal.fire({
                            text: 'Server tidak merespon, coba lagi',
                            icon: "danger",
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

    $('body').on('click', '#viewDetailPofTerlayani', function () {
        var nomor_pof = $('#inputNomorPof').val();
        var part_number = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        $('#modalTitle').html(nomor_pof);
        $('#modalSubTitle').html(part_number);

        $.ajax({
            url: url_route.pof_terlayani,
            method: "POST",
            data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

            success: function (response) {
                loading.release();
                if (response.status == true) {
                    list_faktur = response.data;
                    $('#modalListFaktur').html(list_faktur);
                    $('#FormPofTerlayani').trigger('reset');
                    $('#modalPofPartTerlayani').modal('show');
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
               Swal.fire({
                    text: 'Server tidak merespon, coba lagi',
                    icon: "danger",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        })
    });

    // modalEntryPartNumber modalPofPartBtnPartNumber on click
    $('#modalEntryPartNumber #modalPofPartBtnPartNumber').click(function (e) {
        // show modal modalOptionPartNumber
        $('#modalOptionPartNumber').modal('show');
        loadDataPartNumber(1,10,'');

    });
});
