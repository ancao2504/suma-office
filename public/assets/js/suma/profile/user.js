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

    // page scroll
    var pages = 1;

    $(window).scroll(function () {
        if (loading.isBlocked() === false) {
            if ($(window).scrollTop() >= $(document).height() - $(window).height() - 10) {
                const params = new URLSearchParams(window.location.search)
                for (const param of params) {
                    var user_id = params.get('search');
                    var role_filter = params.get('role_filter');
                }
                pages++;
                loadMoreData(user_id, pages, role_filter);
            }
        }
    });

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }
    // end page scroll

    // load more data
    async function loadMoreData(user_id, pages, role_filter) {
        loading.block();
        $.ajax({
            url: url_profile_user,
            type: "get",
            data: { user_id: user_id, page: pages, role_filter: role_filter },

            success: function (response) {
                if (response.html == '') {
                    $('#dataLoadUsers').html('<center><div class="fw-bolder fs-3 text-gray-600 text-hover-primary mt-10 mb-10">- No more record found -</div><center>');
                    loading.release();
                    return;
                }
                $("#dataUsers").append(response.html);
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
    // end load more data

    // $(document).ready(function() {
    //     $('body').on('click', '#modalUser', function () {
    //         $("#userModalForm").modal({ backdrop: "static ", keyboard: false });
    //         // var _token = $('input[name="_token"]').val();
    //         $('#userModalForm').modal('show');

    //         $('#userModalForm').on('shown.bs.modal', function () {
    //         });
    //     });

    // });
});