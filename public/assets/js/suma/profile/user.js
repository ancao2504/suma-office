// dokumen ready
$(document).ready(function () {
    // ===============================================================
    // Daftar
    // ===============================================================
    function loadMasterData(page = 1, per_page = 10, role_id = '', user_id = '') {
        loading.block();
        window.location.href = window.location.origin + window.location.pathname + '?role_id=' + role_id + '&user_id=' + user_id +
            '&per_page=' + per_page + '&page=' + page;
    }

    $('#inputFilterUserId').on('change', function (e) {
        var per_page = $('#selectPerPageUser').val();
        var role_id = $('#selectFilterRoleId').val();
        var user_id = $('#inputFilterUserId').val();

        loadMasterData(1, per_page, role_id, user_id);
    });

    $('#selectPerPageUser').change(function() {
        var start_record = data_page.start_record;
        var per_page = $('#selectPerPageUser').val();
        var role_id = $('#selectFilterRoleId').val();
        var user_id = $('#inputFilterUserId').val();
        var page = Math.ceil(start_record / per_page);

        loadMasterData(page, per_page, role_id, user_id);
    });

    $(document).on('click', '#paginationUser .page-item a', function () {
        var page = $(this)[0].getAttribute('data-page');
        var per_page = $('#selectPerPageUser').val();
        var role_id = $('#selectFilterRoleId').val();
        var user_id = $('#inputFilterUserId').val();

        loadMasterData(page, per_page, role_id, user_id);
    });
});
