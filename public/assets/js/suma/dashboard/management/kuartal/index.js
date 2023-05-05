// ===============================================================
// Load Data
// ===============================================================
function loadMasterData(year = '', option_company = '', companyid = '', fields = '', kabupaten = '', supervisor = '', salesman = '', produk = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?year=' + year.trim() + '&option_company=' + option_company.trim() +
        '&companyid=' + companyid.trim() + '&fields=' + fields.trim() + '&kabupaten=' + kabupaten.trim() + '&supervisor=' + supervisor.trim() +
        '&salesman=' + salesman.trim() + '&produk=' + produk.trim();
}

function getOptionCompany(option_company = '') {
    if (option_company == 'COMPANY_TERTENTU') {
        $("#inputFilterCompanyId").val(data_default.companyid);
        $("#btnFilterCompanyId").prop("disabled", false);
        $("#btnFilterCompanyId").prop("disabled", false);
    } else {
        if (option_company == 'SEMUA_CABANG') {
            $('#inputFilterCompanyId').attr('placeholder','Semua Cabang');
        } else {
            $('#inputFilterCompanyId').attr('placeholder','Semua Company');
        }
        $("#inputFilterCompanyId").val('');
        $("#inputFilterCompanyId").prop("disabled", true);
        $("#btnFilterCompanyId").prop("disabled", true);
    }
}

window.onload = function () {
    var option_company = data_filter.option_company
    getOptionCompany(option_company);
}

$('#selectFilterOptionCompany').change(function () {
    var selectOptionCompany = $('#selectFilterOptionCompany').val();
    getOptionCompany(selectOptionCompany);
});

