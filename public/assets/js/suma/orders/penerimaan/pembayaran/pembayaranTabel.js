function ViewListPembayaran(data_khasbank) {
    $('#ListPembayaran').html('');
    data_khasbank.forEach(function (v, a) {
        // looop data pada modal
        $('#ListPembayaran').append(`
            <tr>
                <td>
                    <div class="form-check form-check-custom form-check-solid form-check-lg">
                        <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault">
                    </div>
                </td>
                <td>${v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                <td>${v.no_faktur}</td>
                <td>${moment(v.tgl_faktur, 'YYYY-MM-DD').format('DD/MM/YYYY')}</td>
                <td>${v.jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                <td>${v.terbayar.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                <td>${v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                <td>${v.kd_dealer}</td>
                <td>${v.kd_mkr}</td>
            </tr>
        `);
        // end loop data pada modal
    });
}
function ListTidakada(){
    $('#ListPembayaran').html(`
        <tr>
            <td colspan="10" class="text-center text-muted">Tidak ada data</td>
        </tr>
    `);
}
function searchList(keyword){
    let jumlah_block = 0;
    $('#ListPembayaran tr td:nth-child(3)').each(function () {
        if ($(this).text().toLowerCase().indexOf(keyword.toLowerCase()) == -1) {
            $(this).parent().hide();
            jumlah_block++;
            let jumlah_tr = $('#ListPembayaran tr').length;
            if(jumlah_block == jumlah_tr){
                $('#ListPembayaran').append(`
                    <tr id="data_block">
                        <td colspan="10" class="text-center text-muted table-active">Tidak ada data</td>
                    </tr>
                `);
            }
        } else {
            $(this).parent().show();
            // hapus data blosck
            $('#ListPembayaran').find('#data_block').remove();
        }
    });
}
function filterSelectAll(){
    $('#filter_select_all').on('click', function () {
        if($(this).is(':checked')){
            $('#ListPembayaran tr:not([style="display: none;"]) input[type="checkbox"]').prop('checked', true);
            $('#ListPembayaran tr:not([style="display: none;"]) td:nth-child(2)').addClass('edit-on');
        } else {
            $('#ListPembayaran tr input[type="checkbox"]').prop('checked', false);
            $('#ListPembayaran tr td:nth-child(2)').removeClass('edit-on');
        }
        hitungCeklist();
    });
}

