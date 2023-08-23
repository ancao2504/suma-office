$(document).ready(function() {
    $('body').on('click', '.menu-item a', function(e) {
        loading.block();
    });

    $(window).on('offline', function(e) {
        Swal.fire({
            title: "Anda sedang offline",
            text: "Silakan periksa koneksi internet Anda.",
            icon: "warning",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false
        });
    });

    $(window).on('online', function(e) {
        Swal.close();
    });

    // menganti enter ke tab jika dia input, select, textarea
    $("input, select, textarea").on('keypress',function(e) {
        if (e.key === 'Enter') {
            var inputs = $("input, select, textarea").filter(':visible:not([disabled]):not([readonly])');
            inputs[inputs.index(this) + 1].focus();
            e.preventDefault();
        }
    });
});