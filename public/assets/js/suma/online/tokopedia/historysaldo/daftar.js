var pages = 1;
console.log('Page success : ' + pages);

function reloadDaftarHistorySaldo(list_view = '', page = 1, per_page = 50, start_date = '', end_date = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?list_view='+ list_view +
        '&page=' + page + '&per_page=' + per_page + '&start_date=' + start_date + '&end_date=' + end_date;
}

function loadMoreDaftarHistorySaldo(page = 0, per_page = 50, start_date = '', end_date = '') {
    loading.block();
    $.ajax({
        url: url.daftar_history_saldo,
        method: "get",
        data: { page: page + 1, per_page: per_page, start_date: start_date,
                end_date: end_date },

        success: function (response) {
            loading.release();
            if (response.status == true) {
                if(response.data == '') {
                    return;
                }
                pages = parseFloat(pages) + 1;
                $('#tableHistorySaldo > tbody:last-child').append(response.data);

                console.log('Page success : ' + pages);
            } else {
                console.log('Page warning : ' + pages);
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
            console.log('Page error : ' + pages);
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
    });
}

$(document).ready(function () {
    $("#inputStartDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        defaultDate: moment(data.start_date).format('YYYY-MM-DD')
    });
    $("#inputEndDate").flatpickr({
        clickOpens: true,
        dateFormat: 'Y-m-d',
        minDate: moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD'),
        maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(6)).format('YYYY-MM-DD'),
        defaultDate: moment(data.end_date).format('YYYY-MM-DD')
    });

    // ===============================================================
    // Scroll
    // ===============================================================
    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
                var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');

                loadMoreDaftarHistorySaldo(pages, 50, start_date, end_date);
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
            maxDate: moment(new Date($("#inputStartDate").val()).fp_incr(6)).format('YYYY-MM-DD'),
            defaultDate: moment(new Date($("#inputStartDate").val()).fp_incr(6)).format('YYYY-MM-DD')
        });
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');
        var list_view = data.list_view;

        reloadDaftarHistorySaldo(list_view, 1, 50, start_date, end_date);
    });

    $('#navListDetail').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');

        reloadDaftarHistorySaldo('DETAIL', 1, 50, start_date, end_date);
    });

    $('#navListGroupTotal').on('click', function (e) {
        e.preventDefault();
        var start_date = moment(new Date($("#inputStartDate").val())).format('YYYY-MM-DD');
        var end_date = moment(new Date($("#inputEndDate").val())).format('YYYY-MM-DD');

        reloadDaftarHistorySaldo('GROUP_TOTAL', 1, 50, start_date, end_date);
    });
});
