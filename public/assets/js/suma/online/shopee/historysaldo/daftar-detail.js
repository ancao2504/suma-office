var pages = 1;

function reloadDaftarHistorySaldo(page = 1, start_date = '', end_date = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?page=' + page +
            '&start_date=' + start_date + '&end_date=' + end_date;
}

function loadMoreDaftarHistorySaldo(page = 0, start_date = '', end_date = '') {
    loading.block();
    $.ajax({
        url: url.daftar_history_saldo_detail,
        method: "get",
        data: { page: page + 1, start_date: start_date, end_date: end_date },

        success: function (response) {
            loading.release();
            if (response.status == true) {
                if(response.data == '') {
                    return;
                }
                pages = parseFloat(pages) + 1;
                $('#postData').append(response.data);
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

                loadMoreDaftarHistorySaldo(pages, start_date, end_date);
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

        reloadDaftarHistorySaldo(1, start_date, end_date);
    });

});
