$(document).ready(function () {
    $('#btn_kirim').on('click', function (e) {
        e.preventDefault();
        if (!$('#tanggal').hasClass('is-invalid')) {
            $('#form_sj').submit();
        }
    });

    $('#start_date').flatpickr({
        dateFormat: "d-m-Y",
        defaultDate: moment().format('DD-MM-YYYY')
    });
    $('#end_date').flatpickr({
        dateFormat: "d-m-Y",
        defaultDate: moment().format('DD-MM-YYYY')
    });
});