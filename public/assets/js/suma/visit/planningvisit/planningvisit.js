$(document).ready(function () {
    $("#inputTanggal").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        minDate: moment(new Date()).format('YYYY-MM-DD'),
        defaultDate: moment(new Date()).format('YYYY-MM-DD')
    });
    // ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, year = '', month = '', salesman = '', dealer = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&month=' + month.trim() +
            '&salesman=' + salesman.trim() + '&dealer=' + dealer.trim() + '&per_page=' + per_page + '&page=' + page;
    }

    $('#selectPerPageMasterData').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#inputFilterDealer').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, year, month, salesman, dealer);
    });

    $(document).on('click', '#paginationMasterData .page-item a', function () {
        var page = $(this)[0].getAttribute('data-page');
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#inputFilterDealer').val();

        loadMasterData(page, per_page, year, month, salesman, dealer);
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        $('#inputFilterYear').val(data_filter.year);
        $('#selectFilterMonth').prop('selectedIndex', data_filter.month - 1).change();
        $('#inputFilterSalesman').val(data_filter.salesman);
        $('#inputFilterDealer').val(data_filter.dealer);
        $('#modalFilter').modal('show');
    });

    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();
        var per_page = $('#selectPerPageMasterData').val();
        var year = $('#inputFilterYear').val();
        var month = $('#selectFilterMonth').val();
        var salesman = $('#inputFilterSalesman').val();
        var dealer = $('#inputFilterDealer').val();

        loadMasterData(1, per_page, year, month, salesman, dealer);
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var date = moment(moment(), 'YYYY/MM/DD');
        var month = date.format('M');
        var year  = date.format('YYYY');

        $('#inputFilterYear').val(year);
        $('#selectFilterMonth').prop('selectedIndex', month - 1).change();

        if(data_user.role_id == 'MD_H3_SM') {
            $('#inputFilterDealer').val('');
        } else {
            $('#inputFilterSalesman').val('');
            $('#inputFilterDealer').val('');
        }
    });

    // ===============================================================
    // Filter Salesman
    // ===============================================================
    $('#inputFilterSalesman').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'MD_H3_SM') {
            loadDataOptionSalesman();
            $('#formOptionSalesman').trigger('reset');
            $('#modalOptionSalesman').modal('show');
        }
    });

    $('#btnFilterPilihSalesman').on('click', function (e) {
        e.preventDefault();
        if(data_user.role_id != 'MD_H3_SM') {
            loadDataOptionSalesman();
            $('#formOptionSalesman').trigger('reset');
            $('#modalOptionSalesman').modal('show');
        }
    });

    $('body').on('click', '#optionSalesmanContentModal #selectedOptionSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterSalesman').val($(this).data('kode_sales'));
        $('#modalOptionSalesman').modal('hide');
    });

    // ===============================================================
    // Filter Dealer
    // ===============================================================
    $('#inputFilterDealer').on('click', function (e) {
        e.preventDefault();
        var kode_sales = $('#inputFilterSalesman').val();

        if(data_user.role_id != 'D_H3') {
            if(data_user.role_id == 'MD_H3_SM' || data_user.role_id == 'MD_H3_KORSM') {
                if(kode_sales == '') {
                    Swal.fire({
                        text: 'Data salesman tidak boleh kosong',
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                } else {
                    $('#formOptionDealerSalesman').trigger('reset');
                    loadDataOptionDealerSalesman(kode_sales.trim(), 1, 10, '');
                    $('#modalOptionDealerSalesman').modal('show');
                }
            } else {
                $('#formOptionDealer').trigger('reset');
                loadDataOptionDealer(1, 10, '');
                $('#modalOptionDealer').modal('show');
            }
        }
    });

    $('#btnFilterPilihDealer').on('click', function (e) {
        e.preventDefault();
        var kode_sales = $('#inputFilterSalesman').val();

        if(data_user.role_id != 'D_H3') {
            if(data_user.role_id == 'MD_H3_SM' || data_user.role_id == 'MD_H3_KORSM') {
                if(kode_sales == '') {
                    Swal.fire({
                        text: 'Data salesman tidak boleh kosong',
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                } else {
                    $('#formOptionDealerSalesman').trigger('reset');
                    loadDataOptionDealerSalesman(kode_sales.trim(), 1, 10, '');
                    $('#modalOptionDealerSalesman').modal('show');
                }
            } else {
                $('#formOptionDealer').trigger('reset');
                loadDataOptionDealer(1, 10, '');
                $('#modalOptionDealer').modal('show');
            }
        }
    });

    $('body').on('click', '#optionDealerContentModal #selectedOptionDealer', function (e) {
        e.preventDefault();
        $('#inputFilterDealer').val($(this).data('kode_dealer'));
        $('#modalOptionDealer').modal('hide');
    });

    $('body').on('click', '#optionDealerSalesmanContentModal #selectedOptionDealerSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterDealer').val($(this).data('kode_dealer'));
        $('#modalOptionDealerSalesman').modal('hide');
    });

    // ===============================================================
    // Tambah Planning Visit
    // ===============================================================
    $('#btnTambah').click(function () {
        $('#formPlanningVisit').trigger('reset');

        $('#modalTitlePlanningVisit').html("Tambah Data Planning Visit");
        $('#inputTanggal').val(moment(new Date()).format('YYYY-MM-DD'));
        $('#btnSimpan').attr('disabled',false);
        $('#btnSimpan').show();
        $('#modalPlanningVisit').modal('show');

        $('#modalPlanningVisit').on('shown.bs.modal', function () {
        });
    });

    $('#btnSimpanPlanningVisit').click(function () {
        var salesman = $('#inputSalesman').val();
        var dealer = $('#inputDealer').val();
        var tanggal = $('#inputTanggal').val();
        var keterangan = $('#inputKeterangan').val();

        if(salesman == '' || dealer == '' || tanggal == '' || keterangan == '') {
            Swal.fire({
                text: 'Isi data planning visit secara lengkap',
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-warning"
                }
            });
        } else {
            loading.block();
            $('#formPlanningVisit').submit();
        }
    });

    // ===============================================================
    // Input Salesman
    // ===============================================================
    $('#inputSalesman').on('click', function (e) {
        e.preventDefault();
        loadDataOptionSalesman();
        $('#formOptionSalesman').trigger('reset');
        $('#modalOptionSalesman').modal('show');
    });

    $('#btnFormPilihSalesman').on('click', function (e) {
        e.preventDefault();
        loadDataOptionSalesman();
        $('#formOptionSalesman').trigger('reset');
        $('#modalOptionSalesman').modal('show');
    });

    $('body').on('click', '#optionSalesmanContentModal #selectedOptionSalesman', function (e) {
        e.preventDefault();
        $('#inputSalesman').val($(this).data('kode_sales'));
        $('#modalOptionSalesman').modal('hide');
    });

    // ===============================================================
    // Input Dealer
    // ===============================================================
    $('#inputDealer').on('click', function (e) {
        e.preventDefault();
        var kode_sales = $('#inputSalesman').val();
        if(kode_sales == '') {
            Swal.fire({
                text: 'Data salesman tidak boleh kosong',
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-warning"
                }
            });
        } else {
            $('#formOptionDealerSalesman').trigger('reset');
            loadDataOptionDealerSalesman(kode_sales.trim(), 1, 10, '');
            $('#modalOptionDealerSalesman').modal('show');
        }
    });

    $('#btnFormPilihDealer').on('click', function (e) {
        e.preventDefault();
        var kode_sales = $('#inputSalesman').val();
        if(kode_sales == '') {
            Swal.fire({
                text: 'Data salesman tidak boleh kosong',
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-warning"
                }
            });
        } else {
            $('#formOptionDealerSalesman').trigger('reset');
            loadDataOptionDealerSalesman(kode_sales.trim(), 1, 10, '');
            $('#modalOptionDealerSalesman').modal('show');
        }
    });

    $('body').on('click', '#optionDealerSalesmanContentModal #selectedOptionDealerSalesman', function (e) {
        e.preventDefault();
        $('#inputDealer').val($(this).data('kode_dealer'));
        $('#modalOptionDealerSalesman').modal('hide');
    });

    // ===============================================================
    // Hapus Planning Visit
    // ===============================================================
    $('body').on('click', '#hapusPlanningVisit', function (event) {
        var element = $(this).parent().parent();
        var kode_visit = $(this).data('kode');
        var _token = $('input[name="_token"]').val();

        Swal.fire({
            html: `Apa anda yakin akan menghapus data <strong>Planning Visit Ini</strong> ?`,
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
                    url: url.hapus,
                    method: "post",
                    data: {
                        kode_visit: kode_visit, _token: _token
                    },

                    success:function(response) {
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
                            element.fadeOut().remove();
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
                    error:function() {
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
});

