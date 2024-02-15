$(function () {
    var urlParams = new URLSearchParams(window.location.search);
    var filterParam = urlParams.get('f');
    if (filterParam) {
        var decodedFilter = JSON.parse(atob(filterParam));

        $('#filter_jenis_jawab').val(decodedFilter.jenis_jawab).trigger('change');
        $('#filter_column_order').val(decodedFilter.column).trigger('change');
        $('#filter_order').val(decodedFilter.order).trigger('change');
        $('#cari').val(decodedFilter.search);
    }

    $('#per_page').on('change', function () {
        let filter = {
            search: $('#cari').val(),
            column: $('#filter_column_order').val(),
            order: $('#filter_order').val(),
            jenis_jawab: $('#filter_jenis_jawab').val(),
        }
        // window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$(this).val()+'&no_retur=' + $('#cari').val();
        window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$(this).val()+'&f='+btoa(JSON.stringify(filter));
        loading.block()
    });

    $('#cari').on('change', function () {
        let filter = {
            search: $('#cari').val(),
            column: $('#filter_column_order').val(),
            order: $('#filter_order').val(),
            jenis_jawab: $('#filter_jenis_jawab').val(),
        }
        // window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$('#per_page').val()+'&no_retur=' + $('#cari').val();
        window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$('#per_page').val()+'&f='+btoa(JSON.stringify(filter));
        loading.block()
    });

    $('#btn_filter').on('click', function () {
        let filter = {
            search: $('#cari').val(),
            column: $('#filter_column_order').val(),
            order: $('#filter_order').val(),
            jenis_jawab: $('#filter_jenis_jawab').val(),
        }


        // error_filter_column_order dan error_filter_order
        // console.log($('#filter_column_order').val(), $('#filter_order').val())
        // if($('#filter_column_order').val() == '' || $('#filter_order').val() == ''){
        //     $('#filter_column_order').addClass('is-invalid');
        //     $('#filter_order').addClass('is-invalid');
        //     $('#error_filter_column_order').removeClass('d-none');
        //     $('#error_filter_order').removeClass('d-none');
        //     return;
        // }else{
        //     $('#filter_column_order').removeClass('is-invalid');
        //     $('#filter_order').removeClass('is-invalid');
        //     $('#error_filter_column_order').addClass('d-none');
        //     $('#error_filter_order').addClass('d-none');
        // }
        let validasi = false;
        if(filter.column.length == 0){
            $('#filter_column_order').addClass('is-invalid');
            $('#error_filter_column_order').text('Pilih Kolom terlebih dahulu');
            validasi = true;
        }else{
            $('#filter_column_order').removeClass('is-invalid');
            $('#error_filter_column_order').text('');

        }

        if (filter.order == '') {
            $('#filter_order').addClass('is-invalid');
            $('#error_filter_order').text('Pilih Urutan terlebih dahulu');
            validasi = true;
        }else{
            $('#filter_order').removeClass('is-invalid');
            $('#error_filter_order').text('');
        }

        if(validasi == true){
            return;
        }

        window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$('#per_page').val()+'&f='+btoa(JSON.stringify(filter));
        loading.block()
    });

    $('#filter_jenis_jawab').on('change', function () {
        let filter = {
            search: $('#cari').val(),
            column: $('#filter_column_order').val(),
            order: $('#filter_order').val(),
            jenis_jawab: $('#filter_jenis_jawab').val(),
        }
        window.location = window.location.origin + window.location.pathname + '?page=1&per_page='+$('#per_page').val()+'&f='+btoa(JSON.stringify(filter));
        loading.block()
    });


    // delete data -----------------------------------------------------------
    $('.btnDelete').on('click', function () {
        Swal.fire({
            html: 'Apakah Anda Yakin Menghapus Retur dengan <b>No Retur : ' + $(this).data('id') +'</b>',
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus!",
            cancelButtonText: "Batal!",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-secondary"
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                loading.block();
                $.post(base_url + '/retur/supplier/delete',
                    {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        no_retur: $(this).data('id')
                    },
                    function (response) {
                        if (response.status == '1') {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK !",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                },
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
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
                        text: 'Terjadi Kesalahan, Silahkan cek data yang dihapus, jika data masih ada coba lagi',
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
        });
    });
})
