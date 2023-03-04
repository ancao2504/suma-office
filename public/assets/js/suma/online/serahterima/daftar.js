
$(document).ready(function () {
    $("#inputStartDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d'
    });
    $("#inputEndDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD')
    });

    // ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, start_date = '', end_date = '', search = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?start_date=' + start_date.trim() + '&end_date=' + end_date.trim() +
            '&search=' + search.trim() + '&per_page=' + per_page + '&page=' + page;
    }

    $('#selectPerPageMasterData').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, start_date, end_date, search);
    });

    $(document).on('click', '#paginationMasterData .page-item a', function () {
        var page = $(this)[0].getAttribute('data-page');
        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();

        loadMasterData(page, per_page, start_date, end_date, search);
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $('#inputStartDate').change(function() {
        $("#inputEndDate").flatpickr({
            clickOpens: true,
            dateFormat: 'Y-m-d',
            minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD')
        });
    });

    $('#inputSearch').on('change', function(){
        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();

        loadMasterData(1, per_page, start_date, end_date, search);
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();

        var per_page = $('#selectPerPageMasterData').val();
        var start_date = $('#inputStartDate').val();
        var end_date = $('#inputEndDate').val();
        var search = $('#inputSearch').val();

        loadMasterData(1, per_page, start_date, end_date, search);
    });

});
