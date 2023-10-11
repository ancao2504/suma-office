$(document).ready(function () {
    $('body').on('click', '#btnDetailEkspedisi', function (e) {
        e.preventDefault();
        $('#inputKeterangan').val($(this).data("keterangan"));
        $('#inputTiktokID').val($(this).data("tiktok_id"));
        $('#inputIDInternal').val($(this).data("id"));
        $('#selectKodeEkspedisi').val($(this).data("kode")).change();

        $('#modalDetailEkspedisi').modal('show');
    });

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $('#btnSimpanEkspedisi').on('click', function (e) {
        e.preventDefault();

        var tiktokId = $('#inputTiktokID').val();
        var kode = $('#selectKodeEkspedisi').find(":selected").val();
        var nama = $('#inputKeterangan').val();
        var idInternal = $('#inputIDInternal').val();
        var _token = $('input[name="_token"]').val();

        if(kode == '' || nama == '' || tiktokId == '') {
            Swal.fire({
                text: 'Isi data secara lengkap',
                icon: 'warning',
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Ok, got it!',
                customClass: {
                    confirmButton: 'btn btn-warning'
                }
            });
        } else {
            loading.block();
            $.ajax({
                url: url.simpan_ekspedisi,
                method: "post",
                data: {
                    id: idInternal, tiktok_id: tiktokId, kode: kode, nama: nama,
                    _token: _token
                },
                success: function(response) {
                    loading.release();

                    if (response.status == true) {
                        Swal.fire({
                            text: response.message,
                            icon: 'success',
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: 'warning',
                            buttonsStyling: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                },
                error: function() {
                    loading.release();
                    Swal.fire({
                        text: 'Server tidak merespon, coba lagi',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            });
        }
    });
});
