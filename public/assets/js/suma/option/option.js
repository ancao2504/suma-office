// =====================================================================
// Load Data Supervisor
// =====================================================================
function loadDataSupervisor(page = 1, per_page = 10, search = '') {
    $.ajax({
        url: base_url + '/option/supervisor' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#supervisorContentModal').html(response.data);
            }
        }
    });
}

$(document).on('click', '#searchSupervisorForm #pageSupervisor .pagination .page-item a', function () {
    pages = $(this)[0].getAttribute("data-page");
    page = pages.split('?page=')[1];

    var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
    var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

    loadDataSupervisor(page, per_page_spv, search_spv);
});

$('body').on('change', '#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor', function (e) {
    e.preventDefault();

    var start_record_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisorInfo #startRecordSupervisor').html();
    var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
    var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

    var page = Math.ceil(start_record_spv / per_page_spv);

    loadDataSupervisor(page, per_page_spv, search_spv);
});

$('body').on('click', '#searchSupervisorForm #btnSearchSupervisor', function (e) {
    e.preventDefault();
    var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
    var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

    loadDataSupervisor(1, per_page_spv, search_spv);
});

$('#searchSupervisorForm #inputSearchSupervisor').on('change', function (e) {
    e.preventDefault();
    var search_spv = $('#searchSupervisorForm #inputSearchSupervisor').val();
    var per_page_spv = $('#searchSupervisorForm #supervisorContentModal #pageSupervisor #selectPerPageSupervisor').val();

    loadDataSupervisor(1, per_page_spv, search_spv);
});

// =====================================================================
// Load Data Salesman
// =====================================================================
function loadDataSalesman(page = 1, per_page = 10, search = '') {
    $.ajax({
        url: base_url + '/option/salesman' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#salesmanContentModal').html(response.data);
            }
        }
    });
}

$(document).on('click', '#searchSalesmanForm #pageSalesman .pagination .page-item a', function () {
    pages = $(this)[0].getAttribute("data-page");
    page = pages.split('?page=')[1];

    var search_sales = $('#searchSalesmanForm #inputSearchSalesman').val();
    var per_page_sales = $('#searchSalesmanForm #salesmanContentModal #pageSalesman #selectPerPageSalesman').val();

    loadDataSalesman(page, per_page_sales, search_sales);
});

$('body').on('change', '#searchSalesmanForm #salesmanContentModal #pageSalesman #selectPerPageSalesman', function (e) {
    e.preventDefault();

    var start_record_sales = $('#searchSalesmanForm #salesmanContentModal #pageSalesman #selectPerPageSalesmanInfo #startRecordSalesman').html();
    var search_sales = $('#searchSalesmanForm #inputSearchSalesman').val();
    var per_page_sales = $('#searchSalesmanForm #salesmanContentModal #pageSalesman #selectPerPageSalesman').val();

    var page = Math.ceil(start_record_sales / per_page_sales);

    loadDataSalesman(page, per_page_sales, search_sales);
});

$('body').on('click', '#searchSalesmanForm #btnSearchSalesman', function (e) {
    e.preventDefault();
    var search_sales = $('#searchSalesmanForm #inputSearchSalesman').val();
    var per_page_sales = $('#searchSalesmanForm #salesmanContentModal #pageSalesman #selectPerPageSalesman').val();

    loadDataSalesman(1, per_page_sales, search_sales);
});

$('#searchSalesmanForm #inputSearchSalesman').on('change', function (e) {
    e.preventDefault();
    var search_sales = $('#searchSalesmanForm #inputSearchSalesman').val();
    var per_page_sales = $('#searchSalesmanForm #salesmanContentModal #pageSalesman #selectPerPageSalesman').val();

    loadDataSalesman(1, per_page_sales, search_sales);
});

// =====================================================================
// Load Data Dealer Salesman
// =====================================================================
function loadDataDealerSalesman(salesman = '', page = 1, per_page = 10, search = '') {
    $.ajax({
        url: base_url + '/option/dealersalesman' + "?salesman=" + salesman + "&search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#dealerSalesmanContentModal').html(response.data);
            }
        }
    });
}

$(document).on('click', '#searchDealerSalesmanForm #dealerSalesmanContentModal #pageDealerSalesman .pagination .page-item a', function () {
    pages = $(this)[0].getAttribute("data-page");
    page_dealer_salesman = pages.split('?page=')[1];

    var dealer_salesman = $("#inputSalesman").val();
    var search_dealer_salesman = $('#inputSearchDealerSalesman').val();
    var per_page_dealer_salesman = $('#searchDealerSalesmanForm #dealerSalesmanContentModal #pageDealerSalesman #selectPerPageDealerSalesman').val();

    loadDataDealerSalesman(dealer_salesman, page_dealer_salesman, per_page_dealer_salesman, search_dealer_salesman);
});

