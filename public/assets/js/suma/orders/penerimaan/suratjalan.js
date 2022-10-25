$(document).ready(function () {

    // saat tambah diskon di klik focus ke pruduk dan merubah tombol enter menjadi tab
    $('.card-body').find('input').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            var index = $('.card-body').find('input').index(this) + 1;
            $('.card-body').find('input').eq(index).focus();
        }
    });
    // end saat tambah diskon

    // terdapat form submit loading
    $('form').submit(function () {
        loading.block();
    });

    // ajax start
    $(document).ajaxStart(function () {
        loading.block();
    });
    // end ajax start
    // ajax stop
    $(document).ajaxStop(function () {
        loading.release();
    });
    // end ajax stop

    // $('#tgl_terima').val(moment().format('DD-MM-YYYY'));
    $('#tgl_terima').flatpickr({
        dateFormat: "d-m-Y",
        defaultDate: moment().format('DD-MM-YYYY')
    });
    $('#jam_terima').val(moment().format('HH:mm:ss'));

    $('#no_sj').on('change', function () {
        suratJalan($(this).val());
    });

    function suratJalan(data) {
        $.ajax({
            url: url.cek_penerimaan_sj,
            type: "GET",
            data: {
                nomor_sj: data
            },
            success: function (data) {
                if (data.status == 0) {
                    $('#no_sj').removeClass('is-invalid');
                    $('#no_sj').addClass('is-valid');

                    $('#tgl_terima').attr('readonly', false);
                    $('#tgl_terima').removeClass('bg-secondary');
                    $('#jam_terima').attr('readonly', false);
                    $('#jam_terima').removeClass('bg-secondary');

                    $('#no_sj').val(data.data_sj.no_sj);
                    $('#tgl').val(moment(data.data_sj.tanggal_sj).format('DD-MM-YYYY'));

                    $('#dealer').val(data.data_sj.kode_dealer);
                    $('#nm_dealer').val(data.data_sj.nama_dealer);
                    $('#alamat_dealer').val(data.data_sj.alamat_dealer);
                    $('#kota_dealer').val(data.data_sj.kota);

                    $('#foto').parent().removeClass('col-md-3');
                    $('div > label[for="foto"]').text('Upload Gambar');
                    $('#foto').replaceWith('<input class="form-control" type="file" id="foto" name="foto">');

                    $('#form_sj').attr('action', url.surat_jalan_simpan);
                    $('div.modal-footer > button').text('DI Terima');
                    $('div.modal-footer > button').removeClass('btn-danger');
                    $('div.modal-footer > button').addClass('btn-scuccess');

                    $('#btn_kirim.btn-success').on('click', function () {
                        swal.fire({
                            title: "Apakah Anda Yakin Ingin Menerima Surat Jalan?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Ya, Terima!",
                            cancelButtonText: "Tidak, Batalkan!",
                            reverseButtons: true,
                            customClass: {
                                confirmButton: "btn btn-success",
                                cancelButton: "btn btn-secondary"
                            }
                        }).then(function (result) {
                            if (result.value) {
                                $('#form_sj').submit();
                            } else if (result.dismiss === "cancel") {
                            }
                        });
                    });
                } else if (data.status == 1) {
                    swal.fire({
                        title: "Peringatan",
                        text: "Surat Jalan Sudah Di Terima!",
                        icon: "warning",
                        showCancelButton: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                    });

                    $('#no_sj').removeClass('is-invalid');
                    $('#no_sj').addClass('is-valid');

                    $('#tgl_terima').attr('readonly', true);
                    $('#tgl_terima').addClass('bg-secondary');
                    $('#jam_terima').attr('readonly', true);
                    $('#jam_terima').addClass('bg-secondary');

                    $('#no_sj').val(data.data_sj.no_sj);
                    $('#tgl').val(moment(data.data_sj.tanggal_sj).format('DD-MM-YYYY'));

                    $('#dealer').val(data.data_sj.kode_dealer);
                    $('#nm_dealer').val(data.data_sj.nama_dealer);
                    $('#alamat_dealer').val(data.data_sj.alamat_dealer);
                    $('#kota_dealer').val(data.data_sj.kota);


                    $('#foto').parent().addClass('col-md-3');
                    $('div > label[for="foto"]').text('Gambar');
                    $('#foto').replaceWith(`<img id="foto" src="${url.url_image}/${data.data_sj.images}.jpg" class="img-thumbnail" alt="Tidak Ada Gambar" style="cursor: pointer;">`);

                    $('#foto').on('click', function () {
                        window.open(url.url_image + '/' + data.data_sj.images + '.jpg', '_blank');
                    });

                    $('#form_sj').attr('action', url.surat_jalan_hapus);
                    $('div.modal-footer > button').text('Hapus');
                    $('div.modal-footer > button').removeClass('btn-scuccess');
                    $('div.modal-footer > button').addClass('btn-danger');

                    $('#btn_kirim.btn-danger').on('click', function () {
                        swal.fire({
                            title: "Apakah Anda Yakin Ingin Menghapus Surat Jalan?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Ya, Hapus!",
                            cancelButtonText: "Tidak, Batalkan!",
                            reverseButtons: true,
                            customClass: {
                                confirmButton: "btn btn-danger",
                                cancelButton: "btn btn-secondary"
                            },
                        }).then(function (result) {
                            if (result.value) {
                                $('#form_sj').submit();
                            } else if (result.dismiss === "cancel") {
                            }
                        });
                    });
                } else if (data.status == 404) {
                    $('#no_sj').addClass('is-invalid');
                    $('#no_sj').removeClass('is-valid');
                    $('#no_sj').focus();

                    $('#tgl_terima').attr('readonly', false);
                    $('#tgl_terima').removeClass('bg-secondary');
                    $('#jam_terima').attr('readonly', false);
                    $('#jam_terima').removeClass('bg-secondary');

                    $('div.modal-footer > button').text('DI Terima');
                    $('div.modal-footer > button').removeClass('btn-danger');
                    $('div.modal-footer > button').addClass('btn-scuccess');
                    $('.modal-footer button').attr('type', 'button');

                    swal.fire({
                        title: "Peringatan",
                        text: "Surat Jalan Tidak Ditemukan!",
                        icon: "warning",
                        showCancelButton: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                    });
                }
            }
        });
    }
});