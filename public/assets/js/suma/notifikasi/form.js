
var quill = new Quill('#editor', {
    theme: 'snow'
});

function kirim(){
    loading.block();
    $.post(base_url + "/notifikasi/send",
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                type_notif: $('#type_notifikasi').val(),
                no_pof: $('#no_pof').val(),
                no_camp: $('#campaign').val(),
                to: {
                    user_id : [].concat($('#Sales').val(), $('#Dealer').val(), $('#User').val()),
                    role_id : $('#Role').val(),
                },
                subject: $('#subject').val(),
                message: quill.root.innerHTML
            },
            function (response) {
                if (response.status == '1') {
                    swal.fire({
                        title: 'Berhasil!',
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
                }
                if (response.status == '0') {
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
                        }
                    });
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
                text: 'Maaf Terjadi Kesalahan!',
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
        loading.release();
}

function getPof(){
    $.get(base_url+'/pof',{
        no_pof: $('#no_pof').val(),
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if (jQuery.isEmptyObject(dataJson)) {
                $('#no_pof').addClass('is-invalid');
                $('#no_pof').removeClass('is-valid');
                $('#error_no_pof').html('No POF tidak ditemukan');
            } else {
                $('#no_pof').val(dataJson.kode);
                $('#no_pof').removeClass('is-invalid');
                $('#no_pof').addClass('is-valid');
                $('#error_no_pof').html('');
            }
        }
        if (response.status == '0') {
            $('#no_pof').addClass('is-invalid');
            $('#no_pof').removeClass('is-valid');
            $('#error_no_pof').html(response.message);
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
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: 'Maaf, Terjadi Kesalahan, Silahkan coba lagi!',
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
}

function getCampaign(){
    $.get(base_url+'/campaign',{
        no_camp : $('#campaign').val(),
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if (jQuery.isEmptyObject(dataJson)) {
                $('#campaign').addClass('is-invalid');
                $('#campaign').removeClass('is-valid');
                $('#error_campaign').html('No Campaign tidak ditemukan');
            } else {
                $('#campaign').val(dataJson.kode);
                $('#campaign').removeClass('is-invalid');
                $('#campaign').addClass('is-valid');
                $('#error_campaign').html('');
            }
        }
        if (response.status == '0') {
            $('#campaign').addClass('is-invalid');
            $('#campaign').removeClass('is-valid');
            $('#error_campaign').html(response.message);
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
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: 'Maaf, Terjadi Kesalahan, Silahkan coba lagi!',
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
}


$(document).ready(function() {
    $('#multipleSelect').change(function () {
        var selectedValues = $(this).val();

        var isSalesSelected = $.inArray('sales', selectedValues) !== -1;
        var isDealerSelected = $.inArray('Dealer', selectedValues) !== -1;
        var isRoleSelected = $.inArray('Role id', selectedValues) !== -1;
        var isUserSelected = $.inArray('User id', selectedValues) !== -1;

        $('#multipleSelectSales').prop('hidden', !isSalesSelected);
        $('#multipleSelectDealer').prop('hidden', !isDealerSelected);
        $('#multipleSelectRole').prop('hidden', !isRoleSelected);
        $('#multipleSelectUser').prop('hidden', !isUserSelected);
    });

    $('#type_notifikasi').change(function () {
        var selectedValues = $(this).val();
        console.log(selectedValues);

        var isPofSelected = (selectedValues == 'POF') ? true : false;
        var isCampaignSelected = (selectedValues == 'CAMPAIGN') ? true : false;

        $('#select_no_pof').prop('hidden', !isPofSelected);
        $('#select_campaign').prop('hidden', !isCampaignSelected);
    });

    $('select').change(function () {
        var sales_option = $('#Sales').val();
        var dealer_option = $('#Dealer').val();
        var role_option = $('#Role').val();
        var user_option = $('#User').val();

        if($.inArray('all', sales_option) !== -1){
            $('#Sales option').prop('selected', false);
            $('#Sales option[value="all"]').prop('selected', true);
        }
        if($.inArray('all', dealer_option) !== -1){
            $('#Dealer option').prop('selected', false);
            $('#Dealer option[value="all"]').prop('selected', true);
        }
        if($.inArray('all', role_option) !== -1){
            $('#Sales option').prop('selected', false);
            $('#Dealer option').prop('selected', false);
            $('#Role option').prop('selected', false);
            $('#User option').prop('selected', false);
            $('#Role option[value="all"]').prop('selected', true);
        }
        if($.inArray('all', user_option) !== -1){
            $('#Sales option').prop('selected', false);
            $('#Dealer option').prop('selected', false);
            $('#Role option').prop('selected', false);
            $('#User option').prop('selected', false);
            $('#User option[value="all"]').prop('selected', true);
        }
    });

    $('#no_pof').change(function () {
        if ($('#type_notifikasi').val() != 'POF') {
            $('#error_no_pof').html('Type notifikasi harus POF');
            $('#no_pof').addClass('is-invalid');
        }
        if ($(this).val() == '') {
            $('#error_no_pof').html('No POF harus diisi');
            $('#no_pof').addClass('is-invalid');
        }

        getPof();
    });

    $('#campaign').change(function () {
        if ($('#type_notifikasi').val() != 'CAMPAIGN') {
            $('#error_campaign').html('Type notifikasi harus CAMPAIGN');
            $('#campaign').addClass('is-invalid');
        }
        if ($(this).val() == '') {
            $('#error_campaign').html('Campaign harus diisi');
            $('#campaign').addClass('is-invalid');
        }

        getCampaign();
    });

    $('#kirim_notif').click(function () {
        if ($('#type_notifikasi').val() == 'POF') {
            if ($('#no_pof').val() == '') {
                $('#error_no_pof').html('No POF harus diisi');
                $('#no_pof').addClass('is-invalid');
            }
            if ($('#no_pof').val() != '' && $('#no_pof').hasClass('is-invalid') == true) {
                getPof();
                $('#no_pof').focus();
                return false;
            }
        } else if ($('#type_notifikasi').val() == 'CAMPAIGN') {
            if ($('#campaign').val() == '') {
                $('#error_campaign').html('Campaign harus diisi');
                $('#campaign').addClass('is-invalid');
            }
            if ($('#campaign').val() != '' && $('#campaign').hasClass('is-invalid') == true) {
                getCampaign();
                $('#campaign').focus();
                return false;
            }
        }

        kirim();
    });
});
