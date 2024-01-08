function lokasi() {
    $('#company').html(``);
    for (const [key, item] of Object.entries(company[$('input[name="divisi"]:checked').val()].lokasi)) {
        if(old.company != '' && old.company.toUpperCase() == item.companyid.toUpperCase()){
            $('#company').append(`<option value="${item.companyid}" selected>${item.companyid}</option>`);
        } else {
            $('#company').append(`<option value="${item.companyid}">${item.companyid}</option>`);
        }
    };

    $('#lokasi').html('');
    for (const [key, item] of Object.entries(company[$('input[name="divisi"]:checked').val()].lokasi)) {
        if(item.companyid == $('#company').val()){
            item.kd_lokasi.forEach(item => {
                if (old.lokasi != '' && old.lokasi.toUpperCase() == item.toUpperCase()) {
                    $('#lokasi').append(`<option value="${item}" selected>${item}</option>`);
                    return false;
                } else {
                    $('#lokasi').append(`<option value="${item}">${item}</option>`);
                }
            });
        }
    };
}

function typemotor(){
    $('#tipe_motor').html('');
    $('#tipe_motor').append('<option></option>');
    $.each(tipemotor[$('#merk_motor').val()], function (key, value) {
        if(old.tipe != '' && old.tipe.toUpperCase() == value.TypeMotor.toUpperCase()){
            $('#tipe_motor').append(`<option value="${value.TypeMotor}" selected>${value.TypeMotor}</option>`);
        } else {
            $('#tipe_motor').append(`<option value="${value.TypeMotor}">${value.TypeMotor}</option>`);
        }
    });
    $('#tipe_motor').append(`<option value="Lainya">Lainya</option>`);
    $('#tipe_motor_lainya').attr('hidden', true);
    $('#tipe_motor_lainya').attr('required', false);
    $('#tipe_motor').parent().removeClass('col-lg-6').addClass('col-lg-12');
}

let status = {
    faktur: false,
}

