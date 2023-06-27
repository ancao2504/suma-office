$('#form-dtl-add .btn-add').on('click', function (e) {
    e.preventDefault();
    loading.block();
    $.post(baseurl + "/returkonsumen/storedtl",
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            no_retur: $('#no_retur').val(),
            kd_sales: $('#kd_sales').val(),
            kd_dealer: $('#kd_dealer').val(),

            no_faktur: $('#form-dtl-add #no_faktur').val(),
            kd_part: $('#form-dtl-add #kd_part').val(),
            qty_claim: $('#form-dtl-add #qty_claim').val(),
            harga: $('#form-dtl-add #harga').val(),
            disc: $('#form-dtl-add #disc').val(),
            ket: $('#form-dtl-add #ket').val(),
            sts: $('#form-dtl-add #sts').val(),
        },
        function (data) {
            Swal.fire({
                text: data.message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "OK !",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            lodaing.release();
            document.getElementById("form-dtl-add").reset();

            $('#form-dtl-add #no_faktur').removeClass('is-valid');
            $('#form-dtl-add #kd_part').removeClass('is-valid');

            $('.nav-item #btn-minimaiz')[0].click();

            getRetur();
        })
        .fail(function (err) {
            // hapus semua class is-invalid pada semua elemen didalam form #form-dtl-add
            $('#form-dtl-add .form-control').removeClass('is-invalid');

            if (err.status === 422) {
                toastr.error("Masih ada data yang kososng !", "info");

                // saat ada data yang kosong
                $.each(err.responseJSON.errors, function (key, value) {
                    $('#form-dtl-add #' + key).addClass('is-invalid');
                    toastr.error(value, "info");
                });
            } else if (err.status === 300) {
                console.log(err.responseJSON);
            }
            else {
                toastr.error(err.responseJSON.message, "info");
            }
            blockUI.release();
        });
})
$('#form-dtl-edit .btn-edit').on('click', function (e) {
    e.preventDefault();
    loading.block();
    $.post(baseurl + "/returkonsumen/detailupdate",
        {
            _token: $('meta[name="csrf-token"]').attr('content'),
            no_retur: $('#no_retur').val(),
            kd_sales: $('#kd_sales').val(),
            kd_dealer: $('#kd_dealer').val(),
            kd_dealer: $('#kd_dealer').val(),

            no_faktur: $('#form-dtl-edit #no_faktur').val(),
            kd_part: $('#form-dtl-edit #kd_part').val(),
            qty_claim: $('#form-dtl-edit #qty_claim').val(),
            harga: $('#form-dtl-edit #harga').val(),
            disc: $('#form-dtl-edit #disc').val(),
            ket: $('#form-dtl-edit #ket').val(),
            sts: $('#form-dtl-edit #sts').val(),
        },
        function (data) {

            Swal.fire({
                text: data.message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "OK !",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            blockUI.release();

            document.getElementById("form-dtl-edit").reset();

            $('#form-dtl-edit #no_faktur').removeClass('is-valid');
            $('#form-dtl-edit #kd_part').removeClass('is-valid');

            $('.nav-item #btn-minimaiz')[0].click();
            getRetur();
        })
        .fail(function (err) {
            // hapus semua class is-invalid pada semua elemen didalam form #form-dtl-add
            $('#form-dtl-edit .form-control').removeClass('is-invalid');

            if (err.status === 422) {
                // akan me looping errors menarget key dari errors dan berisikan value dari errors
                $.each(err.responseJSON.errors, function (key, value) {
                    $('#form-dtl-edit #' + key).addClass('is-invalid');
                    toastr.error(value, "info");
                });
            }
            else {
                toastr.error(err.responseJSON.message, "info");
            }
            loading.release();
        });
})