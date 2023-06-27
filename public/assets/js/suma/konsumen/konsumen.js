$(document).ready(function () {
    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');

    $('#lokasi').append(`<option value="">Pilih Lokasi</option>`);
    Object.values(lokasi)[0].forEach(item => {
        if (item == url.get('kd_lokasi')) {
            $('#lokasi').append(`<option value="${item}" selected>${item}</option>`);
        } else {
            $('#lokasi').append(`<option value="${item}">${item}</option>`);
        }
    });
    $('#search').val(url.get('search'));

    $('#btn_filter').on('click', function () {
        window.location.href = base_url + '/konsumen?page=1&per_page=10&companyid='+($('#company').val()??(url.get('companyid')??''))+'&kd_lokasi='+($('#lokasi').val()??(url.get('kd_lokasi')??''))+'&search='+($('#search').val()??(url.get('search')??''))+'&by='+(url.get('by')??'');
    });
    
    $('#company').on('change', function () {
        $('#lokasi').empty();
        $('#lokasi').append(`<option value="">Pilih Lokasi</option>`);
        lokasi[$(this).val()].forEach(item => {
            $('#lokasi').append(`<option value="${item}">${item}</option>`);
        });
    });

    $('#per_page').on('change', function () {
        window.location.href = base_url + '/konsumen?page=1&per_page='+$(this).val()+'&companyid='+($('#company').val()??(url.get('companyid')??''))+'&kd_lokasi='+($('#kd_lokasi').val()??url.get('kd_lokasi')??'')+'&search='+($('#search').val()??(url.get('search')??''))+'&by='+(url.get('by')??'');
    });

    $('.select_by').on('click', function () {
        window.location.href = base_url + '/konsumen?page=1&per_page='+($('#per_page').val()??(url.get('per_page')??''))+'&companyid='+($('#company').val()??(url.get('companyid')??''))+'&kd_lokasi='+($('#kd_lokasi').val()??url.get('kd_lokasi')??'')+'&search='+($('#search').val()??(url.get('search')??''))+'&by='+($(this).data('a')??'');
    });

    $('#search').on('change', function () {
        if ($(this).val() == '' && url.get('search') != '') {
            window.location.href = base_url + '/konsumen?page=1&per_page='+($('#per_page').val()??(url.get('per_page')??''))+'&companyid='+($('#company').val()??(url.get('companyid')??''))+'&kd_lokasi='+($('#kd_lokasi').val()??url.get('kd_lokasi')??'')+'&search='+($(this).val())+'&by='+(url.get('by')??'');
        }
    });

    // btnDelete
    $('.btnDelete').on('click', function () {
        let data_delet = JSON.parse(atob($(this).data('id')));
        swal.fire({
            title: "Apakah anda yakin?",
            html : "data konsumen dengan nomor faktur : <b>" + data_delet.no_faktur + "</b> akan dihapus.",
            icon: "warning",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Tidak, Batalkan!",
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light-secondary'
            }
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: base_url + '/konsumen/delete',
                    type: 'post',
                    data: {
                        id: data_delet.id,
                        no_faktur: data_delet.no_faktur,
                        companyid: data_delet.company,
                        kd_lokasi: data_delet.lokasi,
                        divisi: data_delet.divisi,
                        _token: $('meta[name=csrf-token]').attr('content')
                    },
                    beforeSend: function () {
                        loading.block();
                    },
                    success: function (data) {
                        if (data.status == '0') {
                            swal.fire({
                                title: "Gagal!",
                                html : data.message,
                                icon: "error",
                                showCancelButton: false,
                                allowOutsideClick: false,
                                reverseButtons: true,
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: 'btn btn-secondary',
                                }
                            });
                            return false;
                        } else if (data.status == '1') {
                            swal.fire({
                                title: "Terhapus!",
                                html : data.message,
                                icon: "success",
                                showCancelButton: false,
                                allowOutsideClick: false,
                                reverseButtons: true,
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: 'btn btn-success',
                                }
                            }).then(function (result) {
                                if (result.value) {
                                    location.reload();
                                }
                            });
                        } else if (data.status == '2') {
                            swal.fire({
                                title: "Gagal!",
                                html : data.message,
                                icon: "error",
                                showCancelButton: false,
                                allowOutsideClick: false,
                                reverseButtons: true,
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: 'btn btn-secondary',
                                }
                            }).then(function (result) {
                                if (result.value) {
                                    location.reload();
                                }
                            });
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
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
                    },
                    complete: function () {
                        loading.release();
                    }
                });
            }
        });
    });
});