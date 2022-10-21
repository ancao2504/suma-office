// dokumen ready
$(document).ready(function () {
    // jika terdapat submit pada form
    $('form').submit(function (e) {
        loading.block();
    });
    // end form

    $('a').click(function () {
        var href = $(this).attr('href');
        if (href != undefined && href != '') {
            loading.block();
        }
    });
});