$(document).ready(function () {
    $('#btn_kirim').on('click', function (e) {
        e.preventDefault();
        if (!$('#tanggal').hasClass('is-invalid')) {
            $('#form_sj').submit();
        }
    });

    $('#tanggal_awal').flatpickr({
        dateFormat: "d-m-Y",
        defaultDate: moment().format('DD-MM-YYYY')
    });
    $('#tanggal_akhir').flatpickr({
        dateFormat: "d-m-Y",
        defaultDate: moment().format('DD-MM-YYYY')
    });
});