function ViewListPembayaran(data_khasbank){
    $('#ListPembayaranCard').html('');
    data_khasbank.forEach(function (v, a) {
        $('#ListPembayaranCard').append(`
            <div class="card border border-secondary col-12 p-6 mt-3" style="cursor: pointer;">
                <div class="row">
                    <div class="col-5 ps-0 m-0">
                        <div class="col-12">
                            <span class="fs-4 fw-bolder" id="card_faktur">${v.no_faktur}</span>
                        </div>
                        <div class="col-12">
                            <span class="fw-bold text-gray-400">${moment(v.tgl_faktur, 'YYYY-MM-DD').format('DD/MM/YYYY')}</span>
                        </div>
                        <div class="col-12">
                            <table>
                                <tbody>
                                    <tr class="fw-bold text-gray-400">
                                        <td>Dealer</td>
                                        <td>:</td>
                                        <td>${v.kd_dealer}</td>
                                    </tr>
                                    <tr class="fw-bold text-gray-400">
                                        <td>MKR</td>
                                        <td>:</td>
                                        <td id="mkr">${v.kd_mkr}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col pe-0 m-0">
                        <table class="w-100">
                            <tbody>
                                <tr>
                                    <td>Jumlah</td>
                                    <td>:</td>
                                    <td class="text-end">${v.jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                </tr>
                                <tr class="border-bottom border-secondary">
                                    <td>Terbayar</td>
                                    <td>:</td>
                                    <td class="text-end">${v.terbayar.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                </tr>
                                <tr>
                                    <td>Sisa</td>
                                    <td>:</td>
                                    <td class="text-end">${v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                </tr>
                                <tr class="text-success">
                                    <td>Jumlah&nbsp;Dibayar</td>
                                    <td>:</td>
                                    <td class="text-end" id="card_jml_dibayar">${v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `);
    });

    // saat memilih card list pembayaran
    let faktur,sisa;
    $('#ListPembayaranCard .card').on('click', function (e) {
        if (e.target.tagName == 'TD' && $(this).hasClass('border-primary')) {
            // if($(e.target). closest tr terdapat id card_jml_dibayar
            if ($(e.target).closest('tr').find('#card_jml_dibayar').length > 0) {
                faktur = $(this).find('#card_faktur').text();
                $('#ModalEdit #exampleModalLabel').text('Edit Pembayaran Faktur : ' + faktur);
                $('#ModalEdit').modal('show');
                $('#Modallistpembayaran').modal('show');
                sisa = $(this).find('#card_jml_dibayar');
                $('#ModalEdit #jml_dibayar').val(sisa.text());

                $('#ModalEdit #jml_dibayar').on('keyup', function (e) {
                    $(this).val($(this).val().replace(/\D/g, '').replace(/^0+/, '').replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                });
            }else{
                $(this).toggleClass('border-secondary').toggleClass('border-primary').toggleClass('bg-light-primary');
            }
        } else {
            $(this).toggleClass('border-secondary').toggleClass('border-primary').toggleClass('bg-light-primary');
        }
        hitungCeklist();
    });
    // end saat memilih card list pembayaran

    // saat simpan jumlah yang mau di ubah
    $('#ModalEdit #simpan_jml_dibayar').on('click', function () {
        let index = data_khasbank.findIndex(x => x.no_faktur == faktur);
        if($('#ModalEdit #jml_dibayar').val() != '' && $('#ModalEdit #jml_dibayar').val() != 0){
            if(index != -1){
                if(parseInt($('#ModalEdit #jml_dibayar').val().replace(/\,/g, '')) > data_khasbank[index].sisa){
                    swal.fire({
                        title: "Peringatan",
                        text: "Jumlah yang diinputkan melebihi sisa piutang!",
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    $('#ModalEdit #jml_dibayar').val(data_khasbank[index].sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }else{
                    sisa.text($('#ModalEdit #jml_dibayar').val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                    $('#ListPembayaran tr').each(function (i, v) {
                        if($(v).children()[2].innerText == faktur){
                            $(v).children()[1].innerText = $('#ModalEdit #jml_dibayar').val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    });

                    // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
                    $('#PenerimaanPembayaran').find('tr').each(function () {
                        if ($(this).find('td').eq(1).text() == data_khasbank[index].no_faktur) {
                            $(this).find('td').eq(3).text($('#ModalEdit #jml_dibayar').val().toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        }
                    });

                    
                    $('#ModalEdit').modal('hide');
                    $('#ModalEdit #jml_dibayar').val(0);

                    totalPembayaran();
                    total();
                }
            }
        } else {
            swal.fire({
                title: "Peringatan",
                text: "Jumlah yang diinputkan tidak boleh kosong!",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
            
            $('#ModalEdit #jml_dibayar').val(data_khasbank[index].sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            
            // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
            $('#PenerimaanPembayaran').find('tr').each(function () {
                if ($(this).find('td').eq(1).text() == data_khasbank[index].no_faktur) {
                    $(this).find('td').eq(3).text(data_khasbank[index].sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
            });
        }
    });
    // end saat simpan jumlah yang mau di ubah

}

function ListTidakada(){
    $('#ListPembayaranCard').html(
        `
            <div class="card border border-secondary col-12 p-6 my-3" style="cursor: pointer;">
                <div class="row">
                    <div class="col-12">
                        <div class="col-12 text-center">
                            <span id="card_faktur">Tidak ada data</span>
                        </div>
                    </div>
                </div>
            </div>
        `
    );
}

function hitungCeklist() {
    let jumlah = $('#ListPembayaranCard .card.border-primary').length;
    $('#Modallistpembayaran #jml_faktur').val(jumlah);
    $('.card-footer .row #jml_faktur').val(jumlah);
    totalPembayaran();
    total();
}

function totalPembayaran() {
    let total = 0;
    $('#ListPembayaranCard').find('.card.border-primary').each(function (i, v) {
        total += parseInt($(v).find('#card_jml_dibayar').text().replace(/\D/g, ''));
    });
    $('#total_pemayaran.form-control').val(total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}

function pilihData(){
    let index = 0;
    $('#ListPembayaranCard .card.border-primary').each(function (i, v) {
        // memasukkan data yang di ceklist ke array
        let faktur = $(v).find('#card_faktur').text();
        // cari pada data_khasbank berdasarkan faktur
        let data = data_khasbank.findIndex(x => x.no_faktur == faktur);
        data_akandibayar[index] = {
            no_faktur: $(this).find('#card_faktur').text(),
            tgl_faktur: data_khasbank[data].tgl_faktur,
            jumlah: $(this).find('#card_jml_dibayar').text().replace(/\D/g, ''),
            dealer: data_khasbank[data].kd_dealer,
        };
        // end memasukkan data yang di ceklist ke array
        index++;
    });
}
function ubahdataList(fak_update,jumlah){
    $('#ListPembayaranCard').find('.card').each(function () {
        if ($(this).find('#card_faktur').text() == fak_update) {
            $(this).removeClass('border-secondary').addClass('border-primary').addClass('bg-light-primary');
            $(this).find('#card_jml_dibayar').text(jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
    });
}

function filterSelectAll(){
    $('#filter_select_all').on('click', function () {
        if(data_khasbank.length > 0){
            if ($(this).hasClass('btn-light')) {
                $(this).removeClass('btn-light').addClass('btn-primary');

                // $('#ListPembayaranCard .card').removeClass('border-secondary').addClass('border-primary').addClass('bg-light-primary'); card yang tidak ada style display none
                $('#ListPembayaranCard .card:not([style*="display: none"])').removeClass('border-secondary').addClass('border-primary').addClass('bg-light-primary');
            } else if ($(this).hasClass('btn-primary')) {
                $(this).removeClass('btn-primary').addClass('btn-light');
                $('#ListPembayaranCard .card').addClass('border-secondary').removeClass('border-primary').removeClass('bg-light-primary');
            }
            hitungCeklist();
        }
    });
}
function filterAuto(){
    $('#ModalFilter .modal-footer button.btn-primary').on('click', function () {
        // $('#filter_list_pembayaran').removeClass('btn-light').addClass('btn-primary');
        // $('#ListPembayaran tr input[type="checkbox"]').prop('checked', false);
        $('#ListPembayaranCard .card').removeClass('border-primary').removeClass('bg-light-primary').addClass('border-secondary');
        let nominal = $('#ModalFilter .modal-body input[name="nominal_awal"]').val().replace(/\D/g, '');
        $('#ListPembayaranCard .card #card_faktur').each(function () {
            let tanggal = $(this).closest('div').next().find('span').text();
            let tanggal_awal = $('#ModalFilter #tgl_awal').val();
            let tanggal_akhir = $('#ModalFilter #tgl_akhir').val();
            if (moment(tanggal, 'DD/MM/YYYY').isBetween(moment(tanggal_awal, 'DD/MM/YYYY') - 1, moment(tanggal_akhir, 'DD/MM/YYYY') + 1)) {
                if($('#ModalFilter #sales').val() != ''){
                    if ($('#ModalFilter #sales').val() == $(this).closest('.card').find('#mkr').text() && $(this).closest('.card').attr('style').indexOf('display: none') == -1) {
                        if (parseInt($(this).closest('.card').find('#card_jml_dibayar').text().replace(/\D/g, '')) <= parseInt(nominal)) {
                            nominal = parseInt(nominal) - parseInt($(this).closest('.card').find('#card_jml_dibayar').text().replace(/\D/g, ''));
                            $(this).closest('.card').toggleClass('border-secondary').toggleClass('border-primary').toggleClass('bg-light-primary');
                        } else if(nominal > 0){
                            $(this).closest('.card').find('#card_jml_dibayar').text(nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                            nominal = 0;
                            $(this).closest('.card').toggleClass('border-secondary').toggleClass('border-primary').toggleClass('bg-light-primary');
                        }
                    }
                } else {
                    if ($(this).closest('.card').attr('style').indexOf('display: none') == -1) {
                        if (parseInt($(this).closest('.card').find('#card_jml_dibayar').text().replace(/\D/g, '')) <= parseInt(nominal)) {
                            nominal = parseInt(nominal) - parseInt($(this).closest('.card').find('#card_jml_dibayar').text().replace(/\D/g, ''));
                            $(this).closest('.card').toggleClass('border-secondary').toggleClass('border-primary').toggleClass('bg-light-primary');
                        } else if(nominal > 0){
                            $(this).closest('.card').find('#card_jml_dibayar').text(nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                            nominal = 0;
                            $(this).closest('.card').toggleClass('border-secondary').toggleClass('border-primary').toggleClass('bg-light-primary');
                        }
                    }
                }
            }
        });

        hitungCeklist();
        $('#ModalFilter').modal('hide');
    });
}
function searchList(keyword){
    let jumlah_block = 0;
    $('#ListPembayaranCard .card #card_faktur').each(function () {
        if ($(this).text().toLowerCase().indexOf(keyword.toLowerCase()) == -1) {
            $(this).closest('.card').hide();
            let jumlah_card = $('#ListPembayaranCard .card').length;
            jumlah_block++;
            if(jumlah_block == jumlah_card){
                $('#ListPembayaranCard').append(`
                    <div id="data_block" class="card border border-secondary col-12 p-6 my-3" style="cursor: pointer;">
                        <div class="row">
                            <div class="col-12 text-center">
                                <span id="card_faktur">Tidak ada data</span>
                            </div>
                        </div>
                    </div>
                `);
            }
        } else {
            $(this).closest('.card').show();
            $('#ListPembayaranCard').find('#data_block').remove();
        }

    });
}