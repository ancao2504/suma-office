
function validasi_input(target, massage) {
    if($(target).val() == ''){
        $(target).addClass('is-invalid');
        if($(target).next().is('div.invalid-feedback')){
            $(target).next().html(massage);
        }else{
            $(target).after('<div class="invalid-feedback">'+massage+'</div>');
        }
    } else {
        $(target).removeClass('is-invalid');
        $(target).next().remove();
        return true;
    }
}

function validasi_input_group(target, massage){
    if($(target).val() == ''){
        $(target).addClass('is-invalid');
        if($(target).closest('div.input-group').find('div.invalid-feedback').length > 0){
            $(target).closest('div.input-group').find('div.invalid-feedback').html(massage);
        }else{
            $(target).closest('div.input-group').append('<div class="invalid-feedback">'+massage+'</div>');
        }
    } else {
        $(target).removeClass('is-invalid');
        $(target).closest('div.input-group').find('div.invalid-feedback').remove();
        return true;
    }
}

function wh(requst) {
    loading.block();
        $('#modal_nowh').find('tbody').html(`
        <tr>
            <td colspan="11" class="text-center text-primary">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </td>
        </tr>`);

    $.get(base_url + '/wh',{
        option: requst.option,
        no_wh: requst.no_wh,
        page: requst.page,
        per_page: requst.per_page
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    toastr.error('Nomor WH Tidak Ditemukan!', "info");
                    $('#no_dok').val('');
                    $('#no_dok').addClass('is-invalid');
                    $('#no_dok').removeClass('is-valid');
                } else {
                    $('#no_dok').val(dataJson.no_dok);
                    $('#no_dok').addClass('is-valid');
                    $('#no_dok').removeClass('is-invalid');

                    $('#keterangan_info').text(dataJson.ket??'-');
                    $('#Expedisi_info').text(dataJson.kd_ekspedisi??'-');
                    $('#Nama_info').text(dataJson.nm_dealer??'-');

                    if(dataJson.tgl_start != null && dataJson.tgl_finish == null){
                        toastr.warning('Nomor WH Sedang diproses!', "info");
                        timer.tanggal = moment(dataJson.tgl_start, 'YYYY-MM-DD');
                        timer.jam = moment(dataJson.jam_start, 'HH:mm:ss');
                        $('#btn_submit_start').attr("disabled", true);
                        $('.timer-container').attr("hidden", false);
                    }
            
                    if(dataJson.tgl_start != null && dataJson.tgl_finish != null){
                        $('#btn_submit_start').attr("disabled", true);
                        $('#btn_submit_finish').attr("disabled", true);
                        $('#btn_submit_reset').attr("hidden", false);
                        $('.timer-container').attr("hidden", true);

                        toastr.warning('Nomor WH Sudah di proses Packing!', "info");
                    }

                    if(dataJson.tgl_start == null && dataJson.tgl_finish == null){
                        $('#btn_submit_start').attr("disabled", false);
                        $('#btn_submit_finish').attr("disabled", true);

                        toastr.success('Nomor WH di temukan!', "info");
                    }
                }
            } else if (requst.option == 'page') {
                $('#modal_nowh').html(dataJson);
                $('#modal_nowh').modal('show');
            }
        } else {
            $('#modal_nowh').modal('hide');
        }
        if (response.status == '0') {
            toastr.error(response.message, "Error");
            $('#no_dok').addClass('is-invalid');
            $('#no_dok').removeClass('is-valid');
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
        }
    }).always(function () {
        loading.release();
    }).fail(function (jqXHR, textStatus, error) {
        $('#modal_nowh .close').trigger('click')
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: 'Maaf terjadi kesalahan, mohon coba kembali!',
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-secondary'
            },
            allowOutsideClick: false
        });
    });
}

