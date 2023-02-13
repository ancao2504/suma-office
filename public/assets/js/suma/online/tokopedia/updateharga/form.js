function loadDaftarDetailUpdateHarga() {
    loading.block();
    $.ajax({
        url: url.daftar_update_harga,
        method: "get",
        data: { nomor_dokumen: data.nomor_dokumen },

        success: function (response) {
            loading.release();
            if (response.status == true) {
                $('#tableDetailUpdateHarga').html(response.data);
            } else {
                Swal.fire({
                    text: response.message,
                    icon: "error",
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-danger"
                    }
                });
            }
        },
        error: function () {
            loading.release();
        }
    })
}

$(document).ready(function () {
    loadDaftarDetailUpdateHarga();
});