$('body').on('change', '#searchDealerSalesmanForm #dealerSalesmanContentModal #pageDealerSalesman #selectPerPageDealerSalesman', function (e) {
    e.preventDefault();

    var salesman = $("#inputSalesman").val();
    var start_record_dealer_salesman = $('#searchDealerSalesmanForm #dealerSalesmanContentModal #pageDealerSalesman #selectPerPageDealerSalesmanInfo #startRecordDealerSalesman').html();
    var search_dealer_salesman = $('#searchDealerSalesmanForm #inputSearchDealerSalesman').val();
    var per_page_dealer_salesman = $('#searchDealerSalesmanForm #dealerSalesmanContentModal #pageDealerSalesman #selectPerPageDealerSalesman').val();

    var page_dealer_salesman = Math.ceil(start_record_dealer_salesman / per_page_dealer_salesman);

    loadDataDealerSalesman(salesman, page_dealer_salesman, per_page_dealer_salesman, search_dealer_salesman);
});

$('body').on('click', '#searchDealerSalesmanForm #btnSearchDealerSalesman', function (e) {
    e.preventDefault();
    var salesman = $("#inputSalesman").val();
    var search_dealer_salesman = $('#inputSearchDealerSalesman').val();
    var per_page_dealer_salesman = $('#searchDealerSalesmanForm #dealerSalesmanContentModal #pageDealerSalesman #selectPerPageDealerSalesman').val();

    loadDataDealerSalesman(salesman, 1, per_page_dealer_salesman, search_dealer_salesman);
});

$('#searchDealerSalesmanForm #inputSearchDealerSalesman').on('change', function (e) {
    e.preventDefault();
    var salesman = $("#inputSalesman").val();
    var search_dealer_salesman = $('#inputSearchDealerSalesman').val();
    var per_page_dealer_salesman = $('#searchDealerSalesmanForm #dealerSalesmanContentModal #pageDealerSalesman #selectPerPageDealerSalesman').val();

    loadDataDealerSalesman(salesman, 1, per_page_dealer_salesman, search_dealer_salesman);
});

// =====================================================================
// Load Data Dealer
// =====================================================================
function loadDataDealer(page = 1, per_page = 10, search = '') {
    $.ajax({
        url: base_url + '/option/dealer' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#dealerContentModal').html(response.data);
            }
        }
    });
}

$(document).on('click', '#searchDealerForm #dealerContentModal #pageDealer .pagination .page-item a', function () {
    pages = $(this)[0].getAttribute("data-page");
    page_dealer = pages.split('?page=')[1];

    var search_dealer = $('#inputSearchDealer').val();
    var per_page_dealer = $('#searchDealerForm #dealerContentModal #pageDealer #selectPerPageDealer').val();

    loadDataDealer(page_dealer, per_page_dealer, search_dealer);
});

$('body').on('change', '#searchDealerForm #dealerContentModal #pageDealer #selectPerPageDealer', function (e) {
    e.preventDefault();

    var start_record_dealer = $('#searchDealerForm #dealerContentModal #pageDealer #selectPerPageDealerInfo #startRecordDealer').html();
    var search_dealer = $('#searchDealerForm #inputSearchDealer').val();
    var per_page_dealer = $('#searchDealerForm #dealerContentModal #pageDealer #selectPerPageDealer').val();

    var page_dealer = Math.ceil(start_record_dealer / per_page_dealer);

    loadDataDealer(page_dealer, per_page_dealer, search_dealer);
});

$('body').on('click', '#searchDealerForm #btnSearchDealer', function (e) {
    e.preventDefault();
    var search_dealer = $('#inputSearchDealer').val();
    var per_page_dealer = $('#searchDealerForm #dealerContentModal #pageDealer #selectPerPageDealer').val();

    loadDataDealer(1, per_page_dealer, search_dealer);
});

$('#searchDealerForm #inputSearchDealer').on('change', function (e) {
    e.preventDefault();
    var search_dealer = $('#inputSearchDealer').val();
    var per_page_dealer = $('#searchDealerForm #dealerContentModal #pageDealer #selectPerPageDealer').val();

    loadDataDealer(1, per_page_dealer, search_dealer);
});

// =====================================================================
// Load Data Tipe Motor
// =====================================================================
function loadDataTipeMotor(page = 1, per_page = 10, search = '') {
    $.ajax({
        url: base_url + '/option/tipemotor' + "?search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#tipeMotorContentModal').html(response.data);
            }
        }
    });
}

