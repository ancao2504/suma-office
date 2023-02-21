const params = new URLSearchParams(window.location.search)
for (const param of params) {
    let url = JSON.parse(atob(params.get('param')));
    var page = url.page;
    var per_page = url.per_page;
    var start_date = url.start_date;
    var end_date = url.end_date;
    var search = url.search;
}

$(document).ajaxStart(function() {
    loading.block();
});

$(document).ajaxStop(function() {
    loading.release();
});

$('body').attr('data-kt-aside-minimize', 'on');
$('#kt_aside_toggle').addClass('active');

function getDaftar(page = $('.card-body').find('.pagination').data('current_page'), start_date = moment($('#get_start_date').val()).format('YYYY-MM-DD'), end_date = moment($('#get_end_date').val()).format('YYYY-MM-DD')){
    $.ajax({
        type: "GET",
        url: base_url + "/online/pemindahan/shopee",
        data: { 
            search: $('#filterSearch').val(),
            start_date: start_date,
            end_date: end_date,
            page: page,
            per_page: $('#per_page option:selected').val(),
        },
        success: function(response) {
            $('.card-body #tabel').remove();
            $('.card-body').prepend(response.data.view);
            
            // hapus parameter pada url setelah menerapkan filter sebelumnya
            window.history.pushState("", "", window.location.href.split('?')[0]);
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
}

$(document).ready(function() {
    $('#filterSearch').val(search);
    $('#get_start_date').flatpickr({
        defaultDate: start_date??($('#get_start_date').val()??moment(new Date()).format('YYYY-MM-DD')),
    });
    $('#get_end_date').flatpickr({
        defaultDate: end_date??($('#get_end_date').val()??moment(new Date()).format('YYYY-MM-DD')),
    });

    $('.card-body').on('change', '#per_page',function () {
        getDaftar(1);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    $('#btnFilterMasterData').on('click', function(){
        if(moment($('#get_start_date').val()).isBefore($('#get_end_date').val()) || moment($('#get_start_date').val()).isSame($('#get_end_date').val())){
            getDaftar(1);
            $('#text_start_date').text(moment($('#get_start_date').val()).format('DD MMMM YYYY'));
            $('#text_end_date').text(moment($('#get_end_date').val()).format('DD MMMM YYYY'));
        }else{
            Swal.fire({
                text: "Tanggal awal harus lebih kecil dari tanggal akhir!",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }
    });

    $('#filterSearch').on('change', function(){
        getDaftar(1);
    });

    $('.card-body').on('click', '.pagination .page-item:not(.disabled)', function(event) {
        getDaftar($(this).find('.page-link').attr('data-page'));
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    // daftar_table find tabel .btn_detail on click swal fire confirm ajax post
    $('.card-body').on('click', '.btn_detail', function(event) {
        event.preventDefault();
        Swal.fire({
            text: "Apakah anda yakin Update Stok Semua Part dari Nomor Dokumen : " + $(this).parents('tr').attr('data-no') + " ?",
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
                        no_dok: $(this).parents('tr').attr('data-no'),
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
                        console.log(xhr.responseText);
                    }
                });
            }
        }.bind(this));
    });
});