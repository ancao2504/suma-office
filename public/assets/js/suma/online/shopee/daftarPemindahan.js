const params = new URLSearchParams(window.location.search)
for (const param of params) {
    var page = params.get('page');
    var per_page = params.get('per_page');
    var start_date = params.get('start_date')??moment(new Date(), 'DD-MM-YYYY').format('YYYY-MM-DD');
    var end_date = params.get('end_date')??moment(new Date(), 'DD-MM-YYYY').format('YYYY-MM-DD');
    var search = params.get('search');
}

// terdapat ajax start maka loadaing.block()
$(document).ajaxStart(function() {
    loading.block();
});
// terdapat ajax stop maka loadaing.release()
$(document).ajaxStop(function() {
    loading.release();
});

$('body').attr('data-kt-aside-minimize', 'on');
$('#kt_aside_toggle').addClass('active');


function updateStok(){
    // ajax post update
}

function gantiUrl(url_search, url_start_date, url_end_date, url_page = 1, url_per_page = 10){
    loading.block();
    window.location.href = window.location.href.split('?')[0] + '?page=' + url_page + (url_per_page?'&per_page=' + url_per_page:'') + (url_search?'&search=' + url_search:'') + (url_start_date?'&start_date=' + url_start_date:'') + (url_end_date?'&end_date=' + url_end_date:'');
}

function getDetail(key){
    // buat ajax post url: base_url + online/pemindahan/shopee/detail dengan data: no_dokumen
    $.ajax({
        type: "POST",
        url: base_url + "/online/pemindahan/shopee/detail",
        data: { 
            _token: $('meta[name="csrf-token"]').attr('content'),
            nomor_dokumen: key 
        },
        success: function(response) {
            // kode yang akan dijalankan jika request berhasil
            $('#update_stok .modal-boady').html(response.data);
            $('#update_stok').modal('show');
        },
        error: function(xhr, status, error) {
            // kode yang akan dijalankan jika request gagal
            console.log(xhr.responseText);
        }
    });
}
$( function() {
    let tboady = $("#kt_project_users_table > tbody");
    $("#get_tgl_dokumen").flatpickr({
        dateFormat: "Y-m-d",
        mode: "range",
        defaultDate: [start_date??moment(new Date()).format('YYYY-MM-DD'), (end_date??start_date)??moment(new Date()).format('YYYY-MM-DD')],
    });

    $("#card-detail, #update_stok .modal-dialog").draggable();

    tboady.on('mouseenter', 'tr.klikdokumen', function() {
        $(this).css('cursor', 'pointer');
    });

    // list klik kecuali
    tboady.on("click", "tr.klikdokumen", function(event) {
        if (!$(event.target).is("button.btn-edit, span.bi.bi-pencil")){
            $('i.bi-caret-right-fill').remove();
            $('.klikdokumen').removeClass('table-active');

            $(this).addClass('table-active');
            if ($(this).find('td').eq(0).find('i.bi-caret-right-fill').length == 0) {
                $(this).find('td').eq(0).html(`<i class="bi bi-caret-right-fill"></i>${$(this).find('td').eq(0).html()}`);
            }

            if ($('#card-detail').attr('hidden') != 'hidden') {
                $(this).trigger('dblclick');
            }
        }
    });

    tboady.on("click", "button.btn-edit", function(event) {
        getDetail($(this).attr('data-key'));
    });

    $('#update_stok').on('click','button.btn-update',function(event) {
        swal.fire({
            title: "Apakah anda yakin Update Stock Shopee ?",
            text: "Data akan diupdate!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, update!",
            cancelButtonText: "Tidak!",
            reverseButtons: true,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-secondary"
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                updateStok();
            } else if (result.dismiss === "cancel") {
            }
        });
    });

    $('button.btn-update').on('click',function(event) {
        swal.fire({
            title: "Apakah anda yakin Update Stock Shopee ?",
            text: "Data akan diupdate!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, update!",
            cancelButtonText: "Tidak!",
            reverseButtons: true,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-secondary"
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                updateStok();
            } else if (result.dismiss === "cancel") {
            }
        });
    });

    // per_page
    $('#per_page').on('change', function () {
        gantiUrl(search, start_date, end_date, 1, $(this).val());
    });
    $('#per_page option[value="' + per_page + '"]').prop('selected', true);

    // get_tgl_dokumen on change
    let cange = 0;
    $('#get_tgl_dokumen').on('change', function(){
        cange++;
        var tgl_dokumen = $(this).val().split(' to ');
        if(cange == 2){
            gantiUrl(search, tgl_dokumen[0], tgl_dokumen[1], 1, per_page);
            cange = 0;
        }
    });

    $('#filterSearch').val(search);
    // filterSearch on change
    $('#filterSearch').on('change', function(){
        var filter_search = $(this).val().replace(/\//g, '%2F');
        gantiUrl(filter_search, start_date, end_date, 1, per_page);
    });

    // kt_project_users_table_wrapper on click pagination .page-item kecuali ada atribut disabeld
    $('#kt_project_users_table_wrapper').on('click', '.pagination .page-item:not(.disabled)', function(event) {
        gantiUrl(search, start_date, end_date, $(this).find('.page-link').attr('data-page'), per_page);
    });
});