$(document).ready(function () {
    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();

        $('#inputFilterYear').val(data_filter.year);
        $('#selectFilterOptionCompany').val(data_filter.option_company);
        $('#inputFilterCompanyId').val(data_filter.companyid);
        $('#selectFilterFields').val(data_filter.fields);
        $('#inputFilterKabupaten').val(data_filter.kabupaten);
        $('#inputFilterSupervisor').val(data_filter.supervisor);
        $('#inputFilterSalesman').val(data_filter.salesman);
        $('#inputFilterKodeProduk').val(data_filter.produk);

        $('#modalFilter').modal('show');
    });

    // =====================================================================
    // FILTER COMPANY
    // =====================================================================
    $('#btnFilterCompanyId').on('click', function (e) {
        e.preventDefault();
        loadDataOptionCompany(1, 10, '');
        $('#formOptionCompany').trigger('reset');
        $('#modalOptionCompany').modal('show');
    });

    $('#inputFilterCompanyId').on('click', function (e) {
        e.preventDefault();
        loadDataOptionCompany(1, 10, '');
        $('#formOptionCompany').trigger('reset');
        $('#modalOptionCompany').modal('show');
    });

    $('body').on('click', '#optionCompanyContentModal #selectedOptionCompany', function (e) {
        e.preventDefault();
        $('#inputFilterCompanyId').val($(this).data('companyid'));
        $('#modalOptionCompany').modal('hide');
    });

    // =====================================================================
    // FILTER KABUPATEN
    // =====================================================================
    $('#btnFilterKabupaten').on('click', function (e) {
        e.preventDefault();
        loadDataOptionKabupaten(1, 10, '');
        $('#formOptionKabupaten').trigger('reset');
        $('#modalOptionKabupaten').modal('show');
    });

    $('#inputFilterKabupaten').on('click', function (e) {
        e.preventDefault();
        loadDataOptionKabupaten(1, 10, '');
        $('#formOptionKabupaten').trigger('reset');
        $('#modalOptionKabupaten').modal('show');
    });

    $('body').on('click', '#optionKabupatenContentModal #selectedOptionKabupaten', function (e) {
        e.preventDefault();
        $('#inputFilterKabupaten').val($(this).data('kode_kabupaten'));
        $('#modalOptionKabupaten').modal('hide');
    });

    // =====================================================================
    // FILTER SUPERVISOR
    // =====================================================================
    $('#btnFilterSupervisor').on('click', function (e) {
        e.preventDefault();
        loadDataOptionSupervisor(1, 10, '');
        $('#formOptionSupervisor').trigger('reset');
        $('#modalOptionSupervisor').modal('show');
    });

    $('#inputFilterSupervisor').on('click', function (e) {
        e.preventDefault();
        loadDataOptionSupervisor(1, 10, '');
        $('#formOptionSupervisor').trigger('reset');
        $('#modalOptionSupervisor').modal('show');
    });

    $('body').on('click', '#optionSupervisorContentModal #selectedOptionSupervisor', function (e) {
        e.preventDefault();
        $('#inputFilterSupervisor').val($(this).data('kode_spv'));
        $('#modalOptionSupervisor').modal('hide');
    });

    // =====================================================================
    // FILTER SALESMAN
    // =====================================================================
    $('#btnFilterSalesman').on('click', function (e) {
        e.preventDefault();
        loadDataOptionSalesman(1, 10, '');
        $('#formOptionSalesman').trigger('reset');
        $('#modalOptionSalesman').modal('show');
    });

    $('#inputFilterSalesman').on('click', function (e) {
        e.preventDefault();
        loadDataOptionSalesman(1, 10, '');
        $('#formOptionSalesman').trigger('reset');
        $('#modalOptionSalesman').modal('show');
    });

    $('body').on('click', '#optionSalesmanContentModal #selectedOptionSalesman', function (e) {
        e.preventDefault();
        $('#inputFilterSalesman').val($(this).data('kode_sales'));
        $('#modalOptionSalesman').modal('hide');
    });

    // =====================================================================
    // FILTER PRODUK
    // =====================================================================
    $('#btnFilterProduk').on('click', function (e) {
        e.preventDefault();
        loadDataOptionProduk(1, 10, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('#inputFilterKodeProduk').on('click', function (e) {
        e.preventDefault();
        loadDataOptionProduk(1, 10, '');
        $('#formOptionGroupProduk').trigger('reset');
        $('#modalOptionGroupProduk').modal('show');
    });

    $('body').on('click', '#optionProdukContentModal #selectedOptionProduk', function (e) {
        e.preventDefault();
        $('#inputFilterKodeProduk').val($(this).data('kode_produk'));
        $('#modalOptionGroupProduk').modal('hide');
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $('#btnFilterProses').on('click', function (e) {
        e.preventDefault();

        var year = $('#inputFilterYear').val();
        var option_company = $('#selectFilterOptionCompany').val();
        var companyid = $('#inputFilterCompanyId').val();
        var fields = $('#selectFilterFields').val();
        var kabupaten = $('#inputFilterKabupaten').val();
        var supervisor = $('#inputFilterSupervisor').val();
        var salesman = $('#inputFilterSalesman').val();
        var produk = $('#inputFilterKodeProduk').val();

        loading.block();
        loadMasterData(year, option_company, companyid, fields, kabupaten, supervisor, salesman, produk);
    });

    $('#btnFilterReset').on('click', function (e) {
        e.preventDefault();
        var dateObj = new Date();
        var year = dateObj.getUTCFullYear();

        loading.block();
        $.ajax({
            url: url.clossing_marketing,
            method: "get",
            success: function(response) {
                loading.release();
                if (response.status == false) {
                    Swal.fire({
                        text: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                } else {
                    month = response.data.bulan_aktif;
                    year = response.data.tahun_aktif;

                    $('#inputFilterYear').val(year);
                    $('#selectFilterOptionCompany').prop('selectedIndex', 2).change();
                    $('#inputFilterCompanyId').val(data_default.companyid);
                    $('#selectFilterFields').prop('selectedIndex', 1).change();
                    $('#inputFilterKabupaten').val('');
                    $('#inputFilterSupervisor').val('');
                    $('#inputFilterSalesman').val('');
                    $('#inputFilterKodeProduk').val('');
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
        });
    });
});


