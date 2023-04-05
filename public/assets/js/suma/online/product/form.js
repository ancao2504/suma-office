let brand = {
    offset: 0,
    has: true,
};
let prosesRespown = false;

// ! fungsi merubah Input kategori sesuai yang diklik pada DropDown
function kategori(target,ket, val) {
    $('#'+target).val(ket);
    $('#'+target).attr('data-id', val);

    if(target == 'kategoriShopee'){
        $('#tableBrand tbody').html('');
    }
}

// ! fungsi Kirim data
function submitData() {
    $.ajax({
        url: base_url + '/online/product/marketplace/form/add',
        type: 'POST',
        data: {
            _token      : $('meta[name="csrf-token"]').attr('content'),
            image       : JSON.stringify($('#images_product').data('ket')),
            nama        : $('#nama_produk').val(),
            merek       : $('#merekShopee').data('id'),
            deskripsi   : $('#deskripsi_produk').val(),
            harga       : parseInt($('#harga_produk').val().replace(/[^0-9]/g, '')),
            stok        : parseInt($('#stock_produk').val().replace(/[^0-9]/g, '')),
            min_order   : parseInt($('#minimal_order').val().replace(/[^0-9]/g, '')),
            berat       : $('#berat_paket').val(),
            ukuran      : {
                            panjang: $('#panjang_paket').val(),
                            lebar: $('#lebar_paket').val(),
                            tinggi: $('#tinggi_paket').val(),
                        },
            sku         : $('#sku_produk').val(),
            kondisi     : $('#kondisi_produk').val(),
            kategori    : (($('#marketplace_add').data('ket') == 'shopee')?$('#kategoriShopee').data('id'):$('#kategoriTokopedia').data('id')),
            status      : (($('#marketplace_add').data('ket') == 'shopee')?$('#status_produk_shopee').val():$('#status_produk_tokopedia').val()),
            etalase     : $('#etalase_produk').val(),
            logistic    : [
                            {
                                "enabled"       : true,
                                "logistic_id"   : (($('#checkbox0').is(':checked'))?$('#checkbox0').data('id'):0)
                            },
                            {
                                "enabled"       : true,
                                "logistic_id"   : (($('#checkbox1').is(':checked'))?$('#checkbox1').data('id'):0)
                            },
                            {
                                "enabled"       : true,
                                "logistic_id"   : (($('#checkbox2').is(':checked'))?$('#checkbox2').data('id'):0)
                            },
                            {
                                "enabled"       : true,
                                "logistic_id"   : (($('#checkbox3').is(':checked'))?$('#checkbox3').data('id'):0)
                            },
                            {
                                "enabled"       : true,
                                "logistic_id"   : (($('#checkbox4').is(':checked'))?$('#checkbox4').data('id'):0)
                            }
                        ],
            marketplace_update  : $('#marketplace_add').data('ket'),
        },
        dataType: 'json',
        beforeSend: function () {
            loading.block();
            $('#tambah_baru_item').attr('disabled', true);
            $('#tambah_baru_item').html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`);
        },
        success: function (res) {
            if (res.status == 1) {
                Swal.fire({
                    title: 'Berhasil',
                    html: res.message,
                    icon: 'success',
                    allowOutsideClick: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                }).then((result) => {
                    if (result.value) {
                        window.location.href = $('#btnBack').attr('href');
                    }
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    html: res.message,
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.value) {
                    }
                });
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            Swal.fire({
                title: 'Gagal',
                text: 'Terjadi kesalahan, silahkan coba lagi',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value) {
                }
            });
        },
        complete: function () {
            $('#tambah_baru_item').attr('disabled', false);
            $('#tambah_baru_item').html('<i class="fas fa-plus"></i> Buat Produk');
            loading.release();
        }
    });
}

// ! fungsi pemangilan brand/Merek
function brendList() {
    $.ajax({
        url: base_url + '/online/product/shopee/brand',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            offset: brand.offset,
            category_id: $('#kategoriShopee').data('id')
        },
        beforeSend: function () {
            prosesRespown = true;
            $('#tableBrand tbody').append(`<tr id="loadingBrnad"><td colspan="2" style="text-align:center;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`);
        },
        success: function (res) {
            let html = res.data.brand_list.map(function (item) {
                return `<tr>
                            <td class="ps-3 pe-3" style="text-align:center;vertical-align:top;">
                                <button type="button" class="btn btn-sm btn-primary" id="pilihBrand" data-id="${item.brand_id}" data-ket="${item.name}">
                                    Pilih
                                </button>
                            </td>
                            <td class="ps-3 pe-3" style="text-align:left;vertical-align:top;">
                                <span class="fs-6 fw-bolder text-gray-800">${item.name}</span>
                            </td>
                        </tr>`;
            });
            $('#loadingBrnad').remove();
            $('#tableBrand tbody').append(html);
            brand.offset = res.data.page_info.next_page;
        },
        error: function (xhr, ajaxOptions, thrownError) {
        },
        complete: function () {
        $('#modalBrand').modal('show');
        prosesRespown = false;
        }
    });
}

