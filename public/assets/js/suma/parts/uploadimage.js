let fileList = new DataTransfer();
let urut = 0;
let page = 1;

function dropHandler(ev) {
    ev.preventDefault();
    console.log('File(s) dropped');
    $('.dropzone').removeClass('p-10');
    uploadFile(ev.dataTransfer.files);
    // console.log('trigger drop');
}

function dragOverHandler(ev) {
    ev.preventDefault();
    // console.log('File(s) in drop zone');
    // drop zone effect
    $('.dropzone').addClass('p-10');
}
function uploadFile(files) {
    // filter file yang di upload kurang dari 10
    if(fileList.files.length + files.length <= 20){
        for (let i = 0; i < files.length; i++) {
            // Filter file hanya jpg, jpeg, png
            if (files[i].type.match('image/jpeg') || files[i].type.match('image/png') || files[i].type.match('image/jpg')) {
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
                    console.log(fileList.files);
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
        // looping fileList.files cari urut yang sama dengan e
        for (let i = 0; i < fileList.files.length; i++) {
            if(fileList.files[i].urut == e){
                fileList.items.remove(i);
            }
        }
        console.log(fileList.files);
        $(this).parent().remove();
        if ($('.dz-preview').length == 0) {
            $('.dropzone').removeClass('dz-started');
            urut = 0;
        }
    });
}

function pilihGambar(url){

    // aksess url terlebihdahulu jika tidak ditemukan maka jangan tampilkan
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    if (http.status != 404) {
        window.open(url, '_blank');
    } else {
        toastr.error('Gambar tidak ditemukan', 'Info');
    }
}


function listGambar(page){
    $.ajax({
        url: base_url + "/parts/uploadimage/part",
        type: "POST",
        data: {
            _token: $('input[name="_token"]').val(),
            page: page
        },
        dataType: "json",
        success: function(data){
            // console.log(data.data);
            if (data.length != 0 || data != null) {
                // looping data.data
                for (let i = 0; i < data.data.length; i++) {
                    var img = new Image();
                    // untuk cek image apakah ada atau tidak, jika tidak ada akan di hendle oleh eventlistener error dan load
                    img.src = data.data[i].url.trim();
                    img.addEventListener('load', function() {
                        $('#list-gambar').append(`
                            <div class="col-lg-2 col-md-3 col-sm-4 col-lg-3 py-3" onclick="pilihGambar('${data.data[i].url.trim()}')" style="cursor: pointer;">
                                <div class="card border border-dark rounded">
                                    <div class="d-flex justify-content-center">
                                        <div class="bg-image rounded" style="background-image: url('${data.data[i].url.trim()}'); width: 100%; height: 200px; background-size: cover; background-position: center; background-repeat: no-repeat;">
                                            <div class="bg-dark" style="width: 100%; height: 50px; position: absolute; bottom: 0; opacity: 0.8;">
                                                <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                    <span class="text-white">${data.data[i].kd_part}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                    img.addEventListener('error', function(){
                        $('#list-gambar').append(`
                            <div class="col-lg-2 col-md-3 col-sm-4 col-lg-3 py-3">
                                <div class="card border border-dark rounded">
                                    <div class="d-flex justify-content-center">
                                        <div class="bg-image rounded" style="background-image: url('http://localhost:2022/suma-pmo/public/assets/images/default.png'); width: 100%; height: 200px; background-size: cover; background-position: center; background-repeat: no-repeat;">
                                            <div class="bg-dark" style="width: 100%; height: 50px; position: absolute; bottom: 0; opacity: 0.8;">
                                                <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                    <span class="text-white">${data.data[i].kd_part}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
                $('#spin-page').remove();
            }
        }
    });
}

$(document).ready(function(){
    $('form').submit(function () {
        loading.block();
    });

    // button submit klik maka form akan di submit
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
                    // jika prop diatas sudah selesai diproses maka form akan di submit
                    if ($('input[name="file[]"]').prop('files').length == fileList.files.length) {
                        $('#drop_zone').submit();
                    }
                }
            });
        } else {
            toastr.error('Tidak ada gambar yang di upload', 'Info');
        }
    });

    
    // jika dropzone tidak terjadi maka akan dijalankan
    $('.dropzone').on('dragleave', function(){
        // $('.dropzone').removeClass('p-10');
    });

    $('.dropzone').on('click', function(e) {
        // kecuali ynag aca class .dz-message, .dz-preview, .dz-remove
        if (!$(e.target).is('.dz-remove, .dz-image, .dz-details, .dz-details.dz-filename, .dz-details.dz-size, .dz-details.dz-filename span, .dz-details.dz-size span')) {
            $('#file').trigger('click');
        }
    });
    $('#file').on('change', function(){
        uploadFile(this.files);
    });
    
    // let imgindex = 0;
    $(window).scroll(function(){
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            // jika #spin-page tidak ada maka akan di append
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
                setTimeout(function(){
                    listGambar(page);
                }, 1000);
                page++;
            }
        }
    });

    
    listGambar(page);
});