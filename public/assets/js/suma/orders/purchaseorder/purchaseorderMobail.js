// dokumen ready
$(document).ready(function () {
    var pages = 1;
    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                const params = new URLSearchParams(window.location.search)
                for (const param of params) {
                    var year = params.get('year');
                    var month = params.get('month');
                    var salesman = params.get('salesman');
                    var dealer = params.get('dealer');
                }
                pages++;
                loadMoreData(year, month, salesman, dealer, pages);
            }
        }
    });

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }

    async function loadMoreData(year, month, salesman, dealer, pages) {
        loading.block();

        $.ajax({
            url: url.purchase_order,
            type: "get",
            data: {
                year: year, month: month, page: pages, salesman: salesman,
                dealer: dealer
            },

            success: function (response) {
                if (response.html == '') {
                    $('#dataLoadPurchaseOrder').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                    loading.release();
                    return;
                }
                $("#dataPurchaseOrder").append(response.html);
                loading.release();
            },
            error: function () {
                loading.release();
                pages = pages - 1;

                Swal.fire({
                    text: "Gagal mengambil data ke dalam server, Coba lagi",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        });
    }
});