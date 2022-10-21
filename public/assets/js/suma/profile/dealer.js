// dokumen ready
$(document).ready(function () {
    // jika terdapat form submit
    $('form').submit(function () {
        loading.block();
    });
    // end jika terdapat form submit
    // jika terdapat button click
    $('#btnFilterReset').click(function () {
        loading.block();
    });
    // end jika terdapat button click

    // pegination scroll
    var pages = 1;

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                const params = new URLSearchParams(window.location.search)
                for (const param of params) {
                    var search = params.get('search');
                }
                pages++;
                loadMoreData(search, pages);
            }
        }
    });

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }
    // end pegination scroll

    // fungsi menampilkan data
    async function loadMoreData(search, pages) {
        loading.block();
        $.ajax({
            url: url_profile_dealer,
            type: "get",
            data: { search: search, page: pages },
            success: function (response) {
                if (response.html == '') {
                    $('#dataLoadDealer').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                    loading.release();
                    return;
                }
                $("#dataDealer").append(response.html);
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
    // end fungsi menampilkan data
});