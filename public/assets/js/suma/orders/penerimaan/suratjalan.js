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

    function resetForm() {
        $('#tgl').val('');
        $('#tgl_terima').flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: moment().format('DD-MM-YYYY')
        });
        $('#jam_terima').val(moment().format('HH:mm:ss'));
        $('#dealer').val('');
        $('#nm_dealer').val('');
        $('#alamat_dealer').val('');
        $('#kota_dealer').val('');
    }

    // tanggal dan jam di isi saat ini 
    $('#tgl_terima').flatpickr({
        dateFormat: "d-m-Y",
        defaultDate: moment().format('DD-MM-YYYY')
    });
    $('#jam_terima').val(moment().format('HH:mm:ss'));
    // end tanggal dan jam di isi saat ini

    function createListModal(data) {
        $('#form_sj div.modal-footer > button').addClass('disabled');
        $('#tableSuratJalan #SuratJalanBody').html('');
        data.belum_terima.forEach(function (item) {
            $('#tableSuratJalan #SuratJalanBody').append(`
                <tr>
                    <td>
                        <div class="form-check form-check-custom form-check-solid form-check-lg">
                            <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault">
                        </div>
                    </td>
                    <td>${item.no_sj}</td>
                    <td>${moment(item.tanggal_sj).format('DD/MM/YYYY')}</td>
                    <td>${item.kode_dealer}</td>
                    <td>${item.nama_dealer}</td>
                    <td>${item.alamat}</td>
                    <td>${item.kota}</td>
                </tr>
            `);
        });

        function search() {
            var keyword = $('#input_search').val();
            $('#tableSuratJalan tr td:nth-child(2)').each(function () {
                if ($(this).text().toLowerCase().indexOf(keyword.toLowerCase()) == -1) {
                    $(this).parent().hide();
                } else {
                    $(this).parent().show();
                }
            });
        }

        // input_search on keyup
        $('#input_search').on('keyup', function () {
            search();
        });

        // $('#input_search'). jika di enter maka focus false
        $('#input_search').on('keydown', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                // maka jangan focus
                $(this).blur();
            }
        });
    }

    // no_st on change
    $('#no_st').on('change', function () {
        if ($(this).val() != '') {
            // ajax pangil data cek surat jalan
            $.ajax({
                url: url.cek_penerimaan_sj,
                type: 'GET',
                data: {
                    no_st: $('#no_st').val()
                },
                dataType: 'json',
                success: function (respon) {
                    if (respon.status == 1) {
                        $('#no_st').val(respon.data.no_serah_terima);
                        $('#no_sj').val('');
                        resetForm();
                        if (respon.data.belum_terima.length > 0 || respon.data.diterima.length > 0) {
                            data_sj = respon.data;
                            $('#no_st').removeClass('is-invalid');
                            $('#no_st').addClass('is-valid');
                            $('#no_st').next().remove();

                            if (respon.data.belum_terima.length > 0) {
                                createListModal(respon.data);

                                // reset ListModal
                                $('#suratjalanModal div.modal-footer > button#reset').on('click', function () {
                                    createListModal(data_sj);
                                    data = [];
                                    $('#list_ceked_sj > table > tbody').html('');
                                    $('#list_ceked_sj').css('display', 'none');
                                    $('#no_sj').val('');
                                    $('#no_sj').attr('readonly', false);
                                    $('#no_sj').removeClass('bg-secondary');
                                });
                            } else {
                                $('#form_sj div.modal-footer > button').addClass('disabled');
                                $('#tableSuratJalan #SuratJalanBody').html('');
                                $('#tableSuratJalan #SuratJalanBody').append(`
                                    <tr class="table-active">
                                        <td colspan="7" class="text-center">Tidak ada data yang perlu diterima</td>
                                    </tr>
                                `);
                            }
                        } else {
                            $('#form_sj div.modal-footer > button').addClass('disabled');
                            $('#tableSuratJalan #SuratJalanBody').html('');
                            $('#tableSuratJalan #SuratJalanBody').append(`
                                <tr class="table-active">
                                    <td colspan="7" class="text-center">Tidak ada data yang bisa diterima atau dihapus</td>
                                </tr>
                            `);
                        }
                    } else {
                        $('#no_st').removeClass('is-valid');
                        $('#no_st').addClass('is-invalid');
                        $('#no_st').next().remove();
                        $('#no_st').after(`
                            <div class="invalid-feedback">
                                ${respon.message}
                            </div>
                        `);
                        $('#no_sj').val('');
                        resetForm();
                        $('#form_sj > div.modal-footer > button').addClass('disabled');
                    }
                },
            });
            // end ajax pangil data cek surat jalan
        }
    });
    // end no_st on change

    function isiDataValue(ada_data) {
        $('#no_sj').val(ada_data.no_sj);
        $('#tgl').val(moment(ada_data.tanggal_sj).format('DD-MM-YYYY'));
        $('#dealer').val(ada_data.kode_dealer);
        $('#nm_dealer').val(ada_data.nama_dealer);
        $('#alamat_dealer').val(ada_data.alamat);
        $('#kota_dealer').val(ada_data.kota);
    }

    // #no_sj change dan enter
    $('#no_sj').on('change keyup', function (e) {
        if (e.which == 13 || e.type == 'change') {
            if ($('#no_st').val() == '') {
                $('#no_st').addClass('is-invalid');
                $('#no_st').next('span').remove();
                $('#no_st').after('<span class="invalid-feedback">No Surat Terima tidak boleh kosong</span>');

                // reset form
                $('#no_sj').val('');

            } else if ($('#no_sj').val() == '') {
                $('#no_sj').addClass('is-invalid');
                $('#btn_no_sj').next('span').remove();
                $('#btn_no_sj').after('<span class="invalid-feedback">No Surat Jalan tidak boleh kosong</span>');

                // reset form
                $('#no_sj').val('');
                resetForm();
                $('#form_sj div.modal-footer > button').addClass('disabled');
            } else if ($('#no_sj').val() != '' && $('#no_st').val() != '' && $('#no_st').hasClass('is-valid')) {

                // convert no_st menjadi 5 digit
                let no_sj = '';
                for (let i = $(this).val().length; i < 5; i++) {
                    no_sj += '0';
                }
                // end convert no_sj menjadi 5 digit
                no_sj = no_sj + $(this).val();

                $('#list_ceked_sj').css('display', 'none');
                $('#no_sj').attr('readonly', false);
                $('#no_sj').removeClass('bg-secondary');

                $('#no_sj').removeClass('is-invalid');
                $('#btn_no_sj').next('span').remove();

                let belum_terima = data_sj.belum_terima;
                let diterima = data_sj.diterima;
                let ada_data;
                belum_terima.forEach(function (item, index) {
                    if (item.no_sj.includes(no_sj)) {
                        ada_data = {
                            'no_sj': item.no_sj,
                            'tanggal_sj': item.tanggal_sj,
                            'kode_dealer': item.kode_dealer,
                            'nama_dealer': item.nama_dealer,
                            'alamat': item.alamat,
                            'kota': item.kota,
                            'tanggal_terima': item.tanggal_terima ?? '',
                            'jam_terima': item.jam_terima ?? '',
                            'status': 0,
                        }
                    }
                });
                if (ada_data == undefined) {
                    diterima.forEach(function (item, index) {
                        if (item.no_sj.includes($('#no_sj').val())) {
                            ada_data = {
                                'no_sj': item.no_sj,
                                'tanggal_sj': item.tanggal_sj,
                                'kode_dealer': item.kode_dealer,
                                'nama_dealer': item.nama_dealer,
                                'alamat': item.alamat,
                                'kota': item.kota,
                                'tanggal_terima': item.tanggal_terima ?? '',
                                'jam_terima': item.jam_terima ?? '',
                                'status': 1,
                            }
                        }
                    });
                }

                if (ada_data != undefined) {
                    if (ada_data.status == 0) {
                        isiDataValue(ada_data);

                        $('#tgl_terima').attr('readonly', false);
                        $('#tgl_terima').removeClass('bg-secondary');
                        $('#jam_terima').attr('readonly', false);
                        $('#jam_terima').removeClass('bg-secondary');


                        $('#foto').parent().removeClass('col-md-3');
                        $('div > label[for="foto"]').text('Upload Gambar');
                        $('#foto').replaceWith('<input class="form-control" type="file" id="foto" name="foto" accept="image/*">');

                        $('#form_sj').attr('action', url.surat_jalan_simpan);
                        $('#form_sj div.modal-footer > button').text('DI Terima');
                        $('#form_sj div.modal-footer > button').addClass('btn-scuccess');
                        $('#form_sj div.modal-footer > button').removeClass('btn-danger');
                        $('#form_sj div.modal-footer > button').removeClass('disabled');

                        $('#suratjalanModalForm > div.modal-header > div.btn').trigger('click');

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
                    } else if (ada_data.status == 1) {
                        isiDataValue(ada_data);

                        $('#tgl_terima').attr('readonly', true);
                        $('#tgl_terima').addClass('bg-secondary');
                        $('#tgl_terima').val(moment(ada_data.tanggal_terima).format('DD-MM-YYYY'));
                        $('#jam_terima').attr('readonly', true);
                        $('#jam_terima').addClass('bg-secondary');
                        $('#jam_terima').val(ada_data.jam_terima);

                        $('#foto').parent().addClass('col-md-3');
                        $('div > label[for="foto"]').text('Gambar');
                        $('#foto').replaceWith(`<img id="foto" src="${url.url_image}/${$('#no_st').val().replace(/\//g, '') + '_' + ada_data.no_sj.replace(/\//g, '')}.jpg" class="img-thumbnail" alt="Tidak Ada Gambar" style="cursor: pointer;">`);

                        $('#foto').on('click', function () {
                            window.open(url.url_image + '/' + $('#no_st').val().replace(/\//g, '') + '_' + ada_data.no_sj.replace(/\//g, '') + '.jpg', '_blank');
                        });

                        $('#form_sj').attr('action', url.surat_jalan_hapus);
                        $('#form_sj div.modal-footer > button').text('Hapus');
                        $('#form_sj div.modal-footer > button').removeClass('btn-scuccess');
                        $('#form_sj div.modal-footer > button').addClass('btn-danger');
                        $('#form_sj div.modal-footer > button').removeClass('disabled');

                        $('#suratjalanModalForm > div.modal-header > div.btn').trigger('click');

                        $('#btn_kirim.btn-danger').on('click', function () {
                            // console.log elemnt yang di klik
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
                    }
                } else {
                    swal.fire({
                        title: "Surat Jalan Tidak Ditemukan!",
                        icon: "warning",
                        showCancelButton: false,
                        confirmButtonText: "OK",
                        reverseButtons: true,
                        customClass: {
                            confirmButton: "btn btn-danger",
                        },
                    }).then(function (result) {
                        if (result.value) {
                            $('#no_sj').val('');
                            $('#no_sj').focus();
                            resetForm();
                            $('#form_sj div.modal-footer > button').addClass('disabled');
                        }
                    });
                }
            }
        }
    });
    // #no_sj change


    // cek saat btn_no_sj di klik cek no_sj
    $('#btn_no_sj').on('click', function () {
        if ($('#no_st').val() != '') {
            $('#suratjalanModal').modal('show');
        } else {
            $('#no_st').addClass('is-invalid');
            $('#no_st').next('span').remove();
            $('#no_st').after('<span class="invalid-feedback">No Surat Terima tidak boleh kosong</span>');
        }
    });
    // cek saat btn_kirim di klik cek no_sj

    // modal show
    $('#suratjalanModal').on('show.bs.modal', function (e) {
        // saat sudah memilih  pada surat jalan
        $('#pilih_sj').on('click', function () {
            var data = [];
            // cek apakah ada data yang di pilih dengan cara meloooping tr yang ada pada tbody
            $('#tableSuratJalan tbody tr').each(function () {
                // cek jika ada yang di ceklis
                if ($(this).find('input[type="checkbox"]').is(':checked')) {
                    // push in array
                    data.push({
                        no_sj: $(this).find('td:eq(1)').text(),
                        tgl: $(this).find('td:eq(2)').text(),
                        dealer: $(this).find('td:eq(3)').text(),
                        nama_dealer: $(this).find('td:eq(4)').text(),
                        alamat_dealer: $(this).find('td:eq(5)').text(),
                        kota_dealer: $(this).find('td:eq(6)').text()
                    });
                    // end push in array
                }
                // end cek jika ada yang di ceklis
            });
            // end cek apakah ada data yang di pilih dengan cara meloooping tr yang ada pada tbody

            if (data.length > 0) {
                if (data.length > 1) {
                    // membuat list no_sj yang di card
                    $('#list_ceked_sj > table > tbody').html('');
                    data.forEach(function (item) {
                        $('#list_ceked_sj > table > tbody').append(`
                            <tr class="border-bottom">
                                <td>${item.no_sj}</td>
                                <td>
                                    <span class="btn_unceklist  badge badge-danger" style="cursor: pointer;">
                                        <i class="bi bi-x-lg text-white"></i>
                                    </span>
                                </td>
                            </tr>
                        `);
                    });
                    // end membuat list no_sj yang di card
                    $('#list_ceked_sj').css('display', '');
                    $('#no_sj').val('');
                    $('#no_sj').attr('readonly', true);
                    $('#no_sj').addClass('bg-secondary');

                    // saat btn_unceklist di klik
                    $('#list_ceked_sj > table > tbody > tr > td > span.btn_unceklist').on('click', function () {
                        var no_sj = $(this).parent().parent().find('td:eq(0)').text();
                        data = data.filter(function (data) {
                            return data.no_sj != no_sj;
                        });
                        // SuratJalanBody hapus tr yang di ceklis dan no_sj nya sama dengan no_sj yang di klik
                        $('#tableSuratJalan tbody tr').each(function () {
                            if ($(this).find('td:eq(1)').text() == no_sj) {
                                $(this).find('input[type="checkbox"]').prop('checked', false);
                            }
                        });
                        // end SuratJalanBody hapus tr yang di ceklis dan no_sj nya sama dengan no_sj yang di klik
                        $(this).parent().parent().remove();
                        // jika list no_sj yang di card tinggal 1
                        if (data.length == 1) {
                            $('#no_sj').val(data[0].no_sj);
                            $('#no_sj').trigger('change');
                        }
                        // end jika list no_sj yang di card tinggal 1
                    });
                    // end saat btn_unceklist di klik

                    $('#tgl').val('custom');
                    $('#dealer').val('custom');
                    $('#nm_dealer').val('custom');
                    $('#alamat_dealer').val('custom');
                    $('#kota_dealer').val('custom');


                    $('#foto').parent().removeClass('col-md-3');
                    $('div > label[for="foto"]').text('Upload Gambar');
                    $('#foto').replaceWith('<input class="form-control" type="file" id="foto" name="foto" accept="image/*">');

                    $('#form_sj').attr('action', url.surat_jalan_simpan);
                    $('#form_sj div.modal-footer > button').text('DI Terima');
                    $('#form_sj div.modal-footer > button').addClass('btn-scuccess');
                    $('#form_sj div.modal-footer > button').removeClass('btn-danger');
                    $('#form_sj div.modal-footer > button').removeClass('disabled');

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
                                // isi no_sj dengan array data
                                $('#no_sj').val(JSON.stringify(data));
                                $('#form_sj').submit();
                            } else if (result.dismiss === "cancel") {
                            }
                        });
                    });
                } else {
                    $('#no_sj').val(data[0].no_sj);
                    $('#no_sj').trigger('change');
                }

                // close modal
                $('#suratjalanModal').modal('hide');
            } else {
                swal.fire({
                    title: "Peringatan",
                    text: "Silahkan pilih data terlebih dahulu",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            }
        });
        // end saat sudah memilih
    });
});