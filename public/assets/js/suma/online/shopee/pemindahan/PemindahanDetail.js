function getDaftar(){
    $.ajax({
        type: "GET",
        url: window.location.href,
        data: {},
        success: function(response) {
            $('#view_table .card').html(response.data.view);
        },
        error: function(xhr, status, error) {
        }
    });
}
function updateSemuaDetail(){
    if($('#daftar_table').data('no') != ''){
        Swal.fire({
            text: "Apakah anda yakin Update Stok Semua Part dari Nomor Dokumen : " + $('#daftar_table').data('no') + " ?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Ya, Update Stok!",
            cancelButtonText: "Tidak, batalkan!",
            allowOutsideClick: false,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-secondary"
            }
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: base_url + "/online/pemindahan/shopee/update/stock/dokumen",
                    data: { 
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        no_dok: $('#daftar_table').data('no'),
                    },
                    success: function(response) {
                        if(response.status == 0){
                            Swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                allowOutsideClick: false,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }

                        if(response.status == 1){
                            if(response.data){
                                $('#respon_container').html(response.data.modal_respown);
                                $('#respon_container').find('#modal_respown').modal('show');
                            }
                            // jika modal hide maka baru refresh
                            $('#respon_container').find('#modal_respown').on('hidden.bs.modal', function (e) {
                                getDaftar();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                    }
                });
            }
        }.bind(this));
    }
}
function updateDetail(part){
    part = JSON.parse(atob(part));
    Swal.fire({
        text: "Apakah anda yakin Update Stok Part : " + part.kode_part + " ?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Ya, Update Stok!",
        cancelButtonText: "Tidak, batalkan!",
        allowOutsideClick: false,
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-secondary"
        }
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: base_url + "/online/pemindahan/shopee/update/stock/part",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    nomor_dokumen: part.nomor_dokumen,
                    kode_part: part.kode_part,
                },
                success: function(response) {
                    if(response.status == 0){
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            allowOutsideClick: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }

                    if(response.status == 1){
                        if(response.data){
                            $('#respon_container').html(response.data.modal_respown);
                            $('#respon_container').find('#modal_respown').modal('show');
                        }
                        // jika modal hide maka baru refresh
                        $('#respon_container').find('#modal_respown').on('hidden.bs.modal', function (e) {
                            getDaftar();
                        });
                    }
                },
                error: function(xhr, status, error) {
                }
            });
        } else if (result.dismiss === "cancel") {
        }
    });
}

function updateDetailInternal(data){
    data = JSON.parse(atob(data));
    Swal.fire({
        text: "Apakah anda yakin update internal, jika update internal maka anda harus update secara manual pada shopee ?",
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: "Ya, Update!",
        cancelButtonText: "Tidak, batalkan!",
        allowOutsideClick: false,
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-secondary"
        }
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: base_url + "/online/pemindahan/shopee/update/stock/part/internal",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    nomor_dokumen: data.nomor_dokumen,
                    kode_part: data.kode_part,
                },
                success: function(response) {

                    if(response.status){
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            allowOutsideClick: false,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function(result) {
                            if (result.value) {
                                getDaftar();
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                }
            });
        } else if (result.dismiss === "cancel") {
        }
    });
}

$(document).ready(function(){
    $(document).ajaxStart(function() {
        loading.block();
    });
    
    $(document).ajaxStop(function() {
        loading.release();
    });
    
    $('body').attr('data-kt-aside-minimize', 'on');
    $('#kt_aside_toggle').addClass('active');
});