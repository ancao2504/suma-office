function typemotor(){
    $('#filter_report #tipe_motor').html('<option value="">Pilih Tipe Motor</option>');

    $.each(tipemotor[$('#filter_report #merek_motor').val()], function (key, value) {
        $('#filter_report #tipe_motor').append(`<option value="${value.TypeMotor}">${value.TypeMotor}</option>`);
    });
}
let ada_data = false;
function request_data(page){
    loading.block();
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
            page: page,
            per_page: $('#table_list').find('#per_page').val(),

            filter: {[$('#filter_urutkan #urutkan_collom').val()] : $('#filter_urutkan #urutkan').val()},
        },
        dataType: 'json',
        success: function(response) {
            if(response.status == 0){
                Swal.fire({
                    text: response.message,
                    icon: "error",
                    confirmButtonText: "OK !",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                    }
                });
                loading.release();
                return false;
            }

            $('#kt_post #table_list').html(response.data);
            $('#filter_report').modal('hide');
            if($('#filter_report #lokasi').val() == ''){
                $('#filter_report #lokasi').val(response.old.request.kd_lokasi).trigger('change');
            }
            // response.old.filter looping
            // #kt_post #table_list #{item}
            response.old.filter.forEach(function(item) {
                console.log(item);
                // ubah warna text menjadi primary pada boostrap
                $('#kt_post #table_list #'+item).addClass('text-primary');
            });

            loading.release();
            ada_data = true;
        },
        error: function(jqXHR, textStatus, errorThrown) {
            loading.release();
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
            $('.text-kd_part').text('Kode Part');
            $('#filter_report #kd_part').attr('placeholder', 'Contoh : 22535KWN901');
            $('#filter_report #jenis_part_selector').attr('hidden', true);

            const honda_lokasi = lokasi['honda'].lokasi;
            Object.keys(honda_lokasi).map(function(item) {
                $('#filter_report #company').append(`<option value="${honda_lokasi[item].companyid}">${honda_lokasi[item].companyid}</option>`);
            });
        } else if ($(this).val().toLowerCase() == 'fdr') {
            $('.text-kd_part').text('Ukuran Ban');
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

        request_data();
    });

    $('#filter_urutkan #btn-smt').on('click', function() {
        if($('#filter_urutkan #urutkan_collom').val() != '' && $('#filter_urutkan #urutkan').val() != ''){
            request_data();
            $('#filter_urutkan').modal('hide');
        }
    });

    $('#table_list').on('change', '#per_page',function() {
        request_data();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });
    $('#table_list').on('click', '.pagination .page-item a.page-link',function() {
        request_data($(this).attr('href').split('page=')[1]);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    $('#kt_post').on('click', '#btn_export',function () {
        if(ada_data == false){
            Swal.fire({
                text: 'Atur Filter terlebih dahulu!',
                icon: "info",
                confirmButtonText: "OK !",
                customClass: {
                    confirmButton: "btn btn-info"
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#filter_report').modal('show');
                }
            });
            return false;
        }
        $.ajax({
            url: base_url + '/report/konsumen/daftar/export',
            method: 'POST',
            xhrFields: {
                responseType: 'blob'
            },
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

                filter: {[$('#filter_urutkan #urutkan_collom').val()] : $('#filter_urutkan #urutkan').val()},
            },
            beforeSend: function () {
                loading.block();
            }
        }).done(function (response) {
            if (response.status == '0') {
                toastr.error(response.message, "Error");
                return false;
            }
            if (response.status == '2') {
                swal.fire({
                    title: 'Perhatian!',
                    text: response.message,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
                return false;
            }
            
            var blob = new Blob([response], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'Konsumen (' + ($('#filter_report #divisi').val() != ''? ' divisi = ' + $('#filter_report #divisi').val() + ' ,' : '') + ($('#filter_report #company').val() != ''? ' company = ' + $('#filter_report #company').val()+ ' ,' : '') + ($('#filter_report #lokasi').val() != ''? ' lokasi = ' + $('#filter_report #lokasi').val()+ ' ,' : '') + (
                ($('#filter_report #tgl_tran').val() != '')? ' tgl transaksi = ' + $('#filter_report #tgl_tran').val()+ ' ,' : (($('#filter_report #tgl_tran0').val() != '') ? ' tgl transaksi = ' + $('#filter_report #tgl_tran0').val() + ' s/d ' + $('#filter_report #tgl_tran1').val()+ ' ,' : '')
            ) + (
                ($('#filter_report #tgl_lahir').val() != '')? ' ,tgl lahir = ' + $('#filter_report #tgl_lahir').val()+ ' ,' : (($('#filter_report #tgl_lahir1').val() != '') ? ' tgl lahir = ' + $('#filter_report #tgl_lahir1').val() + '-'+ $('#filter_report #tgl_lahir2').val() + ' s/d ' + $('#filter_report #tgl_lahir3').val() + '-' + $('#filter_report #tgl_lahir4').val()+ ' ,' : '')
            ) + ($('#filter_report #jenis_part').val() != ''? ' jenis part = ' + $('#filter_report #jenis_part').val()+ ' ,' : '') + ($('#filter_report #kd_part').val() != ''? ' kode part = ' + $('#filter_report #kd_part').val()+ ' ,' : '') + ($('#filter_report #merek_motor').val() != ''? ' merek motor = ' + $('#filter_report #merek_motor').val()+ ' ,' : '') + ($('#filter_report #tipe_motor').val() != ''? ' tipe motor = ' + $('#filter_report #tipe_motor').val()+ ' ,' : '') + ($('#filter_report #jenis_motor').val() != ''? ' jenis motor = ' + $('#filter_report #jenis_motor').val()+ ' ,' : '') + ').xlsx';
            link.click();
            link.remove();
        }).fail(function (jqXHR, textStatus, error) {
            Swal.fire({
                title: 'Perhatian!',
                text: 'Silahkan coba kembali, jika pesan ini masih muncul silahkan Filter data lebih spesifik.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }).always(function () {
            loading.release();
        });
    });
});