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
            $('#filter_report .text-kd_part').text('Kode Part');
            $('#filter_report #kd_part').attr('placeholder', 'Contoh : 22535KWN901');
            $('#filter_report #jenis_part_selector').attr('hidden', true);

            const honda_lokasi = lokasi['honda'].lokasi;
            Object.keys(honda_lokasi).map(function(item) {
                $('#filter_report #company').append(`<option value="${honda_lokasi[item].companyid}">${honda_lokasi[item].companyid}</option>`);
            });
        } else if ($(this).val().toLowerCase() == 'fdr') {
            $('#filter_report .text-kd_part').text('Ukuran Ban');
            $('#filter_report #kd_part').attr('placeholder', 'Contoh : 80/90-17');
            $('#filter_report #jenis_part_selector').attr('hidden', false);

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

    $('#filter_report #tgl_lahir').on('change', function() {
        $('#filter_report #tgl_lahir1').val('');
        $('#filter_report #tgl_lahir2').val('');
        $('#filter_report #tgl_lahir3').val('');
        $('#filter_report #tgl_lahir4').val('');
    });

    $('#filter_report #tgl_lahir1, #tgl_lahir2, #tgl_lahir3, #tgl_lahir4').on('change', function() {
        $('#filter_report #tgl_lahir').val('');
    });

    $('#filter_report #tgl_tran').on('change', function() {
        $('#filter_report #tgl_tran0').val('');
        $('#filter_report #tgl_tran1').val('');
    });

    $('#filter_report #tgl_tran0, #tgl_tran1').on('change', function() {
        $('#filter_report #tgl_tran').val('');
    });

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

        if($('#filter_report #tgl_lahir1').val() != '' || $('#filter_report #tgl_lahir2').val() != '' || $('#filter_report #tgl_lahir3').val() != '' || $('#filter_report #tgl_lahir4').val() != ''){
            if($('#filter_report #tgl_lahir1').val() == '' || $('#filter_report #tgl_lahir2').val() == '' || $('#filter_report #tgl_lahir3').val() == '' || $('#filter_report #tgl_lahir4').val() == ''){
                Swal.fire({
                    text: 'Maaf Tanggal Lahir harus diisi semua!',
                    icon: "warning",
                    confirmButtonText: "OK !",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if ($('#filter_report #tgl_lahir1').val() == '') {
                            $('#filter_report #tgl_lahir1').trigger('click').focus();
                        } else if ($('#filter_report #tgl_lahir2').val() == '') {
                            $('#filter_report #tgl_lahir2').trigger('click').focus();
                        } else if ($('#filter_report #tgl_lahir3').val() == '') {
                            $('#filter_report #tgl_lahir3').trigger('click').focus();
                        } else {
                            $('#filter_report #tgl_lahir4').trigger('click').focus();
                        }
                    }
                });
                return false;
            }
        }

        if($('#filter_report #tgl_tran0').val() != '' || $('#filter_report #tgl_tran1').val() != ''){
            if($('#filter_report #tgl_tran0').val() == '' || $('#filter_report #tgl_tran1').val() == ''){
                Swal.fire({
                    text: 'Maaf Tanggal Transaksi harus diisi semua!',
                    icon: "warning",
                    confirmButtonText: "OK !",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if ($('#filter_report #tgl_tran0').val() == '') {
                            $('#filter_report #tgl_tran0').trigger('click').focus();
                        } else {
                            $('#filter_report #tgl_tran1').trigger('click').focus();
                        }
                    }
                });
                return false;
            }
        }
        loading.block();
        // kirim data ke server
        $.ajax({
            url: base_url + '/report/konsumen/daftar',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                divisi: $('#filter_report #divisi').val(),
                companyid: $('#filter_report #company').val(),
                kd_lokasi: $('#filter_report #lokasi').val(),

                tgl_transaksi: ($('#filter_report #tgl_tran').val()!='')?[$('#filter_report #tgl_tran').val()]:(($('#filter_report #tgl_tran0').val() != '')?[$('#filter_report #tgl_tran0').val(), $('#filter_report #tgl_tran1').val()]:null),

                tgl_lahir: ($('#filter_report #tgl_lahir').val()!='')?[$('#filter_report #tgl_lahir').val()]:(($('#filter_report #tgl_lahir1').val() != '')?[$('#filter_report #tgl_lahir1').val(), $('#filter_report #tgl_lahir2').val(), $('#filter_report #tgl_lahir3').val(), $('#filter_report #tgl_lahir4').val()]:null),

                jenis_part: $('#filter_report #jenis_part').val(),
                kd_part: $('#filter_report #kd_part').val(),

                merek_motor: $('#filter_report #merek_motor').val(),
                tipe_motor: $('#filter_report #tipe_motor').val(),
                jenis_motor: $('#filter_report #jenis_motor').val(),
                page: 1,
                per_page: 10
            },
            dataType: 'json',
            beforeSend: function() {
                loading.block();
            },
            success: function(response) {
                $('#kt_post #table_list').html(response.data);
                
                $('#filter_report').modal('hide');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    text: 'Maaf terjadi kesalahan, silahkan coba lagi!',
                    icon: "error",
                    confirmButtonText: "OK !",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            }
        });
        
        loading.release();
    });

    // data = {
    //     tgl_lahir : tgl_lahir??[tgl_lahir1, tgl_lahir2, tgl_lahir3, tgl_lahir4],

    // }

});