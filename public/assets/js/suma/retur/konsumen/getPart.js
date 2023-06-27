function Part(requst){
    loading.block();
    $.get(base_url+'/part',{
        option: requst.option,
        no_faktur: $('#no_faktur').val(),
        kd_sales: $('#kd_sales').val(),
        kd_part: requst.kd_part,
        page : requst.page,
        per_page : requst.per_page
    }, function (response) {
        if(response.status == '1'){
            let dataJson = response.data;
            if(requst.option == 'first'){
                if (jQuery.isEmptyObject(dataJson)) {
                    toastr.error('Part Number Tidak Ditemukan!', "info");
                    $('#kd_part').addClass('is-invalid');
                    $('#kd_part').removeClass('is-valid');
                } else {
                    $('#kd_part').val(dataJson.kd_part);
                    $('#ket_part').val(dataJson.nm_part);
                    $('#qty_faktur').val(dataJson.jml_jual);
                    if (dataJson.jml_jual == 0) {
                        toastr.error('Jumlah Jual Bernilai 0!', "info");
                    } else {
                        if (dataJson.harga != '' && dataJson.harga != 0 && dataJson.harga != null && dataJson.harga != undefined) {
                            var options = { style: 'decimal', useGrouping: true, minimumFractionDigits: 0, maximumFractionDigits: 2, minimumIntegerDigits: 1 };
                            
                            $('#harga').val(Number(dataJson.harga).toLocaleString('en-US', options));
                            $('#disc').val((parseFloat(disc2)) ? parseFloat(disc2) : '');
                            $('#qty_claim,' + '#harga,' + '#disc').on('keyup', function () {
                                if (parseInt($('#disc').val()) > 100) {
                                    $('#disc').val(100);
                                } else if (parseInt($('#disc').val()) < 0) {
                                    $('#disc').val(0);
                                }
        
                                let ttl = (Number($('#qty_claim').val().replace(/[^\d.-]/g, '')) * Number($('#harga').val().replace(/[^\d.-]/g, '')));
                                let disc01 = (ttl * (dataJson.disc1 / 100));
                                let disc02 = ((disc01 == 0) ? ttl * (Number($('#disc').val().replace(/[^\d.-]/g, '')) / 100) : (ttl - disc01) * (Number($('#disc').val().replace(/[^\d.-]/g, '')) / 100));
                                let total = ttl - disc01 - disc02;
        
                                $('#total').val(total.toLocaleString('en-US', options));
                            });
    
                            let ttl = (Number($('#qty_claim').val().replace(/[^\d.-]/g, '')) * Number($('#harga').val().replace(/[^\d.-]/g, '')));
                            let disc01 = (ttl * (dataJson.disc1 / 100));
                            let disc02 = ((disc01 == 0) ? ttl * (Number($('#disc').val().replace(/[^\d.-]/g, '')) / 100) : (ttl - disc01) * (Number($('#disc').val().replace(/[^\d.-]/g, '')) / 100));
                            let total = ttl - disc01 - disc02;
        
                            $('#total').val(total.toLocaleString('en-US', options));
                        }
                    }
                    $('#kd_part').removeClass('is-invalid');
                    $('#kd_part').addClass('is-valid');
                }
            } else if (requst.option == 'page') {
                $('#part-list').html(response.data);
                $('#part-list').modal('show');
            } else {
                $('#dealer-list .close').trigger('click')
            }
        }
        if (response.status == '0') {
            toastr.error(response.message, "Error");
        }
        if (response.status == '2') {
            swal.fire({
                title: 'Perhatian!',
                text: response.message,
                icon: 'warning',
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
        }
    }).always(function () {
        loading.release();
    }).fail(function (jqXHR, textStatus, error) {
        $('#part-list .close').trigger('click')
        Swal.fire({
            title: 'Error ' + jqXHR.status,
            text: textStatus,
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
    });
}


$('#part-list').on('click','.pilih' ,function () {
    $('#kd_part').val($(this).data('a'));
    Part({
        option: 'first',
        kd_part: $(this).data('a'),
        page : 1,
        per_page : 10
    });
    $('#part-list .close').trigger('click')
})

$('#kd_part').on('change', function () {
    $('#ket_part').val('');
    if($('#kd_part').val() == ''){
        $('#kd_part').removeClass('is-valid');
        return false;
    }

    Part({
        option: 'first',
        kd_part: $(this).val(),
        page : 1,
        per_page : 10
    });
});

$('.list-part').on('click', function () {
    Part({
        option: 'page',
        page : 1,
        per_page : 10
    });
});

$('#part-list').on('click', '.pagination .page-item', function () {
    Part({
        option: 'page',
        page : $(this).find('a').attr('href').split('page=')[1],
        per_page : $('#part-list').find('#per_page').val()
    });
});

$('#part-list').on('change','#per_page', function () {
    Part({
        option: 'page',
        kd_part: $('#part-list').find('#cari').val(),
        page : 1,
        per_page : $(this).val()
    });
});

$('#part-list').on('click','#btn_cari', function () {
    Part({
        option: 'page',
        kd_part: $('#part-list').find('#cari').val(),
        page : 1,
        per_page : $('#part-list').find('#per_page').val()
    });
});
