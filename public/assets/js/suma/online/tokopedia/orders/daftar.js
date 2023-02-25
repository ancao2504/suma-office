function reloadDaftarOrders(page = 1, per_page = 10, start_date = '', end_date = '', status = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?page=' + page + '&per_page=' + per_page +
        '&start_date=' + start_date.trim() + '&end_date=' + end_date + '&status=' + status;
}

function loadDaftarOrders(page = 1, per_page = 10, start_date = '', end_date = '', status = '') {
    loading.block();
    $.ajax({
        url: url.daftar_order,
        method: "get",
        data: { page: page, per_page: per_page, start_date: start_date,
                end_date: end_date, status: status },

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
        maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(3)).format('YYYY-MM-DD'),
        defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(3)).format('YYYY-MM-DD')
    });

    // ===============================================================
    // Scroll
    // ===============================================================
    var pages = 1;

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
                var status = $('#selectStatus').val();

                pages++;
                loadDaftarOrders(pages, 10, start_date, end_date, status);
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
            maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(3)).format('YYYY-MM-DD'),
            defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(3)).format('YYYY-MM-DD')
        });
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(1, 10, start_date, end_date, status);
    });

    $('#selectStatus').on('change', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var status = $('#selectStatus').val();
        reloadDaftarOrders(1, 10, start_date, end_date, status);
    });
});