function cekfaktur(){
    loading.block();
    $.ajax({
        url: base_url + '/faktur/konsumen',
        type: "GET",
        data: {
            option      : 'first',
            divisi      : $('input[name="divisi"]:checked').val(),
            no_faktur   : $('#inputNomorFaktur').val(),
            companyid   : $('#company').val(),
            kd_lokasi   : $('#lokasi').val(),
            id_konsumen : ($('#inputId').val() == ''?1:$('#inputId').val())
        },
        success: function (response) {
            if (response.status == '1') {
                if(response.data == null){
                    $('#inputNomorFaktur').removeClass('is-valid').addClass('is-invalid').focus();
                    $('#inputNomorFaktur').siblings('.invalid-feedback').length === 0 ? $('#inputNomorFaktur').after('<div class="invalid-feedback">Maaf Nomor Faktur tidak ditemukan!</div>') : $('#inputNomorFaktur').siblings('.invalid-feedback').text('Maaf Nomor Faktur tidak ditemukan!');
                    status.faktur = true;
                    return false;
                }

                if (response.data.id != null){
                    Swal.fire({
                        text: 'Maaf Nomor Faktur sudah digunakan!',
                        icon: "warning",
                        confirmButtonText: "OK !",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#inputNomorFaktur').val('').focus();
                            $('#inputTotalFaktur').val('');
                        }
                    });
                    status.faktur = true;
                    return false;
                }

                status.faktur = true;
                $('#inputNomorFaktur').val(response.data.no_faktur);
                $('#inputNomorFaktur').removeClass('is-invalid').addClass('is-valid');
                $('#inputNomorFaktur').siblings('.invalid-feedback').remove();

                const options = { style: 'decimal', useGrouping: true, minimumFractionDigits: 0, maximumFractionDigits: 2, minimumIntegerDigits: 1 };
                $('#inputTotalFaktur').val(Number(response.data.total).toLocaleString('en-US', options));
            }
            if (response.status == '0') {
                $('#inputNomorFaktur').removeClass('is-valid').addClass('is-invalid').focus();
                $('#inputNomorFaktur').siblings('.invalid-feedback').length === 0 ? $('#inputNomorFaktur').after('<div class="invalid-feedback">' + response.message + '</div>') : $('#inputNomorFaktur').siblings('.invalid-feedback').text(response.message);
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
        }
    }).always(function () {
        loading.release();
    }).fail(function (jqXHR, textStatus, error) {
        $('#dealer-list .close').trigger('click')
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: textStatus,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-secondary'
            },
            allowOutsideClick: false
        });
    });
}
function ceknik(request){
    $.ajax({
        url: base_url + '/option/konsumen',
        type: "GET",
        data: {
            option      : request.option,
            divisi      : $('input[name="divisi"]:checked').val(),
            nik         : $('#inputNIK').val(),
            search      : $('#autocomplateKonsumen').find('#cari').val(),
            page        : request.page??1,
            per_page    : request.per_page??10,
        },
        beforeSend: function () {
            loading.block();
        },
        success: function (response) {
            if (response.status == '1') {
                if(request.option == 'first'){
                    if(response.data == null){
                        toastr.info('Belum ada data pelanggan', "Info");
                        return false;
                    }

                    toastr.info('Pelanggan ditemukan', "Info");
                    $('#messageError').html('');
                    $('#inputNIK').val(response.data.nik);
                    $('#inputNamaPelanggan').val(response.data.nama);
                    $('#inputTempatLahir').val(response.data.tempat_lahir);
                    $("#inputTanggalLahir").flatpickr({
                        dateFormat: "d-m-Y",
                        defaultDate: moment(response.data.tgl_lahir).format('DD-MM-YYYY'),
                    });
                    $('#inputTelepon').val(response.data.telepon);
                    $('#inputAlamat').val(response.data.alamat);
                    $('#inputEmail').val(response.data.email);
                    $('#inputNopol').val(response.data.nopol);
                } else if(request.option == 'page'){
                    $('#autocomplateKonsumen').html(response.data);
                }
            }
            if (response.status == '0') {
                toastr.error(response.message, "Error");
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
        }
    }).done(function () {
        if(request.option == 'page'){
            $('#autocomplateKonsumen').modal('show');
        }
    }).always(function () {
        loading.release();
    }).fail(function (jqXHR, textStatus, error) {
        $('#dealer-list .close').trigger('click')
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: textStatus,
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
    });

    loading.release();
}
function validasi (targert, message) {
    if ( Array.isArray(targert) ) {
        let valid = true;
        targert.forEach(function (item) {
            if ($(item).val() == '') {
                $(item).addClass('is-invalid').focus();
                $(item).parent('.input-group').addClass('has-validation');
                $(targert[targert.length - 1]).siblings('.invalid-feedback').length === 0 ? $(targert[targert.length - 1]).after('<div class="invalid-feedback">' + message + '</div>') : $(targert[targert.length - 1]).siblings('.invalid-feedback').text(message);
                valid = false;
                return false;
            }
            $(item).removeClass('is-invalid');
            $(item).parent('.input-group').removeClass('has-validation');
            $(item).next('invalid-feedback').remove();
        });
        return valid;
    } else if (typeof targert == 'string') {
        if ($(targert).val() == '') {
            $(targert).addClass('is-invalid').focus();
            $(targert).siblings('.invalid-feedback').length === 0 ? $(targert).after('<div class="invalid-feedback">' + message + '</div>') : $(targert).siblings('.invalid-feedback').text(message);
            return false;
        }
        $(targert).removeClass('is-invalid');
        $(targert).next('invalid-feedback').remove();
        return true;
    }
}

function simpan(){
    $.ajax({
        url: base_url + '/konsumen/store',
        type: "POST",
        data: {
            _token                  : $('meta[name="csrf-token"]').attr('content'),
            id                      : $('#inputId').val(),
            divisi                  : $('input[name="divisi"]:checked').val(),
            companyid               : $('#company').val(),
            kd_lokasi               : $('#lokasi').val(),
            nomor_faktur            : $('#inputNomorFaktur').val(),
            nik                     : $('#inputNIK').val(),
            nama_pelanggan          : $('#inputNamaPelanggan').val(),
            tempat_lahir            : $('#inputTempatLahir').val(),
            tanggal_lahir           : $('#inputTanggalLahir').val(),
            alamat                  : $('#inputAlamat').val(),
            telepon                 : $('#inputTelepon').val(),
            email                   : $('#inputEmail').val(),
            nopol                   : $('#inputNopol').val(),
            merk_motor              : ($('#merk_motor').val() == 'Lainya') ? $('#merk_motor_lainya').val() : $('#merk_motor').val(),
            tipe_motor              : ($('#tipe_motor').val() == 'Lainya') ? $('#tipe_motor_lainya').val() : $('#tipe_motor').val(),
            jenis_motor             : $('#inputJenisMotor').val(),
            tahun_motor             : $('#inputTahunMotor').val(),
            keterangan              : $('#inputKeterangan').val(),
            mengetahui              : $('#selectMengetahui').val(),
            keterangan_mengetahui   : $('#inputKeteranganMengetahui').val(),
        },
        beforeSend: function () {
            loading.block();
        }
    }).done(function (response) {
        if (response.status == '0') {
            toastr.error(response.message, "Error");
            return false;
        }else if (response.status == '1') {
            swal.fire({
                title: 'Berhasil!',
                text: response.message,
                icon: 'success',
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
        }else if (response.status == '2') {
            swal.fire({
                title: 'Berhasil!',
                text: response.message,
                icon: 'success',
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
    }).fail(function (jqXHR, textStatus, error) {
        Swal.fire({
            title: 'Perhatian!',
            text: 'Maaf terjadi kesalahan, silahkan coba lagi',
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
}

$(document).ready(function () {
    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');

    lokasi();
    $('input[name="divisi"]').on('change', function () {
        lokasi();
        if($('#inputNomorFaktur').val() != ''){
            cekfaktur();
        }
    });
    $('#company').on('change', function () {
        $('#lokasi').html('');
        for (const [key, item] of Object.entries(company[$('input[name="divisi"]:checked').val()].lokasi)) {
            if(item.companyid == $('#company').val()){
                item.kd_lokasi.forEach(item => {
                    $('#lokasi').append(`<option value="${item}">${item}</option>`);
                });
            }
        };
    });

    $("#inputTanggalLahir").flatpickr({
        dateFormat: "d-m-Y",
    });

    $('#merk_motor').on('change', function () {
        if ($(this).val() == 'Lainya') {
            $('#merk_motor_lainya').attr('hidden', false);
            $('#merk_motor_lainya').attr('required', true);
            $('#merk_motor').parent().removeClass('col-lg-12').addClass('col-lg-6');

            $('#tipe_motor_lainya').attr('hidden', true);
            $('#tipe_motor_lainya').attr('required', false);
            $('#tipe_motor').html(`
                <option></option>
                <option value="Lainya">Lainya</option>
            `);
            $('#tipe_motor').parent().removeClass('col-lg-6').addClass('col-lg-12');
            return false;
        }

        $('#merk_motor').parent().removeClass('col-lg-6').addClass('col-lg-12');
        $('#merk_motor_lainya').attr('hidden', true);
        $('#merk_motor_lainya').attr('required', false);
        typemotor();
    });

    $('#tipe_motor').on('change', function () {
        if ($(this).val() == 'Lainya') {
            $('#tipe_motor_lainya').attr('hidden', false);
            $('#tipe_motor_lainya').attr('required', true);
            $('#tipe_motor').parent().removeClass('col-lg-12').addClass('col-lg-6');
            return false;
        }
        $('#tipe_motor').parent().removeClass('col-lg-6').addClass('col-lg-12');
        $('#tipe_motor_lainya').attr('hidden', true);
        $('#tipe_motor_lainya').attr('required', false);
    });

    $('#inputNomorFaktur').on('change',function () {
        if($(this).val() != ''){
            cekfaktur();
        }
    });

    $('#inputNIK').on('change', function () {
        if ($('#inputNIK').val() != '') {
            ceknik({option : 'first'});
        }
    });

    $('#btn_nik').on('click', function () {
        ceknik({option:'page'});
    });

    $('#autocomplateKonsumen').on('click', '.pilih', function () {
        let data = JSON.parse(atob($(this).data('a')));
        $('#messageError').html('');
        $('#inputNIK').val(data.nik);
        $('#inputNamaPelanggan').val(data.nama);
        $('#inputTempatLahir').val(data.tempat_lahir);
        $("#inputTanggalLahir").flatpickr({
            dateFormat: "d-m-Y",
            defaultDate: moment(data.tgl_lahir).format('DD-MM-YYYY'),
        });
        $('#inputTelepon').val(data.telepon);
        $('#inputAlamat').val(data.alamat);
        $('#inputEmail').val(data.email);
        $('#inputNopol').val(data.nopol);
    });

    $('#selectMengetahui').on('change', function () {
        if ($(this).val() == 'LAIN-LAIN' || $(this).val() == 'SOSIAL MEDIA') {
            $('#keterangan_mengetahui').attr('hidden', false);
            $('#keterangan_mengetahui').attr('required', true);
            $('#selectMengetahui').parent().removeClass('col-lg-12').addClass('col-lg-6');
            return false;
        }
        $('#selectMengetahui').parent().removeClass('col-lg-6').addClass('col-lg-12');
        $('#keterangan_mengetahui').attr('hidden', true);
        $('#keterangan_mengetahui').attr('required', false);
    });

    // btn_cari on click
    $('#autocomplateKonsumen').on('click','#btn_cari', function () {
        ceknik({option:'page'});
    });
    // pagination .page-item yang tidak ada class active atau disabled
    $('#autocomplateKonsumen').on('click','.pagination .page-item:not(.active):not(.disabled) .page-link', function () {
        ceknik({option:'page', page:$(this).attr('href').split('=')[1]});
    });
    $('#autocomplateKonsumen').on('click','#per_page', function () {
        ceknik({option:'page', per_page:$(this).val()});
    });


    $('#btnSimpan').on('click', function () {
        if(status.faktur == true) {
            $('#inputNomorFaktur').removeClass('is-invalid').addClass('is-valid');
            $('#inputNomorFaktur').next('invalid-feedback').remove();
        }

        if($('#inputNomorFaktur').val() == ''){
            $('#inputNomorFaktur').removeClass('is-valid').addClass('is-invalid').focus();
            $('#inputNomorFaktur').siblings('.invalid-feedback').length === 0 ? $('#inputNomorFaktur').after('<div class="invalid-feedback">Nomor Faktur tidak boleh kosong</div>') : $('#inputNomorFaktur').siblings('.invalid-feedback').text('Nomor Faktur tidak boleh kosong');
        }

        validasi('#inputNamaPelanggan', 'Nama Pelanggan tidak boleh kosong')
        validasi(['#inputTempatLahir', '#inputTanggalLahir'], 'Tempat dan Tanggal Lahir tidak boleh kosong');
        validasi('#inputAlamat', 'Alamat tidak boleh kosong');
        validasi('#inputTelepon', 'Telepon tidak boleh kosong');
        validasi('#inputNopol', 'Nomor Polisi tidak boleh kosong');

        if(($('#merk_motor').val() == 'Lainya' || $('#merk_motor').val() == '') && $('#merk_motor_lainya').val() == ''){
            $('#merk_motor').addClass('is-invalid');
            $('#merk_motor_lainya').addClass('is-invalid');
            $('#merk_motor_lainya').siblings('.invalid-feedback').length === 0 ? $('#merk_motor_lainya').after('<div class="invalid-feedback">Merk Motor tidak boleh kosong</div>') : $('#merk_motor_lainya').siblings('.invalid-feedback').text('Merk Motor tidak boleh kosong');
        } else {
            $('#merk_motor').removeClass('is-invalid');
            $('#merk_motor_lainya').removeClass('is-invalid');
            $('#merk_motor_lainya').next('invalid-feedback').remove();
        }

        if(($('#tipe_motor').val() == 'Lainya' || $('#tipe_motor').val() == '') && $('#tipe_motor_lainya').val() == ''){
            $('#tipe_motor').addClass('is-invalid');
            $('#tipe_motor_lainya').addClass('is-invalid');
            $('#tipe_motor_lainya').siblings('.invalid-feedback').length === 0 ? $('#tipe_motor_lainya').after('<div class="invalid-feedback">Tipe Motor tidak boleh kosong</div>') : $('#tipe_motor_lainya').siblings('.invalid-feedback').text('Tipe Motor tidak boleh kosong');
        } else {
            $('#tipe_motor').removeClass('is-invalid');
            $('#tipe_motor_lainya').removeClass('is-invalid');
            $('#tipe_motor_lainya').next('invalid-feedback').remove();
        }

        if(($('#selectMengetahui').val() == 'LAIN-LAIN' || $('#selectMengetahui').val() == 'SOSIAL MEDIA') && $('#keterangan_mengetahui').val() == ''){
            $('#keterangan_mengetahui').addClass('is-invalid').focus();
            $('#keterangan_mengetahui').siblings('.invalid-feedback').length === 0 ? $('#keterangan_mengetahui').after('<div class="invalid-feedback">Keterangan Mengetahui tidak boleh kosong</div>') : $('#keterangan_mengetahui').siblings('.invalid-feedback').text('Keterangan Mengetahui tidak boleh kosong');
        } else {
            $('#keterangan_mengetahui').removeClass('is-invalid');
            $('#keterangan_mengetahui').next('invalid-feedback').remove();
        }

        if ($('#inputNomorFaktur').val() != '' && $('#inputNamaPelanggan').val() != '' && $('#inputTempatLahir').val() != '' && $('#inputTanggalLahir').val() != '' && $('#inputAlamat').val() != '' && $('#inputTelepon').val() != '' && $('#inputNopol').val() != '' && $('#merk_motor').val() != '' && $('#tipe_motor').val() != '' && $('#selectMengetahui').val() != '') {
            if (status.faktur == false) {
                $('#inputNomorFaktur').focus();
                return false;
            }
            simpan();
        }
    });

    if (old.merk != '') {
        $('#merk_motor').val(old.merk).trigger('change');
    }

    if($('#inputNomorFaktur').val() != ''){
        cekfaktur();
    }
});
