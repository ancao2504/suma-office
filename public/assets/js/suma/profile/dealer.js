$(document).ready(function () {
    // ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, kode_dealer = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?kode_dealer=' + kode_dealer +
            '&per_page=' + per_page + '&page=' + page;
    }

    $('#searchFilterDealer').on('change', function (e) {
        if (e.keyCode == 13 || e.type == 'change') {
            var per_page = $('#selectPerPageDealer').val();
            var kode_dealer = $('#searchFilterDealer').val();

            loadMasterData(1, per_page, kode_dealer);
        }
    });

    $('#selectPerPageDealer').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPageDealer').val();
        var kode_dealer = $('#searchFilterDealer').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, kode_dealer);
    });

    $(document).on('click', '#paginationDealer .page-item a', function () {
        var page = $(this)[0].getAttribute('data-page');
        var per_page = $('#selectPerPageDealer').val();
        var kode_dealer = $('#searchFilterDealer').val();

        loadMasterData(page, per_page, kode_dealer);
    });
});