// ! Dokumen Ready
$(document).ready(function () {
    // ! Tombol tambah baru diklik
    $('.card.card-flush').on('click','#tambah_baru_item', function (e) {
        let error = 0;
        $('body').find('input[required], select[required], textarea[required]').each(function () {
            if ($(this).val() == '' && $(this).attr('id') != 'inputKodeSalesIndex' && $(this).attr('id') != 'inputKodeDealerIndex') {
                error=1;
                $(this).addClass('is-invalid');
                if (!$(this).next().hasClass('invalid-feedback')) {
                    $(this).after('<div class="invalid-feedback">Data tidak boleh kosong</div>');
                }
                $(this).focus();
            } else {
                $(this).removeClass('is-invalid');
                if ($(this).next().hasClass('invalid-feedback')) {
                    $(this).next().remove();
                }
            }
        });

        if ($('#checkbox0').is(':checked') || $('#checkbox1').is(':checked') || $('#checkbox2').is(':checked') || $('#checkbox3').is(':checked') || $('#checkbox4').is(':checked')) {
            $('#accordionLogistic .accordion-item').removeClass('border-danger');
            if ($('#accordionLogistic').next().hasClass('invalid-feedback')) {
                $('#accordionLogistic').next().remove();
            }
        } else {
            error=1;
            $('#accordionLogistic .accordion-item').addClass('border-danger');
        }

        if (error == 0) {
            Swal.fire({
                title: 'Warning',
                html: 'Apakah anda yakin akan menambahkan produk ini pada <b>'+$('#marketplace_add').data('ket')+'</b> ?',
                icon: 'warning',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn btn-warning',
                    cancelButton: 'btn btn-secondary'
                },
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    submitData();
                }
            });
        }
    });

    // ! Saat klik tombol pilih Merek
    $('#merekShopee[readonly]').on('click', function (e) {
        if (!$('#kategoriShopee').data('id')) {
            Swal.fire({
                title: 'Warning',
                text: 'Silahkan pilih kategori terlebih dahulu',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-warning'
                },
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value) {
                }
            });
        } else {
            if(brand.has == true){
                if ($('#modalBrand .modal-body').find('tbody').children().length == 0) {
                    brendList();
                }
                
                $('#modalBrand').modal('show');
            }
        }

        $('#modalBrand .modal-body').on('scroll', function() {
            var $this = $(this);
            var scrollTop = $this.scrollTop();
            var height = $this.height();
            var outerHeight = $this.outerHeight();
            var scrollHeight = $this[0].scrollHeight;
            if (scrollTop - 500 + height >= scrollHeight - outerHeight && prosesRespown == false) {
                brendList();
            }
        });

        $('#modalBrand .modal-body').on('click', '#pilihBrand', function (e) {
            $('#merekShopee').val($(this).data('ket'));
            $('#merekShopee').data('id', $(this).data('id'));
            $('#modalBrand').modal('hide');
        });
    });

    // ! format rupiah dengan koma
    $('#harga_produk, #stock_produk, #minimal_order').on('keyup', function (e) {
        if ($(this).val() == '') {
            $(this).val('');
        } else {
            $(this).val(parseInt($(this).val().replace(/[^0-9]/g, '')).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
    });
});