$(document).on('click', '#searchTipeMotorForm #pageTipeMotor .pagination .page-item a', function () {
    pages = $(this)[0].getAttribute("data-page");
    page = pages.split('?page=')[1];

    var search_tipe_motor = $('#searchTipeMotorForm #inputSearchTipeMotor').val();
    var per_page_tipe_motor = $('#searchTipeMotorForm #tipeMotorContentModal #pageTipeMotor #selectPerPageTipeMotor').val();

    loadDataTipeMotor(page, per_page_tipe_motor, search_tipe_motor);
});

$('body').on('change', '#searchTipeMotorForm #tipeMotorContentModal #pageTipeMotor #selectPerPageTipeMotor', function (e) {
    e.preventDefault();

    var start_record_tipe_motor = $('#searchTipeMotorForm #tipeMotorContentModal #pageTipeMotor #selectPerPageTipeMotorInfo #startRecordTipeMotor').html();
    var search_tipe_motor = $('#searchTipeMotorForm #inputSearchTipeMotor').val();
    var per_page_tipe_motor = $('#searchTipeMotorForm #tipeMotorContentModal #pageTipeMotor #selectPerPageTipeMotor').val();

    var page = Math.ceil(start_record_tipe_motor / per_page_tipe_motor);

    loadDataTipeMotor(page, per_page_tipe_motor, search_tipe_motor);
});

$('body').on('click', '#searchTipeMotorForm #btnSearchTipeMotor', function (e) {
    e.preventDefault();
    var search_tipe_motor = $('#searchTipeMotorForm #inputSearchTipeMotor').val();
    var per_page_tipe_motor = $('#searchTipeMotorForm #tipeMotorContentModal #pageTipeMotor #selectPerPageTipeMotor').val();

    loadDataTipeMotor(1, per_page_tipe_motor, search_tipe_motor);
});

$('#searchTipeMotorForm #inputSearchTipeMotor').on('change', function (e) {
    e.preventDefault();
    var search_tipe_motor = $('#searchTipeMotorForm #inputSearchTipeMotor').val();
    var per_page_tipe_motor = $('#searchTipeMotorForm #tipeMotorContentModal #pageTipeMotor #selectPerPageTipeMotor').val();

    loadDataTipeMotor(1, per_page_tipe_motor, search_tipe_motor);
});

// =====================================================================
// Load Data Produk
// =====================================================================
function loadDataProduk(page = 1, per_page = 10, search = '', level = '') {
    $('#searchProdukForm #inputFilterLevelProduk').html(level);

    $.ajax({
        url: base_url + '/option/groupproduk' + "?level=" + level + "&search=" + search + "&per_page=" + per_page + "&page=" + page,
        method: "get",
        success: function (response) {
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
                $('#produkContentModal').html(response.data);
            }
        }
    });
}

$(document).on('click', '#searchProdukForm #pageProduk .pagination .page-item a', function () {
    pages = $(this)[0].getAttribute("data-page");
    page = pages.split('?page=')[1];

    var level_produk = $('#searchProdukForm #inputFilterLevelProduk').html();
    var search_produk = $('#searchProdukForm #inputSearchProduk').val();
    var per_page_produk = $('#searchProdukForm #produkContentModal #pageProduk #selectPerPageProduk').val();

    loadDataProduk(page, per_page_produk, search_produk, level_produk);
});

$('body').on('change', '#searchProdukForm #produkContentModal #pageProduk #selectPerPageProduk', function (e) {
    e.preventDefault();

    var start_record_produk = $('#searchProdukForm #produkContentModal #pageProduk #selectPerPageProdukInfo #startRecordProduk').html();
    var level_produk = $('#searchProdukForm #inputFilterLevelProduk').html();
    var search_produk = $('#searchProdukForm #inputSearchProduk').val();
    var per_page_produk = $('#searchProdukForm #produkContentModal #pageProduk #selectPerPageProduk').val();

    var page = Math.ceil(start_record_produk / per_page_produk);

    loadDataProduk(page, per_page_produk, search_produk, level_produk);
});

$('body').on('click', '#searchProdukForm #btnSearchProduk', function (e) {
    e.preventDefault();
    var level_produk = $('#searchProdukForm #inputFilterLevelProduk').html();
    var search_produk = $('#searchProdukForm #inputSearchProduk').val();
    var per_page_produk = $('#searchProdukForm #produkContentModal #pageProduk #selectPerPageProduk').val();

    loadDataProduk(1, per_page_produk, search_produk, level_produk);
});

$('#searchProdukForm #inputSearchProduk').on('change', function (e) {
    e.preventDefault();
    var level_produk = $('#searchProdukForm #inputFilterLevelProduk').html();
    var search_produk = $('#searchProdukForm #inputSearchProduk').val();
    var per_page_produk = $('#searchProdukForm #produkContentModal #pageProduk #selectPerPageProduk').val();

    loadDataProduk(1, per_page_produk, search_produk, level_produk);
});
