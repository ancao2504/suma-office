function loadDaftarPartNumber(part_number = '') {
    if(part_number != '') {
        loading.block();
        $.ajax({
            url: window.location.href,
            method: "get",
            data: { part_number: part_number },

            success: function (response) {
                loading.release();
                if (response.status == true) {
                    $('#tableResultPartNumber').html(response.data);
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
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
                    text: 'Server Not Responding',
                    icon: "danger",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    }
}

function loadDataProductID(product_id = '') {
    var _token = $('input[name="_token"]').val();

    loading.block();
    $.ajax({
        url: url.cek_product_id,
        method: "post",
        data: { product_id: product_id, _token: _token },

        success: function (response) {
            loading.release();
            if (response.status == true) {
                $('#messageProductId').html(response.data);
            } else {
                Swal.fire({
                    text: response.message,
                    icon: "warning",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
                $('#messageProductId').html('');
            }
        },
        error: function () {
            loading.release();
            Swal.fire({
                text: 'Server Not Responding',
                icon: "warning",
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-warning"
                }
            });
        }
    });
}

$(document).ready(function () {
    $('#inputCariPartNumber').on('keypress',function(e) {
        if(e.which == 13) {
            var part_number =  $('#inputCariPartNumber').val();

            if(part_number == '' || part_number == null) {
                Swal.fire({
                    text: 'Isi data part number yang ingin dicari terlebih dahulu',
                    icon: "warning",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
            } else {
                loadDaftarPartNumber(part_number.trim());
            }
        }
    });

    $('#btnCariPartNumber').on('click',function(e) {
        var part_number =  $('#inputCariPartNumber').val();

        if(part_number == '' || part_number == null) {
            Swal.fire({
                text: 'Isi data part number yang ingin dicari terlebih dahulu',
                icon: "warning",
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
            });
        } else {
            loadDaftarPartNumber(part_number.trim());
        }
    });

    $('body').on('click', '#btnUpdateProductId', function (e) {
        e.preventDefault();
        var part_number = $(this).data('part_number');
        var product_id = $(this).data('product_id');
        var description = $(this).data('description');

        $('#modalEditProductPartNumber').html(part_number);
        $('#modalEditProductDescription').html(description);
        $('#modalEditProductInputProductId').val(product_id);
        $('#messageProductId').html('');

        if(product_id == 0) {
            product_id = '';
        }

        if(product_id != '' && product_id != null) {
            loadDataProductID(product_id);
        }

        $('#modalEditProduct').modal('show');
    });

    $('#modalEditProductContent #modalEditProductInputProductId').on('change',function(e) {
        e.preventDefault();
        var product_id = $(this).val();
        if(product_id == '' || product_id == null) {
            $('#messageProductId').html('');
        } else {
            loadDataProductID(product_id);
        }
    });

    $('#modalEditProductBtnSimpan').on('click', function(e) {
        e.preventDefault();
        var part_number = $('#modalEditProductPartNumber').html();
        var product_id = $('#modalEditProductContent #modalEditProductInputProductId').val();
        var _token = $('input[name="_token"]').val();

        if(product_id == '' || product_id == null) {
            Swal.fire({
                text: 'Isi data product id terlebih dahulu',
                icon: 'warning',
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Ok, got it!',
                customClass: {
                    confirmButton: 'btn btn-warning'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#modalEditProductInputProductId').focus();
                }
            });
        } else {
            loading.block();
            $.ajax({
                url: url.update_product_id,
                method: "post",
                data: { part_number: part_number, product_id: product_id,
                        _token: _token },

                success: function (response) {
                    loading.release();
                    if (response.status == true) {
                        Swal.fire({
                            text: response.message,
                            icon: 'success',
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                loadDaftarPartNumber(part_number.trim());
                                $('#modalEditProduct').modal('hide');
                            }
                        });
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
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
            });
        }
    });
});
