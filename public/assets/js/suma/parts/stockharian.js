$(document).ready(function () {
    $('#btnCetak').on('click', function (e) {
        console.log('cetak');
    });
    $('#btnExcel').on('click', function (e) {
        loading.block();

        kode_class = $('#selectClassProduk').val();
        kode_produk = $('#selectGroupProduk').val();
        kode_produk_level = $('#selectGroupLevel').val();
        kode_sub = $('#selectSubProduk').val();
        frg = $('#selectFrg').val();
        kode_lokasi = $('#selectKodeLokasi').val();
        kode_rak = $('#inputKodeRak').val();
        option_stock_sedia = $('#selectStockSedia').val();
        nilai_stock_sedia = $('#inputNilaiStockSedia').val();

        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            type: 'get',
            url: url.export_excel,
            data: {
                kode_class: kode_class, kode_produk: kode_produk, kode_produk_level: kode_produk_level,
                kode_sub: kode_sub, frg: frg, kode_lokasi: kode_lokasi, kode_rak: kode_rak,
                option_stock_sedia: option_stock_sedia, nilai_stock_sedia: nilai_stock_sedia
            },
            success: function(result, status, xhr) {
                loading.release();

                var disposition = xhr.getResponseHeader('content-disposition');
                var matches = /"([^"]*)"/.exec(disposition);
                var filename = (matches != null && matches[1] ? matches[1] : 'StockHarian.xlsx');

                // The actual download
                var blob = new Blob([result], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;

                document.body.appendChild(link);

                link.click();
                document.body.removeChild(link);
            },
            error: function() {
                loading.release();
                Swal.fire({
                    text: 'Server not responding, coba lagi',
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    });
});
