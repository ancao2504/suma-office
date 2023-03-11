function reloadDaftarOrders(cursor = 0, page_size = 10, fields = 'create_time', start_date = '', end_date = '', status = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?fields=' + fields +
        '&start_date=' + start_date + '&end_date=' + end_date + '&status=' + status +
        '&page_size=' + page_size + '&cursor=' + cursor;

}

function loadDaftarOrders(cursor = 0, page_size = 10, fields = 'create_time', start_date = '', end_date = '', status = '') {
    loading.block();
    $.ajax({
        url: url.daftar_order,
        method: "get",
        data: { cursor: cursor, page_size: page_size, fields: fields,
                start_date: start_date, end_date: end_date,
                status: status },

        success: function (response) {
            loading.release();

            if (response.status == true) {
                if(response.data == '') {
                    return;
                }
                $('#postOrder').append(response.data);
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
                text: 'Server not responding',
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
    })
}

$(document).ready(function () {
    $("#inputStartDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d'
    });
    $("#inputEndDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD'),
        maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD'),
        defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD')
    });

    // ===============================================================
    // Scroll
    // ===============================================================
    var pages = 0;

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                var fields = $('#selectFields').val();
                var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
                var status = $('#selectStatus').val();

                pages++;
                loadDaftarOrders(pages, 10, fields, start_date, end_date, status);
            }
        }
    });

    // ===============================================================
    // Filter
    // ===============================================================
    $('#inputStartDate').on('change', function (e) {
        e.preventDefault();
        $("#inputEndDate").flatpickr({
            clickOpens: true,
            dateFormat: 'Y-m-d',
            minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD'),
            maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD'),
            defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(14)).format('YYYY-MM-DD')
        });
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
    });

    $('#selectStatus').on('change', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
    });

    $('#navSemuaProses').on('click', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(0, 10, fields, start_date, end_date, status);
    });

    $('#navBelumProses').on('click', function (e) {
        e.preventDefault();
        var fields = $('#selectFields').val();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        reloadDaftarOrders(0, 10, fields, start_date, end_date, 'READY_TO_SHIP');
    });
});
