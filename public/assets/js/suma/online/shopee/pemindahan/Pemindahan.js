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

function getDaftar(page = $('#view_daftar_paginat').find('.pagination').data('current_page')){
    $.ajax({
        type: "POST",
        url: base_url + "/online/pemindahan/shopee/daftar",
        data: { 
            _token: $('meta[name="csrf-token"]').attr('content'),
            search: $('#filterSearch').val(),
            start_date: $("#get_start_date").val()??moment(new Date()).format('YYYY-MM-DD'),
            end_date: $("#get_end_date").val()??moment(new Date()).format('YYYY-MM-DD'),
            page: page??1,
            per_page: $('#per_page option:selected').val(),
        },
        success: function(response) {
            console.log('sukses');
            console.log(response);
            $('#daftar_table #tabel').remove();
            $('#daftar_table').prepend(response.table);
            $('#view_daftar_paginat').html(response.pagination);
            
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
        defaultDate: start_date??moment(new Date()).format('YYYY-MM-DD'),
    });
    $('#get_end_date').flatpickr({
        defaultDate: end_date??moment(new Date()).format('YYYY-MM-DD'),
    });
    
    $('#text_start_date').text(start_date??moment(new Date()).format('DD MMMM YYYY'));
    $('#text_end_date').text(end_date??moment(new Date()).format('DD MMMM YYYY'));

    $('#per_page option[value="'+per_page+'"]').prop('selected', true);
    getDaftar(page);

    $('#per_page').on('change', function () {
        getDaftar(1);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    $('#get_start_date, #get_end_date').on('change', function(){
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

    $('#view_daftar_paginat').on('click', '.pagination .page-item:not(.disabled)', function(event) {
        getDaftar($(this).find('.page-link').attr('data-page'));
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    // daftar_table find tabel .btn_detail on click swal fire confirm ajax post
    $('#daftar_table').on('click', '#tabel .btn_detail', function(event) {
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

                            if(response.data.data_error){
                                $('body').append(response.modal_respown);
                                $('body').find('#modal_respown').modal('show');
                            }
                            getDaftar(page);
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