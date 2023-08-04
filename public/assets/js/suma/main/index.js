function estimasiTotalCart() {
    loading.block();
    $.ajax({
        url: url_dsb.estimasi_cart,
        method: "get",

        success: function(response) {
            loading.release();

            if (response.status == true) {
                $('#infoCartTotal').html(response.view_estimate_cart);
                if(response.view_total_item_cart > 0) {
                    $('#infoItemCart').html(response.view_item_cart);
                } else {
                    $('#infoItemCart').html('');
                }
                $('#kt_body').addClass('header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed');
                document.getElementById('kt_body').style.cssText = '--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px';
            } else {
                $('#infoItemCart').html('');
                $('#infoCartTotal').html('');
                $('#kt_body').addClass('header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed');
                document.getElementById('kt_body').removeAttribute("style");
            }
        },
        error: function() {
            loading.release();
        }
    });
}

function cekSalesmanDealerIndex() {
    loading.block();
    $.ajax({
        url: url_dsb.cart_index,
        method: "get",

        success: function(response) {
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
                if(response.data != null) {
                    $('#inputKodeSalesIndex').val(response.data.salesman);
                    $('#inputKodeDealerIndex').val(response.data.dealer);
                }

                $('#modalSalesmanDealerIndex').modal({backdrop: 'static', keyboard: false});
                $('#modalSalesmanDealerIndex').modal('show');
            }
        },
        error: function() {
            loading.release();
        }
    });
}

$(document).ready(function() {
    $('body').on('click', '.menu-item a', function(e) {
        loading.block();
    });

    $(window).on('offline', function(e) {
        Swal.fire({
            title: "Anda sedang offline",
            text: "Silakan periksa koneksi internet Anda.",
            icon: "warning",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false
        });
    });

    $(window).on('online', function(e) {
        Swal.close();
    });

    // menganti enter ke tab jika dia input, select, textarea
    $("input, select, textarea").on('keypress',function(e) {
        if (e.key === 'Enter') {
            var inputs = $("input, select, textarea").filter(':visible:not([disabled]):not([readonly])');
            inputs[inputs.index(this) + 1].focus();
            e.preventDefault();
        }
    });

    

    $('body').on('click', '#btnSalesmanDealerHeaderIndex', function(e) {
        cekSalesmanDealerIndex();
    });

    //===========================================================
    // SALESMAN
    //===========================================================
    $('body').on('click', '#inputKodeSalesIndex', function(e) {
        loadDataOptionSalesmanIndex();
        $('#formOptionSalesmanIndex').trigger('reset');
        $('#modalOptionSalesmanIndex').modal('show');
    });

    $('body').on('click', '#btnPilihSalesmanIndex', function(e) {
        loadDataOptionSalesmanIndex();
        $('#formOptionSalesmanIndex').trigger('reset');
        $('#modalOptionSalesmanIndex').modal('show');
    });

    $('body').on('click', '#formOptionSalesmanIndex #selectSalesman', function(e) {
        e.preventDefault();
        var salesman_index = $(this).data('kode_sales');

        $('#inputKodeSalesIndex').val(salesman_index);
        $('#inputKodeDealerIndex').val('');

        $('#modalOptionSalesmanIndex').modal('hide');
    });

    //===========================================================
    // DEALER
    //===========================================================
    $('body').on('click', '#modalSalesmanDealerIndex #inputKodeDealerIndex', function(e) {
        var salesmanIndex = $('#inputKodeSalesIndex').val();

        if(salesmanIndex == '') {
            Swal.fire({
                text: "Pilih kode sales terlebih dahulu",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        } else {
            $('#formOptionDealerIndex').trigger('reset');
            loadDataOptionDealerIndex(salesmanIndex.trim(), 1, 10, '');
            $('#modalOptionDealerIndex').modal('show');
        }
    });

    $('body').on('click', '#modalSalesmanDealerIndex #btnPilihDealerIndex', function(e) {
        var salesmanIndex = $('#inputKodeSalesIndex').val();

        if(salesmanIndex == '') {
            Swal.fire({
                text: "Pilih kode sales terlebih dahulu",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-danger"
                }
            });
        } else {
            $('#formOptionDealerIndex').trigger('reset');
            loadDataOptionDealerIndex(salesmanIndex.trim(), 1, 10, '');
            $('#modalOptionDealerIndex').modal('show');
        }
    });

    $('body').on('click', '#formOptionDealerIndex #selectDealerSalesman', function(e) {
        e.preventDefault();
        var dealerIndex = $(this).data('kode_dealer');
        $('#inputKodeDealerIndex').val(dealerIndex);
        $('#modalOptionDealerIndex').modal('hide');
    });
});