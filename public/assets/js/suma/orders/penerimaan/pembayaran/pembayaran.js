
let data_akandibayar;
let data_khasbank = [];

function total() {
    let sisa = parseInt($('#total_bpk').val().replace(/\D/g, '')) - parseInt($('#total_pemayaran').val().replace(/\D/g, ''));
    $('#sisa.form-control').val(sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}

$(document).ready(function () {

    // tambah atribut data-kt-aside-minimize="on" pada tag body
    $('body').attr('data-kt-aside-minimize', 'on');
    // tambahkan pada id kt_aside_toggle class active
    $('#kt_aside_toggle').addClass('active');

    // saat tambah diskon di klik focus ke pruduk dan merubah tombol enter menjadi tab
    $('.card-body').find('input').on('keydown', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            var index = $('.card-body').find('input').index(this) + 1;
            if ($('.card-body').find('input').eq(index).attr('readonly') || $('.card-body').find('input').eq(index).hasClass('bg-secondary')) {
                for (let i = index; i < $('.card-body').find('input').length; i++) {
                    if (!$('.card-body').find('input').eq(i).attr('readonly') || !$('.card-body').find('input').eq(i).hasClass('bg-secondary')) {
                        $('.card-body').find('input').eq(i).focus();
                        break;
                    }
                }
            } else {
                $('.card-body').find('input').eq(index).focus();
            }
        }
    });
    // end saat tambah diskon

    // terdapat form submit loading
    $('form').submit(function () {
        loading.block();
    });

    // ajax start
    $(document).ajaxStart(function () {
        loading.block();
    });
    // end ajax start

    // ajax stop
    $(document).ajaxStop(function () {
        loading.release();
    });
    // end ajax stop

    // js untuk modal list
    $('#btn_list_pembayaran').on('click', function () {
        $('#list_pembayaran').modal('show');
        $('#filterSearch').on('keyup',function () {
            searchList($(this).val());
        });
        
    });
    // filter
        filterSelectAll();
        filterAuto();
    // end filter

        // Saat Filter pertama di jalanakan
        $('#Modallistpembayaran').on('click','#filter_list_pembayaran', function () {
            if($('#total_bpk').val() != 0 && $('#total_bpk').val() != ''){
                if($('#ModalFilter #nominal_awal').val() == 0 || $('#ModalFilter #nominal_awal').val() == ''){
                    $('#ModalFilter #nominal_awal').val($('#total_bpk').val());
                }
            }

            $('#ModalFilter').modal('show');
            $('#list_pembayaran').modal('show');
        });
        $('#ModalFilter #tgl_awal').flatpickr({
            dateFormat: "d/m/Y",
            defaultDate: moment().format('DD/MM/YYYY')
        });
        $('#ModalFilter #tgl_akhir').flatpickr({
            dateFormat: "d/m/Y",
            defaultDate: moment().format('DD/MM/YYYY')
        });
        $('#ModalFilter #nominal_awal').on('keyup', function (e) {
            $(this).val($(this).val().replace(/\D/g, '').replace(/^0+/, '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        });

        // end Saat fillter pertamakali di jalankan
    
    // end js untuk modal list

    // double click pada jumlah yang akan dibayar
    $('#PenerimaanPembayaran').on('click', '.edit-on', function () {
        $(this).removeClass('edit-on').addClass('update-on');
        $(this).html(`<input type="text" class="form-control form-control-sm" value="${$(this).text()}" style=" width: 13ch;">`);
        $(this).find('input').focus().select();

        $('#PenerimaanPembayaran').on('focus blur keyup', 'input[type="text"]', function (e) {
            $(this).val($(this).val().replace(/\D/g, '').replace(/^0+/, '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        });
    });
    // end double click pada jumlah yang akan dibayar

    // edit jumlah dibayar pada daftar akan dibayar
    function inputValPenerimaan(data) {
        $(data).removeClass('update-on').addClass('edit-on');
        // simpan jumlah yang di ganti
        let jumlah = $(data).find('input').val();
        // merubah menjadi text di inutannya
        $(data).html(jumlah);
        // mencari pada arry dan merubah jumlahnya menjadi jumlah baru
        
        let index_fu = data_khasbank.findIndex((x) => x.no_faktur == $(data).closest('tr').find('td').eq(1).text());
        if (index_fu != undefined) {
            if(parseInt(jumlah.replace(/\D/g, '')) > parseInt(data_khasbank[index_fu].sisa.replace(/\D/g, '')) || jumlah == ''|| jumlah == 0){
                setTimeout(function () {
                    if(jumlah == ''|| jumlah == 0){
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
                        let index_fda = data_akandibayar.findIndex((v) => v.no_faktur == data_khasbank[index_fu].no_faktur);
                        data_akandibayar[index_fda].jumlah = data_khasbank[index_fu].sisa
                    }
                    
                    ubahdataList(data_khasbank[index_fu].no_faktur, data_khasbank[index_fu].sisa);
                },100);
            } else{
                if(data_akandibayar != undefined){
                    let index_fda = data_akandibayar.findIndex((v) => v.no_faktur == data_khasbank[index_fu].no_faktur);
                    data_akandibayar[index_fda].jumlah = jumlah;
                    ubahdataList(data_khasbank[index_fu].no_faktur, data_akandibayar[index_fda].jumlah);
                }
            }
        }

        
        setTimeout(function () {
            totalPembayaran();
            total();
        },100);
    }

    $('#PenerimaanPembayaran').on('blur', '.update-on', function (e) {
        inputValPenerimaan(this);
    });
    $('#PenerimaanPembayaran').on('keydown', '.update-on', function (e) {
        if (e.type == 'keydown' && e.keyCode == 13) {
            inputValPenerimaan(this);
        }
    });
    // end edit jumlah dibayar pada daftar akan dibayar

    // untuk modal search dealer
    $('#form_pp #kd_dealer').on('click', function () {
        $('#btnFilterPilihDealer').trigger('click');
    });

    $('#btnFilterPilihDealer').on('click', function (e) {
        e.preventDefault();
        loadDataDealer(1, 10, '');
        $('#searchDealerForm').trigger('reset');
        $('#dealerSearchModal').modal('show');
    });

    $('#dealerContentModal').on('click', '#selectDealer.btn', function () {
        $('#form_pp #kd_dealer').val($(this).data('kode_dealer'));
        $('#form_pp #nm_dealer').val($(this).data('nama_dealer'));
        $('#dealerSearchModal').modal('hide');
        $('#form_pp #kd_dealer').trigger('change');
    });
    // end modal search dealer
    $('#form_pp #kd_dealer').on('change', function () {
        if ($(this).val() != '') {
            // ambil data dengan ajax
            $.ajax({
                url: base_url + '/orders/penerimaan/pembayaran/daftar',
                type: 'GET',
                dataType: 'json',
                data: {
                    kd_dealer: $(this).val()
                },
                success: function (data) {
                    if (data.status == 0) {
                        $('#PenerimaanPembayaran').html(`
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        `);
                        $('#PenerimaanPembayaran').html(`
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    `);
                    } else {
                        data_khasbank = data.data;
                        if(data.data.length > 0){
                            ViewListPembayaran(data_khasbank);
                            $('#PenerimaanPembayaran').html(`
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            `);
                        } else {
                            ListTidakada();
                            $('#PenerimaanPembayaran').html(`
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            `);
                        }
                    }
                }
            });

            hitungCeklist();
            
            $('#pilih_pembayaran').on('click', function () {
                data_akandibayar = [];
                
                pilihData();

                if(data_akandibayar.length > 0){
                    $('#PenerimaanPembayaran').html('');
                    // looop data pada daftar akan dibayar
                    data_akandibayar.forEach(function (v, a) {
                        $('#PenerimaanPembayaran').append(`
                            <tr>
                                <td>
                                    ${a + 1}
                                </td>
                                <td>${v.no_faktur}</td>
                                <td>${moment(v.tgl_faktur).format('DD/MM/YYYY')}</td>
                                <td class="edit-on">${v.jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                                <td>${v.dealer}</td>
                            </tr>
                        `);
                    });
                    // end loop data pada daftar akan dibayar
                } else {
                    // jika data yang di ceklist kosong
                    $('#PenerimaanPembayaran').html('');
                    $('#PenerimaanPembayaran').html(`
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    `);
                    // end jika data yang di ceklist kosong
                }
                $('#Modallistpembayaran').modal('hide');
            });
        } else {
            $('#form_pp #kd_dealer').val('');
            $('#form_pp #nm_dealer').val('');
            $('#form_pp #total').val('');
            $('#form_pp #total').trigger('keyup');
            total();

            ListTidakada();

            $('#Modallistpembayaran #jml_faktur').val(0);
            $('.card-footer .row #jml_faktur').val(0);
            $('#total_bpk').val(0);
            $('#total_pemayaran').val(0);
            $('#sisa').val(0);
        }
    });

    // merubah menjadi format number
    $('#form_pp #total').on('keyup focus', function (e) {
        if (e.type == 'focus') {
            $(this).select();
        } else {
            $(this).val($(this).val().replace(/\D/g, '').replace(/^0+/, '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            $('#total_bpk.form-control').val($(this).val()==''?0:$(this).val());
            total();
        }
    });
    // end merubah menjadi format number

    // #Simpan data diklk Validasi jika terdapat inputan utama yang kososng
    $('#btn_kirim').on('click', function () {
        if($('#form_pp #kd_dealer').val() == ''){
            if(!$('#form_pp #kd_dealer').hasClass('is-invalid')){
                $('#form_pp #kd_dealer').addClass('is-invalid');
                $('#form_pp #kd_dealer').closest('div').addClass('has-validation');
                $('#form_pp #kd_dealer').after(`
                    <div class="invalid-feedback">
                        Tidak boleh kosong
                    </div>
                `);
            }
        } else {
            if($('#form_pp #kd_dealer').hasClass('is-invalid')){
                $('#form_pp #kd_dealer').removeClass('is-invalid');
                $('#form_pp #kd_dealer').next().next().remove();
            }
        }

        if($('#form_pp #jenis_transaksi').val() == ''){
            if(!$('#form_pp #jenis_transaksi').hasClass('is-invalid')){
                $('#form_pp #jenis_transaksi').addClass('is-invalid');
                $('#form_pp #jenis_transaksi').after(`
                    <div class="invalid-feedback">
                        Tidak boleh kosong
                    </div>
                `);
            }
        }else {
            if($('#form_pp #jenis_transaksi').hasClass('is-invalid')){
                $('#form_pp #jenis_transaksi').removeClass('is-invalid');
                $('#form_pp #jenis_transaksi').next().remove();
            }
        }

        if($('#form_pp #total').val() == '' || $('#form_pp #total').val() == 0){
            if(!$('#form_pp #total').hasClass('is-invalid')){
                $('#form_pp #total').addClass('is-invalid');
                $('#form_pp #total').after(`
                    <div class="invalid-feedback">
                        Tidak boleh kosong
                    </div>
                `);
            }
        }else {
            if($('#form_pp #total').hasClass('is-invalid')){
                $('#form_pp #total').removeClass('is-invalid');
                $('#form_pp #total').next().remove();
            }
        }

        if($('#form_pp #total').val() != '' && $('#form_pp #total').val() != 0 && $('#form_pp #kd_dealer').val() != '' && $('#form_pp #jenis_transaksi').val() != ''){
            cekData(0);
        }
    });
    // end #Simpan data diklk Validasi jika terdapat inputan utama yang kososng

    function cekData(total_akhir){
        if(data_akandibayar !=  undefined && data_akandibayar.length != 0){
            data_akandibayar.forEach(function (v, a) {
                total_akhir = parseInt(total_akhir??0) + parseInt(v.jumlah.replace(/\D/g, ''));
            });
            if (parseInt($('#total').val().replace(/\D/g, '')) != parseInt(total_akhir) && parseInt($('#total').val().replace(/\D/g, '')) != 0){
                swal.fire({
                    title: "Peringatan",
                    text: "Terdapat Sisa Pada Perhitungan!",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            } else {
                swal.fire({
                    title: "Apakah Anda Yakin Menyimpan Data?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Simpan!",
                    cancelButtonText: "Tidak, Batalkan!",
                    reverseButtons: true,
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-secondary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        // membuat inputan untuk menampung data
                        $('#form_pp').append(`
                            <input type="hidden" name="detail" value='${JSON.stringify(data_akandibayar)}'>
                        `);
                        // dikirim ke controller
                        $('#form_pp').attr('action', base_url + '/orders/penerimaan/pembayaran/simpan').submit();
                    } else if (result.dismiss === "cancel") {
                    }
                });
            }
        } else {
            swal.fire({
                title: "Peringatan",
                text: "Anda belum memilih data yang akan diBayar!",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }
    }

    $('#tabel_list_akan_dibayar').on('scroll', function () {
        $('#tabel_list_akan_dibayar thead').css('transform', 'translateY(' + this.scrollTop + 'px)');
    });

    // hendel jika terjadi masalah pada server
    if(old.length != 0){
        
        $('#form_pp #kd_dealer').trigger('change');
        $('#form_pp #total').trigger('keyup');
        
        if(old.detail.length != 0){
            
            $(document).ajaxStop(function () {
                data_akandibayar = old.detail;
                $('#PenerimaanPembayaran').html('');
                data_akandibayar.forEach(function (v, a) {
                    $('#PenerimaanPembayaran').append(`
                        <tr>
                            <td>
                                ${a + 1}
                            </td>
                            <td>${v.no_faktur}</td>
                            <td>${moment(v.tgl_faktur).format('DD/MM/YYYY')}</td>
                            <td class="edit-on">${v.jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
                            <td>${v.dealer}</td>
                        </tr>
                    `);

                    ubahdataList(v.no_faktur, v.jumlah);
                    hitungCeklist();
                });
            });

        }
    }
    // end hendel jika terjadi masalah pada server
});