function simpan(sts_packing){
    loading.block();
    $.post(base_url + '/gudang/packing/online/form',
    {
        _token: $('meta[name="csrf-token"]').attr('content'),
        no_dok: $('#no_dok').val(),
        no_meja: $('#no_meja').val(),
        kd_packer: $('#kd_packer').val(),
        sts_packing: sts_packing
    },
    function (response) {
        if (response.status == '1') {
            if (sts_packing == 'selesai') {
                $('#btn_submit_start').attr("disabled", true);
                $('#btn_submit_finish').attr("disabled", true);
                $('.timer-container').attr("hidden", true);
                swal.fire({
                    title: 'Perhatian!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                $('#btn_submit_start').attr("disabled", true);
                $('#btn_submit_finish').attr("disabled", false);
                $('#btn_submit_reset').attr("hidden", true);
                $('.timer-container').attr("hidden", false);
                
                timer.tanggal = moment(response.data.tgl_start, 'YYYY-MM-DD');
                timer.jam = moment(response.data.jam_start, 'HH:mm:ss');
            }
        }
        if (response.status == '0') {
            toastr.warning(response.message, "Peringatan");
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
        }
    }).always(function () {
        loading.release();
    }).fail(function (err) {
        swal.fire({
            title: 'Perhatian!',
            text: 'Terjadi Kesalahan, Silahkan Coba Lagi',
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
    });
}

let timer = {
    tanggal:moment().format('DD/MM/YYYY'),
    jam:moment().format('HH:mm:ss')
}
$(document).ready(function() {
    $('#no_meja').val(old.no_meja);

    $('#no_dok').on('change', function () {
        if ($(this).val() != '') {
            wh({
                option: 'first',
                no_wh: $(this).val(),
            });
        }
    })
    $('#btn_nowh').on('click', function () {
        wh({
            option: 'page',
            no_wh: $('#modal_nowh #cari').val(),
            page: 1,
            per_page: 10
        });
    })
    $('#modal_nowh').on('change','#per_page', function () {
        wh({
            option: 'page',
            no_wh: $('#modal_nowh #cari').val(),
            page: 1,
            per_page: $(this).val()
        });
    })
    $('#modal_nowh').on('click', '.pagination a.page-link', function () {
        wh({
            option: 'page',
            no_wh: $('#modal_nowh #cari').val(),
            page: $(this).attr('href').split('?page=')[1],
            per_page: $('#modal_nowh #per_page').val(),
        });
    });
    $('#modal_nowh').on('click','#btn_cari', function () {
        wh({
            'option': 'page',
            'no_wh': $('#modal_nowh #cari').val(),
            'page': 1,
            'per_page': 10,
        });
    });

    $('#modal_nowh').on('click','.pilih', function () {
        const data_complate = JSON.parse(atob($(this).data('kd')));
        $('#no_dok').val(data_complate.no_dok);
        $('#no_dok').addClass('is-valid');
        $('#no_dok').removeClass('is-invalid');

        $('#keterangan_info').text(data_complate.ket??'-');
        $('#Expedisi_info').text(data_complate.kd_ekspedisi??'-');
        $('#Nama_info').text(data_complate.nm_dealer??'-');
        $('#modal_nowh').modal('hide');

        if(data_complate.tgl_start != null && data_complate.tgl_finish == null){
            timer.tanggal = moment(data_complate.tgl_start, 'YYYY-MM-DD');
            timer.jam = moment(data_complate.jam_start, 'HH:mm:ss');
            $('#btn_submit_start').attr("disabled", true);
            $('.timer-container').attr("hidden", false);
        }

        if(data_complate.tgl_start != null && data_complate.tgl_finish != null){
            $('#btn_submit_start').attr("disabled", true);
            $('#btn_submit_finish').attr("disabled", true);
            $('#btn_submit_reset').attr("hidden", false);
            $('.timer-container').attr("hidden", true);
        }

        if(data_complate.tgl_start == null && data_complate.tgl_finish == null){
            $('#btn_submit_start').attr("disabled", false);
            $('#btn_submit_finish').attr("disabled", true);
        }
    });



    $('#btn_submit_start').on('click', function(){
        let valid = {
            no_meja: validasi_input('#no_meja', 'Nomor Meja tidak boleh kosong!'),
            kd_packer: validasi_input('#kd_packer', 'Packer tidak boleh kosong!'),
            no_dok: validasi_input_group('#no_dok', 'Nomor WH tidak boleh kosong!'),
        };

        if(valid.no_meja && valid.kd_packer && valid.no_dok){
            $('#btn_nowh').attr("disabled", true);

            simpan('mulai');
        }
    });
    $('#btn_submit_finish').on('click', function(){
        let valid = {
            no_meja: validasi_input('#no_meja', 'Nomor Meja tidak boleh kosong!'),
            kd_packer: validasi_input('#kd_packer', 'Packer tidak boleh kosong!'),
            no_dok: validasi_input_group('#no_dok', 'Nomor WH tidak boleh kosong!'),
        };

        if(valid.no_meja && valid.kd_packer && valid.no_dok){
            simpan('selesai');
        }
    });

    $('#btn_submit_reset').on('click', function(){
        let valid = {
            no_meja: validasi_input('#no_meja', 'Nomor Meja tidak boleh kosong!'),
            kd_packer: validasi_input('#kd_packer', 'Packer tidak boleh kosong!'),
            no_dok: validasi_input_group('#no_dok', 'Nomor WH tidak boleh kosong!'),
        };

        if(valid.no_meja && valid.kd_packer && valid.no_dok){
            swal.fire({
                title: 'Perhatian!',
                text: 'Apakah anda yakin Mengulangi Proses Packing ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Iya!",
                cancelButtonText: "Tidak!",
                customClass: {
                    confirmButton: "btn btn-warning",
                    cancelButton: "btn btn-secondary"
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    simpan('reset');
                }
            });
        }
    });

    function updateTimer() {
        let diffDuration = moment.duration(moment().diff(moment(timer.jam, 'HH:mm:ss')));
        document.getElementById('timer').innerText = `${diffDuration.hours().toString().padStart(2, '0')}:${diffDuration.minutes().toString().padStart(2, '0')}:${diffDuration.seconds().toString().padStart(2, '0')}`;
    }
    setInterval(updateTimer, 1000);
});