let fileList = new DataTransfer();
let urut = 0;
let page = 2;
let scroll = true;

function dropHandler(ev) {
    ev.preventDefault();
    $('.dropzone').removeClass('p-10');
    uploadFile(ev.dataTransfer.files);
}

function dragOverHandler(ev) {
    ev.preventDefault();
    // drop zone effect
    $('.dropzone').addClass('p-10');
}
function uploadFile(files) {
    // filter file yang di upload kurang dari 10
    if(fileList.files.length + files.length <= 20){
        for (let i = 0; i < files.length; i++) {
            // Filter file hanya jpg, jpeg, png
            if (files[i].type.match('image/jpeg') || files[i].type.match('image/jpg')) {
                // byte to kb dibulatkan 2 angka dibelakang koma
                if(Math.round(files[i].size / 1024) <= 2048){
                    $('.dropzone').addClass('dz-started');
                    $('.dz-message').after(`
                        <div class="dz-preview dz-processing dz-error dz-complete dz-image-preview">
                            <div class="dz-image">
                                <img data-dz-thumbnail="" alt="${files[i].name}" src="${URL.createObjectURL(files[i])}">
                            </div>
                            <div class="dz-details">
                                <div class="dz-size">
                                    <span data-dz-size=""><strong>${Math.round(files[i].size / 1024)}</strong> KB</span>
                                </div>
                                <div class="dz-filename"><span data-dz-name="">${files[i].name}</span></div>
                            </div>
                            <a class="dz-remove urut-${urut}" href="javascript:undefined;" data-dz-remove="">Remove file</a>
                        </div>
                    `);
                    let file = new File([files[i]], files[i].name, {type: files[i].type});
                    fileList.items.add(file);
                    fileList.files[urut].urut = urut;
                    removeFile(urut);
                    urut++;
                } else {
                    toastr.error('File : '+files[i].name+' tidapat di upload karena ukuran file lebih dari 2 MB', 'Info');
                }
            } 
            else {
                toastr.error('File : '+files[i].name+' tidapat di upload karena format file tidak sesuai', 'Info');
            }
        }
    } else {
            toastr.error('Maksimal 20 file', 'Info');
    }
    
}

function removeFile(e){
    $('.urut-'+e).on('click', function(){
        for (let i = 0; i < fileList.files.length; i++) {
            if(fileList.files[i].urut == e){
                fileList.items.remove(i);
            }
        }
        $(this).parent().remove();
        if ($('.dz-preview').length == 0) {
            $('.dropzone').removeClass('dz-started');
            urut = 0;
        }
    });
}

function pilihGambar(url){
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    if (http.status != 404) {
        window.open(url, '_blank');
    } else {
        toastr.error('Gambar tidak ditemukan', 'Info');
    }
}

function listGambar(){
    $.ajax({
        url: window.location.href,
        type: "GET",
        data: {
            search: $('#inputCariPartNumber').val(),
            page: page
        },
        dataType: "json",
        success: function(data){
            if (data != null && data.status == 1) {
                $('#list-gambar').append(data.data);
                $('#spin-page').remove();
                page++;
            } else if (data != null && data.status == 0) {
                $('#list-gambar').append(`
                    <div class="col-12 text-center">
                        <span class="text-danger">${data.message}</span>
                    </div>
                `);
                scroll = false;
                $('#spin-page').remove();
            } else if (data != null && data.status == 2) {
                scroll = false;
                $('#spin-page').remove();
            }
        },
        error: function(data){
            scroll = false;
        }
    });
}

$(document).ready(function(){
    $('form').submit(function () {
        loading.block();
    });

    $(document).ajaxStart(function () {
        loading.block();
    });

    $(document).ajaxStop(function () {
        loading.release();
    });

    $('#inputCariPartNumber').on('change', function(){
        $('#list-gambar').html('');
        page = 1;
        scroll = true;
        listGambar();
    });
    $('#cariImagePart').on('click', function(){
        $('#list-gambar').html('');
        page = 1;
        scroll = true;
        listGambar();
    });

    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height()) == $(document).height() && (scroll == true || $('#inputCariPartNumber').val() == '')) {
            if ($('#spin-page').length == 0) {
                $('#list-gambar').append(`
                    <div class="col-lg-12 col-md-12 col-sm-12 py-3" id="spin-page">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                `);
                listGambar(page);
            }
        }
    });

    $('#btnSubmit').on('click', function(){
        if ($('.dz-preview').length != 0) {
            swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang sudah di upload tidak dapat dihapus, tetapi dapat di timpa dengan data yang baru!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Upload!',
                cancelButtonText: 'Tidak, Batalkan!',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger ml-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('input[name="file[]"]').prop('files', fileList.files);

                    if ($('input[name="file[]"]').prop('files').length == fileList.files.length) {
                        $('#drop_zone').append(`
                            <input type="hidden" name="search" value="${$('#inputCariPartNumber').val()}">
                        `);
                        $('#drop_zone').submit();
                    }
                }
            });
        } else {
            toastr.error('Tidak ada gambar yang di upload', 'Info');
        }
    });

    
    $('.dropzone').on('dragleave', function(){
        // $('.dropzone').removeClass('p-10');
    });

    $('.dropzone').on('click', function(e) {
        if (!$(e.target).is('.dz-remove, .dz-image, .dz-details, .dz-details.dz-filename, .dz-details.dz-size, .dz-details.dz-filename span, .dz-details.dz-size span')) {
            $('#file').trigger('click');
        }
    });
    $('#file').on('change', function(){
        uploadFile(this.files);
    });
});