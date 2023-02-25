function reloadDaftarOrders(nomor_invoice = '') {
    loading.block();
    window.location.href = window.location.origin + window.location.pathname + '?nomor_invoice=' + nomor_invoice;
}

$(document).ready(function () {
    $('#inputNomorInvoice').on('change', function (e) {
        e.preventDefault();
        var nomor_invoice = $('#inputNomorInvoice').val();
        reloadDaftarOrders(nomor_invoice);
    });

    $('#btnFilterMasterData').on('click', function (e) {
        e.preventDefault();
        var nomor_invoice = $('#inputNomorInvoice').val();
        reloadDaftarOrders(nomor_invoice);
    });
});
