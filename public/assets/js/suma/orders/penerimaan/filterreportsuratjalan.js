$(document).ready(function () {
    // saat tambah diskon di klik focus ke pruduk dan merubah tombol enter menjadi tab
    $('#form_sj .card-body').find('input').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            var index = $('#form_sj .card-body').find('input').index(this) + 1;
            if ($('#form_sj .card-body').find('input').eq(index).attr('readonly') || $('#form_sj .card-body').find('input').eq(index).hasClass('bg-secondary')) {
                for (let i = index; i < $('#form_sj .card-body').find('input').length; i++) {
                    if (!$('#form_sj .card-body').find('input').eq(i).attr('readonly') || !$('#form_sj .card-body').find('input').eq(i).hasClass('bg-secondary')) {
                        $('#form_sj .card-body').find('input').eq(i).focus();
                        break;
                    }
                }
            } else {
                $('#form_sj .card-body').find('input').eq(index).focus();
            }
        }
    });
    // end saat tambah diskon

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