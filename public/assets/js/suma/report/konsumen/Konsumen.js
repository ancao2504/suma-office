function typemotor(){
    $('#filter_report #tipe_motor').html('<option value="">Pilih Tipe Motor</option>');

    $.each(tipemotor[$('#filter_report #merek_motor').val()], function (key, value) {
        $('#filter_report #tipe_motor').append(`<option value="${value.TypeMotor}">${value.TypeMotor}</option>`);
    });
}

$('document').ready(function() {
    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');

    $("#filter_report #tgl_tran0").flatpickr();
    $("#filter_report #tgl_tran1").flatpickr();

    $('#filter_report #divisi').on('change', function() {
        $('#filter_report #company').html('<option value="">Pilih Cabang</option>');
        $('#filter_report #lokasi').html('<option value="">Pilih Lokasi</option>');

        if ($(this).val().toLowerCase() == 'honda') {
            $('.text-part').text('Jenis Part');
            $('.text-kd_part').text('Kode Part');

            const honda_lokasi = lokasi['honda'].lokasi;
            Object.keys(honda_lokasi).map(function(item) {
                $('#filter_report #company').append(`<option value="${honda_lokasi[item].companyid}">${honda_lokasi[item].companyid}</option>`);
            });
        } else {
            $('.text-part').text('Ukuran Ring Ban');
            $('.text-kd_part').text('Ukuran Ban');

            const fdr_lokasi = lokasi['fdr'].lokasi;
            Object.keys(fdr_lokasi).map(function(item) {
                $('#filter_report #company').append(`<option value="${fdr_lokasi[item].companyid}">${fdr_lokasi[item].companyid}</option>`);
            });
        }
    });

    $('#filter_report #company').on('change', function() {
        if($(this).val() != ''){
            $('#filter_report #lokasi').html('<option value="">Pilih Lokasi</option>');
            lokasi[$('#filter_report #divisi').val()].lokasi[$(this).val()].kd_lokasi.forEach(function(item) {
                $('#filter_report #lokasi').append(`<option value="${item}">${item}</option>`);
            });
        } else {
            $('#filter_report #lokasi').html('<option value="">Pilih Lokasi</option>');
        }
    });

    $('#filter_report #merek_motor').on('change', function() {
        typemotor();
    });

    // #filter_report #btn-smt onclick
    $('#filter_report #btn-smt').on('click', function() {
        if ($('#filter_report #divisi').val() == '' && $('#filter_report #company').val() == '') {
            Swal.fire({
                text: 'Maaf Divisi dan Cabang harus diisi!',
                icon: "warning",
                confirmButtonText: "OK !",
                customClass: {
                    confirmButton: "btn btn-warning"
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    if ($('#filter_report #divisi').val() == '') {
                        $('#filter_report #divisi').trigger('click').focus();
                    } else {
                        $('#filter_report #company').trigger('click').focus();
                    }
                }
            });
            return false;
        }


        // kirim data ke server
    });

    // data = {
    //     tgl_lahir : tgl_lahir??[tgl_lahir1, tgl_lahir2, tgl_lahir3, tgl_lahir4],

    // }

});