const params = new URLSearchParams(window.location.search)
for (const param of params) {
    let url = JSON.parse(atob(params.get('param')));
    var page = url.page;
    var per_page = url.per_page;
    var part_number = url.part_number;
}

function loadDaftarPartNumber() {
    if(part_number != '') {
        $.ajax({
            url: window.location.href,
            method: "get",
            data: { part_number: part_number, page: page, per_page: per_page },
            success: function (response) {
                loading.release();
                if (response.status == true) {
                    $('#tableResultPartNumber').html(response.data);
                } else {
                    Swal.fire({
                        html: response.message,
                        icon: "warning",
                        buttonsStyling: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-warning"
                        }
                    });
                }
            },
            error: function () {
                Swal.fire({
                    text: 'Server Not Responding',
                    icon: "danger",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    }
}

$(document).ready(function () {
    $(document).ajaxStart(function () {
        loading.block();
    });
    $(document).ajaxStop(function () {
        loading.release();
    });
    $('#inputCariPartNumber').on('keypress',function(e) {
        if(e.which == 13) {
            part_number =  $('#inputCariPartNumber').val().trim();

            if(part_number == '' || part_number == null) {
                Swal.fire({
                    text: 'Isi data part number yang ingin dicari terlebih dahulu',
                    icon: "warning",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
            } else {
                page = 1;
                loadDaftarPartNumber();
            }
        }
    });

    $('#btnCariPartNumber').on('click',function(e) {
        part_number =  $('#inputCariPartNumber').val().trim();

        if(part_number == '' || part_number == null) {
            Swal.fire({
                text: 'Isi data part number yang ingin dicari terlebih dahulu',
                icon: "warning",
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
            });
        } else {
            page = 1;
            loadDaftarPartNumber();
        }
    });

    
    $('#tableResultPartNumber').on('change', '#per_page',function () {
        page = 1;
        per_page = $(this).val();
        loadDaftarPartNumber();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    })
    
    $('#tableResultPartNumber').on('click', '.pagination .page-item:not(.disabled)', function(event) {
        page = $(this).find('.page-link').attr('data-page');
        loadDaftarPartNumber();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    window.history.pushState("", "", window.location.href.split('?')[0]);
});