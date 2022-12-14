
$(document).ready(function () {
    $('ul.pagination').hide();
    $(function () {
        $('.scrolling-pagination').jscroll({
            autoTrigger: true,
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.scrolling-pagination',
            callback: function () {
                $('ul.pagination').remove();
            }
        });
    });

    $('#modalEntryPartNumber').modal({
        backdrop: 'static', keyboard: false
    });

    // $("form").bind("keypress", function (e) {
    //     if (e.keyCode == 13) {
    //         return false;
    //     }
    // });

    loadDetailPurchaseOrder();



    var tablePartNumber = $('#tableSearchPartNumber').DataTable();

    function loadDataPartNumber() {
        part_number = $('#modalPofPartInputPartNumber').val();

        tablePartNumber.destroy();
        tablePartNumber = $("#tableSearchPartNumber").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url_route.option_part_number,
                data: { part_number: part_number }
            },
            columns: [
                { data: 'part_number', name: 'part_number', render: function (data) { return '<span class="text-gray-700 fw-bold fs-6">' + data + '</span>' } },
                { data: 'description', name: 'description', render: function (data) { return '<span class="text-gray-700 fw-bold fs-6">' + data + '</span>' } },
                { data: 'produk', name: 'produk', render: function (data) { return '<span class="text-gray-700 fw-bold fs-6">' + data + '</span>' } },
                { data: 'het', name: 'het', render: function (data) { return '<span class="text-gray-700 fw-bold fs-6">' + data + '</span>' } },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

    function clearModalPofPart() {
        $('#modalPofPartInputPartNumber').val('');
        $('#modalPofPartInputNamaPart').val('');
        $('#modalPofPartInputProduk').val('');
        $('#modalPofPartInputJmlOrder').val(1);
        $('#modalPofPartInputHarga').val(0);
        $('#modalPofPartInputDiscount').val(0);
    }

    $('input').on('keydown', function (e) {
        var keyCode = e.keyCode || e.which;
        if (e.keyCode === 13) {
            e.preventDefault();
            $('input')[$('input').index(this) + 1].focus();
        }
    });

    $('#modalPofPartInputPartNumber').blur(function () {
        var part_number = $('#modalPofPartInputPartNumber').val();
        var _token = $('input[name="_token"]').val();

        if (part_number == "") {
            clearModalPofPart();
        } else {
            if (part_number.includes('?')) {
                loadDataPartNumber();
                $('#modalTitlePartNumber').html("Pilih Data Part Number");
                $('#partNumberSearchForm').trigger('reset');
                $('#partNumberSearchModal').modal('show');
            } else {
                $.ajax({
                    url: url_route.validasi_part_number,
                    method: "POST",
                    dataType: "JSON",
                    data: { part_number: part_number, _token: _token },

                    success: function (response) {
                        if (response.status == false) {
                            $('#messageErrorPofPart').html('<div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row p-5 mb-10">' +
                                '<span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">' +
                                '<path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="currentColor"></path>' +
                                '<path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="currentColor"></path>' +
                                '</svg>' +
                                '</span>' +
                                '<div class="d-flex flex-column text-light pe-0 pe-sm-10">' +
                                '<h4 class="mb-2 text-light">Informasi</h4>' +
                                '<span>' + response.message + '</span>' +
                                '</div>' +
                                '<button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">' +
                                '<span class="svg-icon svg-icon-2x svg-icon-light">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">' +
                                '<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>' +
                                '<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>' +
                                '</svg>' +
                                '</span>' +
                                '</button>' +
                                '</div>');
                            clearModalPofPart();

                            $('#modalPofPartInputPartNumber').focus();
                        } else {
                            $('#messageErrorPofPart').html('');
                            $('#modalPofPartInputPartNumber').val(response.data.part_number);
                            $('#modalPofPartInputNamaPart').val(response.data.description);
                            $('#modalPofPartInputProduk').val(response.data.produk);
                            $('#modalPofPartInputHarga').val(response.data.het);
                            $('#modalPofPartInputJmlOrder').focus();
                        }
                    }
                })
            }
        }
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

    $('body').on('click', '#btnTambahPartNumber', function () {
        var tpc = $('#selectKodeTpc option:selected').val();
        $('#modalEntryPartTitle').html("Tambah Part Number");

        clearModalPofPart();

        $('#modalEntryPartNumber').trigger('reset');
        $('#modalPofPartInputPartNumber').attr('readonly', false);
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
        var nomor_pof = $('#inputNomorPof').val();;
        var part_number = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: url_route.purchase_order_form_edit_part,
            method: "POST",
            data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

            success: function (response) {
                if (response.status == true) {
                    $('#modalEntryPartTitle').html("Edit Part Number");

                    $('#modalPofPartInputPartNumber').val(response.data.part_number);
                    $('#modalPofPartInputNamaPart').val(response.data.nama_part);
                    $('#modalPofPartInputProduk').val(response.data.produk);
                    $('#modalPofPartInputJmlOrder').val(response.data.jml_order);
                    $('#modalPofPartInputHarga').val(response.data.harga);
                    $('#modalPofPartInputDiscount').val(response.data.disc_detail);

                    $('#modalPofPartInputPartNumber').attr('readonly', true);
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
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
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
                $.ajax({
                    url: url_route.purchase_order_form_hapus_part,
                    method: "POST",
                    data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

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
                            loadDetailPurchaseOrder();
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

        $.ajax({
            url: url_route.purchase_order_form_simpan_part,
            method: "POST",
            data: {
                nomor_pof: nomor_pof, part_number: part_number, jml_order: jml_order,
                harga: harga, discount: discount, _token: _token
            },

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
                    $('#modalEntryPartNumber').modal('hide');
                    loadDetailPurchaseOrder();
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
            }
        })
    });

    $('body').on('click', '#btnEditPofDiscount', function () {
        var nomor_pof = $('#inputNomorPof').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: url_route.purchase_order_form_edit_discount,
            method: "POST",
            data: { nomor_pof: nomor_pof, _token: _token },

            success: function (response) {
                if (response.status == true) {
                    $('#modalDiscountTitle').html("Edit Discount");
                    $('#modalPofDiscountInputDiscount').val(response.data.discount);

                    $('#modalEntryDiscount').modal('show');

                    $('#modalEntryDiscount').on('shown.bs.modal', function () {
                        $("#modalPofDiscountInputDiscount").focus();
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
            }
        })
    });

    $('body').on('click', '#btnSimpanPofDiscount', function () {
        var nomor_pof = $('#inputNomorPof').val();
        var discount = $('#modalPofDiscountInputDiscount').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: url_route.purchase_order_form_update_discount,
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
                    loadDetailPurchaseOrder();
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
            }
        })
    });

    $('#selectKodeTpc').change(function () {
        var nomor_pof = $('#inputNomorPof').val();
        var tpc = $('#selectKodeTpc option:selected').val();
        var _token = $('input[name="_token"]').val();

        event.preventDefault();
        const url = $(this).attr('href');

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
                    url: url_route.purchase_order_form_update_tpc,
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
                            loadDetailPurchaseOrder();
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
                            if (tpc_sebelumnya == '14') {
                                $("#selectKodeTpc").html('<option value="14" selected>14</option>' +
                                    '<option value="20">20</option>');
                            } else {
                                $("#selectKodeTpc").html('<option value="14">14</option>' +
                                    '<option value="20" selected>20</option>');
                            }
                        }
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
                    url: url_route.purchase_order_form_batal_approve,
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
                                    window.location.href = window.location.href;
                                }
                            })
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
            url: url_route.purchase_order_form_terlayani,
            method: "POST",
            data: { nomor_pof: nomor_pof, part_number: part_number, _token: _token },

            success: function (response) {
                if (response.status == true) {
                    list_faktur = response.data;
                    $('#modalListFaktur').html(list_faktur);
                    $('#FormPofTerlayani').trigger('reset');
                    $('#modalPofPartTerlayani').modal('show');
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
            }
        })
    });
});
