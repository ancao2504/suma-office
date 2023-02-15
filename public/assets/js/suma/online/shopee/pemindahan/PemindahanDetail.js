$(document).ajaxStart(function() {
    loading.block();
});

$(document).ajaxStop(function() {
    loading.release();
});

$('body').attr('data-kt-aside-minimize', 'on');
$('#kt_aside_toggle').addClass('active');

function getDaftar(){
    $.ajax({
        type: "POST",
        url: base_url + "/online/pemindahan/shopee/detail",
        data: { 
            _token: $('meta[name="csrf-token"]').attr('content'),
            nomor_dokumen: $('#daftar_table').data('no'),
        },
        success: function(response) {
            console.log(response);
            $('#daftar_table .table_delete').remove();
            $('#daftar_table').prepend(response.data);
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
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
                        console.log('sukses');
                        console.log(response);
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
                            // $('#detail_modal .modal-body').html(response);
                            // $('#detail_modal').modal('show');

                            if(response.data){
                                $('body').append(response.modal_respown);
                                $('body').find('#modal_respown').modal('show');
                            }
                            getDaftar();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
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
                    console.log(response);

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
                        // if(response.data){
                            $('body').append(response.modal_respown);
                            $('body').find('#modal_respown').modal('show');
                        // }
                    }
                    
                    getDaftar();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        } else if (result.dismiss === "cancel") {
        }
    });
}

function updateDetailInternal(data){
    data = JSON.parse(atob(data));
    console.log(data);
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
                    console.log(response);

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
                    console.log(xhr.responseText);
                }
            });
        } else if (result.dismiss === "cancel") {
        }
    });
}

$( function() {
    getDaftar();
});