function filterAuto(){
    $('#ModalFilter .modal-footer button.btn-primary').on('click', function () {
        // $('#filter_list_pembayaran').removeClass('btn-light').addClass('btn-primary');

        $('#ListPembayaran tr input[type="checkbox"]').prop('checked', false);
        let nominal = $('#ModalFilter .modal-body input[name="nominal_awal"]').val().replace(/\D/g, '');
        $('#ListPembayaran tr td:nth-child(4)').each(function () {
            let tanggal = $(this).text();
            let tanggal_awal = $('#ModalFilter #tgl_awal').val();
            let tanggal_akhir = $('#ModalFilter #tgl_akhir').val();
            if (moment(tanggal, 'DD/MM/YYYY').isBetween(moment(tanggal_awal, 'DD/MM/YYYY') - 1, moment(tanggal_akhir, 'DD/MM/YYYY') + 1)) {
                if($('#ModalFilter #sales').val() != ''){
                    if ($('#ModalFilter #sales').val() == $(this).parent().find('td:nth-child(9)').text() && $(this).closest('tr').attr('style') != 'display: none;') {
                        if (parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, '')) <= parseInt(nominal)) {
                            nominal -= parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, ''));
                            $(this).parent().find('td:nth-child(1) input').prop('checked', true);
                            $(this).parent().find('td:nth-child(2)').addClass('edit-on');
                        } else if (nominal > 0) {
                            $(this).parent().find('td:nth-child(2)').text(nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                            nominal = 0;
                            $(this).parent().find('td:nth-child(1) input').prop('checked', true);
                            $(this).parent().find('td:nth-child(2)').addClass('edit-on');
                        }
                    }
                } else {
                    if ($(this).closest('tr').attr('style') != 'display: none;') {
                        if (parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, '')) <= parseInt(nominal)) {
                            nominal -= parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, ''));
                            $(this).parent().find('td:nth-child(1) input').prop('checked', true);
                            $(this).parent().find('td:nth-child(2)').addClass('edit-on');
                        } else if (nominal > 0) {
                            $(this).parent().find('td:nth-child(2)').text(nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                            nominal = 0;
                            $(this).parent().find('td:nth-child(1) input').prop('checked', true);
                            $(this).parent().find('td:nth-child(2)').addClass('edit-on');
                        }
                    }
                }
            }
        });

        hitungCeklist();
        $('#ModalFilter').modal('hide');
    });
}
function pilihData(){
    let index = 0;
    $('#ListPembayaran tr').each(function (i, v) {
        if ($(this).find('input[type="checkbox"]').is(':checked')) {
            // memasukkan data yang di ceklist ke array
            let data = data_khasbank.findIndex(x => x.no_faktur == $(this).find('td:nth-child(3)').text());
            data_akandibayar[index] = {
                no_faktur: $(this).find('td:nth-child(3)').text(),
                tgl_faktur: data_khasbank[data].tgl_faktur,
                jumlah: $(this).find('td:nth-child(2)').text(),
                dealer: data_khasbank[data].kd_dealer,
            };
            // end memasukkan data yang di ceklist ke array
            index++;
        }
    });
}
function inputValList(data) {
    $(data).removeClass('update-on').addClass('edit-on');
    // simpan jumlah yang di ganti
    let jumlah = $(data).find('input').val();
    // merubah menjadi text di inutannya
    $(data).html(jumlah);
    // mencari pada arry dan merubah jumlahnya menjadi jumlah baru
        
        let index_fu = data_khasbank.findIndex((x) => x.no_faktur == $(data).closest('tr').find('td').eq(2).text());
        if (index_fu != undefined) {
            if(parseInt(jumlah.replace(/\D/g, '')) > parseInt(data_khasbank[index_fu].sisa.replace(/\D/g, '')) || jumlah == ''|| jumlah == 0){
                if(jumlah == ''|| jumlah == 0){
                    swal.fire({
                        title: "Peringatan",
                        text: "Jumlah yang diinputkan tidak boleh kososng!",
                        icon: "warning",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                } else {
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
                }

                $(data).text(data_khasbank[index_fu].sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                if(data_akandibayar != undefined){
                    let index_fda = data_akandibayar.findIndex((x) => x.no_faktur == data_khasbank[index_fu].no_faktur);
                    data_akandibayar[index_fda].jumlah = data_khasbank[index_fu].sisa;
                }
                // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
                $('#PenerimaanPembayaran').find('tr').each(function () {
                    if ($(this).find('td').eq(1).text() == data_khasbank[index_fu].no_faktur) {
                        $(this).find('td').eq(3).text(data_khasbank[index_fu].sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    }
                });
            } else {
                if(data_akandibayar != undefined){
                    let index_fda = data_akandibayar.findIndex((x) => x.no_faktur == data_khasbank[index_fu].no_faktur);
                    data_akandibayar[index_fda].jumlah = jumlah;
    
                    // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
                    $('#PenerimaanPembayaran').find('tr').each(function () {
                        if ($(this).find('td').eq(1).text() == data_khasbank[index_fu].no_faktur) {
                            $(this).find('td').eq(3).text(jumlah);
                        }
                    });
                }
            }
        }

    totalPembayaran();
    total();
}
function hitungCeklist() {
    let jumlah = $('#ListPembayaran').find('input[type="checkbox"]:checked').length;
    $('#Modallistpembayaran #jml_faktur').val(jumlah);
    $('.card-footer .row #jml_faktur').val(jumlah);
    totalPembayaran();
    total();
}
function totalPembayaran() {
    let total = 0;
    $('#ListPembayaran').find('input[type="checkbox"]:checked').each(function (i, v) {
        total += parseInt($(v).closest('tr').find('td').eq(1).text().replace(/\D/g, ''));
    });
    $('#total_pemayaran.form-control').val(total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}
function ubahdataList(fak_update,jumlah){
    $('#ListPembayaran').find('tr').each(function () {
        if ($(this).find('td').eq(2).text() == fak_update) {
            $(this).find('td').eq(0).find('input').prop('checked', true);
            // $('#ListPembayaran tr:not([style="display: none;"]) td:nth-child(2)').addClass('edit-on');
            $(this).not('[style="display: none;"]').find('td:nth-child(2)').addClass('edit-on');
            $(this).find('td').eq(1).text(jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
    });
}

$(document).ready(function () {
    $('#ListPembayaran').on('click', '#flexCheckDefault', function () {
        if (this.checked) {
            $(this).closest('td').next().addClass('edit-on');
        } else {
            $(this).closest('td').next().removeClass('edit-on');
        }
    });

    $('#ListPembayaran').on('click', '.edit-on', function () {
        // if ($(this).closest('tr').eq(0).find('td > div > input[type="checkbox"]').is(':checked')) {
        $(this).removeClass('edit-on').addClass('update-on');
        $(this).html(`<input type="text" class="form-control form-control-sm" value="${$(this).text()}" style=" width: 13ch;">`);
        $(this).find('input').focus().select();
        // }

        $('#ListPembayaran').on('focus blur keyup', 'input[type="text"]', function (e) {
            $(this).val($(this).val().replace(/\D/g, '').replace(/^0+/, '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        });
    });

    $('#ListPembayaran').on('blur', '.update-on', function (e) {
        inputValList(this);
    });
    $('#ListPembayaran').on('keydown', '.update-on', function (e) {
        if (e.type == 'keydown' && e.keyCode == 13) {
            inputValList(this);
        }
    });

    $('#ListPembayaran').on('click', '#flexCheckDefault', function () {
        hitungCeklist();
    });

    $('#ListPembayaran').on('change', 'input[type="text"]', function () {
        setTimeout(function () {
            totalPembayaran();
            total();
        }, 100);
    });

    
    $('#tabel_list').on('scroll', function () {
        $('#tabel_list thead').css('transform', 'translateY(' + this.scrollTop + 'px)');
    });
});


