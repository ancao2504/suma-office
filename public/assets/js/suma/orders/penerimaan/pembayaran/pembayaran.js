$(document).ready(function () {
    let data_akandibayar;
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
        function search(keyword) {
            $('#ListPembayaran tr td:nth-child(3)').each(function () {
                if ($(this).text().toLowerCase().indexOf(keyword.toLowerCase()) == -1) {
                    $(this).parent().hide();

                    // ListPembayaran tr each style display none
                    $('#ListPembayaran tr').each(function () {
                        if ($(this).css('display') != 'none') {
                            $('#ListPembayaran').append(
                                `
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                                `
                            );
                        }
                    });

                } else {
                    $(this).parent().show();
                }
            });
        }
    
        $('#filterSearch').on('keyup',function () {
            search($(this).val());
        })

        // filter_select_all
        $('#filter_select_all').on('click', function () {
            if($(this).is(':checked')){
                $('#ListPembayaran tr:not([style="display: none;"]) input[type="checkbox"]').prop('checked', true);
            } else {
                $('#ListPembayaran tr input[type="checkbox"]').prop('checked', false);
            }
            hitungCeklist();
        });
        // end filter_select_all

        // Fillter pilih otomatis
        $('#ModalFilter .modal-footer button.btn-primary').on('click', function () {
            $('#ListPembayaran tr input[type="checkbox"]').prop('checked', false);
            let nominal = $('#ModalFilter .modal-body input[name="nominal_awal"]').val().replace(/\D/g, '');
            $('#ListPembayaran tr td:nth-child(4)').each(function () {
                let tanggal = $(this).text();
                let tanggal_awal = $('#ModalFilter #tgl_awal').val();
                let tanggal_akhir = $('#ModalFilter #tgl_akhir').val();
                if (moment(tanggal, 'DD/MM/YYYY').isBetween(moment(tanggal_awal, 'DD/MM/YYYY') - 1, moment(tanggal_akhir, 'DD/MM/YYYY') + 1)) {
                    if($('#ModalFilter #sales').val() != ''){
                        if ($('#ModalFilter #sales').val() == $(this).parent().find('td:nth-child(9)').text()) {
                            if (parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, '')) <= parseInt(nominal)) {
                                nominal -= parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, ''));
                                $(this).parent().find('td:nth-child(1) input').prop('checked', true);
                            }
                        }
                    } else {
                        if (parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, '')) <= parseInt(nominal)) {
                            nominal -= parseInt($(this).parent().find('td:nth-child(2)').text().replace(/\D/g, ''));
                            $(this).parent().find('td:nth-child(1) input').prop('checked', true);
                        }
                    }
                }
            });

            hitungCeklist();
            $('#ModalFilter').modal('hide');
        });
        // end Fillter pilih otomatis
    });

        // Saat Filter pertama di jalanakan
        $('#Modallistpembayaran').on('click','#filter_list_pembayaran', function () {
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
            $(this).val($(this).val().replace(/\D/g, '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        });
        
        // end Saat fillter pertamakali di jalankan
    
    // end js untuk modal list



    // triger saat ceklis di klik
    $('#ListPembayaran').on('click', '#flexCheckDefault', function () {
        if (this.checked) {
            $(this).closest('td').next().addClass('edit-on');
        } else {
            $(this).closest('td').next().removeClass('edit-on');
        }
    });
    // end triger saat ceklis di klik

    // double click pada jumlah yang akan dibayar
    $('#ListPembayaran, #PenerimaanPembayaran').on('click', '.edit-on', function () {
        // if ($(this).closest('tr').eq(0).find('td > div > input[type="checkbox"]').is(':checked')) {
        $(this).removeClass('edit-on').addClass('update-on');
        $(this).html(`<input type="text" class="form-control" value="${$(this).text()}" style=" width: 15ch;">`);
        $(this).find('input').focus().select();
        // }

        $('#ListPembayaran, #PenerimaanPembayaran').on('focus blur keyup', 'input[type="text"]', function (e) {
            $(this).val($(this).val().replace(/\D/g, '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        });
    });
    // end double click pada jumlah yang akan dibayar

    // edit jumlah dibayar pada modal
    function inputValList(data) {
        $(data).removeClass('update-on').addClass('edit-on');
        // simpan jumlah yang di ganti
        let jumlah = $(data).find('input').val();
        // merubah menjadi text di inutannya
        $(data).html(jumlah);
        // menyimpan data faktur yang di ganti
        let fak_update = $(data).closest('tr').find('td').eq(2).text();
        // mencari pada arry dan merubah jumlahnya menjadi jumlah baru
        data_khasbank.forEach((v,a)=>{
            if(fak_update == v.no_faktur){
                if(parseInt(jumlah.replace(/\D/g, '')) > parseInt(v.sisa.replace(/\D/g, '')) || jumlah == ''|| jumlah == 0){
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
                    $(data).text(v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    if(data_akandibayar != undefined){
                        data_akandibayar.forEach((i,a)=>{
                            if(fak_update == i.no_faktur){
                                i.jumlah = v.sisa
                            }
                        })
                    }
                    // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
                    $('#PenerimaanPembayaran').find('tr').each(function () {
                        if ($(this).find('td').eq(1).text() == fak_update) {
                            $(this).find('td').eq(3).text(v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        }
                    });
                } else{
                    if(data_akandibayar != undefined){
                        data_akandibayar.forEach((item, index) => {
                            if (item.no_faktur == fak_update) {
                                item.jumlah = jumlah;
                            }
                        });
            
                        // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
                        $('#PenerimaanPembayaran').find('tr').each(function () {
                            if ($(this).find('td').eq(1).text() == fak_update) {
                                $(this).find('td').eq(3).text(jumlah);
                            }
                        });
                    }
                }
            }
        });

        totalPembayaran();
        total();
    }
    $('#ListPembayaran').on('blur', '.update-on', function (e) {
        inputValList(this);
    });
    $('#ListPembayaran').on('keydown', '.update-on', function (e) {
        if (e.type == 'keydown' && e.keyCode == 13) {
            inputValList(this);
        }
    });
    // end edit jumlah dibayar pada modal

    // edit jumlah dibayar pada daftar akan dibayar
    function inputValPenerimaan(data) {
        $(data).removeClass('update-on').addClass('edit-on');
        // simpan jumlah yang di ganti
        let jumlah = $(data).find('input').val();
        // merubah menjadi text di inutannya
        $(data).html(jumlah);
        // menyimpan data faktur yang di ganti
        let fak_update = $(data).closest('tr').find('td').eq(1).text();
        // mencari pada arry dan merubah jumlahnya menjadi jumlah baru
        data_khasbank.forEach((v,a)=>{
            if(fak_update == v.no_faktur){
                if(parseInt(jumlah.replace(/\D/g, '')) > parseInt(v.sisa.replace(/\D/g, '')) || jumlah == ''|| jumlah == 0){
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
                        $(data).text(v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        
                        if(data_akandibayar != undefined){
                            data_akandibayar.forEach((i,a)=>{
                                if(fak_update == i.no_faktur){
                                    i.jumlah = v.sisa
                                }
                            })
                        }
                        
                        // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
                        $('#ListPembayaran').find('tr').each(function () {
                            if ($(this).find('td').eq(2).text() == fak_update) {
                                $(this).find('td').eq(1).text(v.sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                            }
                        });
                    },100);
                } else{
                    if(data_akandibayar != undefined){
                        data_akandibayar.forEach((item, index) => {
                            if (item.no_faktur == fak_update) {
                                item.jumlah = jumlah;
                            }
                        });

                        // merubah pada table akan dibayar mencari faktur yang sama dan merubah jumlahnya
                        $('#ListPembayaran').find('tr').each(function () {
                            if ($(this).find('td').eq(2).text() == fak_update) {
                                $(this).find('td').eq(1).text(jumlah);
                            }
                        });
                    }
                }
            }
        })
        
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

    // merubah menjadi format number
    $('#form_pp #total').on('keyup focus', function (e) {
        if (e.type == 'focus') {
            $(this).select();
        } else {
            $(this).val($(this).val().replace(/\D/g, '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            $('#total_bpk.form-control').val($(this).val());
            total();
        }
    });
    // end merubah menjadi format number

    // perhitungan total
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

    $('#ListPembayaran').on('click', '#flexCheckDefault', function () {
        hitungCeklist();
    });

    function total() {
        let sisa = parseInt($('#total_bpk').val().replace(/\D/g, '')) - parseInt($('#total_pemayaran').val().replace(/\D/g, ''));
        $('#sisa.form-control').val(sisa.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    }

    $('#ListPembayaran').on('change', 'input[type="text"]', function () {
        setTimeout(function () {
            totalPembayaran();
            total();
        }, 100);
    });
    // end perhitungan total

    let data_khasbank = [];
    // = [
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD77360/IX/22",
    //         "tgl_faktur": "2022-09-23",
    //         "jumlah": "1960000",
    //         "terbayar": "1959725",
    //         "sisa": "275"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD78824/IX/22",
    //         "tgl_faktur": "2022-09-27",
    //         "jumlah": "91843200",
    //         "terbayar": "91843000",
    //         "sisa": "200"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD78913/IX/22",
    //         "tgl_faktur": "2022-09-28",
    //         "jumlah": "1433700",
    //         "terbayar": "1433505",
    //         "sisa": "195"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD79792/X/22",
    //         "tgl_faktur": "2022-10-03",
    //         "jumlah": "1031535",
    //         "terbayar": "0",
    //         "sisa": "1031535"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD80263/X/22",
    //         "tgl_faktur": "2022-10-04",
    //         "jumlah": "16023000",
    //         "terbayar": "0",
    //         "sisa": "16023000"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD80280/X/22",
    //         "tgl_faktur": "2022-10-04",
    //         "jumlah": "20757000",
    //         "terbayar": "0",
    //         "sisa": "20757000"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD80296/X/22",
    //         "tgl_faktur": "2022-10-04",
    //         "jumlah": "11865000",
    //         "terbayar": "0",
    //         "sisa": "11865000"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD80619/X/22",
    //         "tgl_faktur": "2022-10-06",
    //         "jumlah": "850500",
    //         "terbayar": "0",
    //         "sisa": "850500"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD80977/X/22",
    //         "tgl_faktur": "2022-10-07",
    //         "jumlah": "1583550",
    //         "terbayar": "0",
    //         "sisa": "1583550"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD81626/X/22",
    //         "tgl_faktur": "2022-10-11",
    //         "jumlah": "208980",
    //         "terbayar": "0",
    //         "sisa": "208980"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD81627/X/22",
    //         "tgl_faktur": "2022-10-11",
    //         "jumlah": "1705000",
    //         "terbayar": "0",
    //         "sisa": "1705000"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD82286/X/22",
    //         "tgl_faktur": "2022-10-13",
    //         "jumlah": "3274830",
    //         "terbayar": "0",
    //         "sisa": "3274830"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD82584/X/22",
    //         "tgl_faktur": "2022-10-14",
    //         "jumlah": "2176875",
    //         "terbayar": "0",
    //         "sisa": "2176875"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD82586/X/22",
    //         "tgl_faktur": "2022-10-14",
    //         "jumlah": "1460430",
    //         "terbayar": "0",
    //         "sisa": "1460430"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD82957/X/22",
    //         "tgl_faktur": "2022-10-15",
    //         "jumlah": "2170800",
    //         "terbayar": "0",
    //         "sisa": "2170800"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD82965/X/22",
    //         "tgl_faktur": "2022-10-15",
    //         "jumlah": "1123470",
    //         "terbayar": "0",
    //         "sisa": "1123470"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD83341/X/22",
    //         "tgl_faktur": "2022-10-17",
    //         "jumlah": "2033100",
    //         "terbayar": "0",
    //         "sisa": "2033100"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD83343/X/22",
    //         "tgl_faktur": "2022-10-17",
    //         "jumlah": "81810",
    //         "terbayar": "0",
    //         "sisa": "81810"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD83786/X/22",
    //         "tgl_faktur": "2022-10-18",
    //         "jumlah": "2472120",
    //         "terbayar": "0",
    //         "sisa": "2472120"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD83787/X/22",
    //         "tgl_faktur": "2022-10-18",
    //         "jumlah": "859734",
    //         "terbayar": "0",
    //         "sisa": "859734"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD84380/X/22",
    //         "tgl_faktur": "2022-10-20",
    //         "jumlah": "79992000",
    //         "terbayar": "0",
    //         "sisa": "79992000"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD84755/X/22",
    //         "tgl_faktur": "2022-10-21",
    //         "jumlah": "960255",
    //         "terbayar": "0",
    //         "sisa": "960255"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD85246/X/22",
    //         "tgl_faktur": "2022-10-22",
    //         "jumlah": "550557",
    //         "terbayar": "0",
    //         "sisa": "550557"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD85925/X/22",
    //         "tgl_faktur": "2022-10-25",
    //         "jumlah": "1158300",
    //         "terbayar": "0",
    //         "sisa": "1158300"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD86440/X/22",
    //         "tgl_faktur": "2022-10-26",
    //         "jumlah": "1053405",
    //         "terbayar": "0",
    //         "sisa": "1053405"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD86622/X/22",
    //         "tgl_faktur": "2022-10-26",
    //         "jumlah": "842400",
    //         "terbayar": "0",
    //         "sisa": "842400"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD87240/X/22",
    //         "tgl_faktur": "2022-10-27",
    //         "jumlah": "1048383",
    //         "terbayar": "0",
    //         "sisa": "1048383"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD87567/X/22",
    //         "tgl_faktur": "2022-10-28",
    //         "jumlah": "987795",
    //         "terbayar": "0",
    //         "sisa": "987795"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD87569/X/22",
    //         "tgl_faktur": "2022-10-28",
    //         "jumlah": "1337067",
    //         "terbayar": "0",
    //         "sisa": "1337067"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD87938/X/22",
    //         "tgl_faktur": "2022-10-29",
    //         "jumlah": "85564800",
    //         "terbayar": "0",
    //         "sisa": "85564800"
    //     },
    //     {
    //         "kd_dealer": "LB1",
    //         "no_faktur": "PD88272/XI/22",
    //         "tgl_faktur": "2022-11-01",
    //         "jumlah": "844425",
    //         "terbayar": "0",
    //         "sisa": "844425"
    //     }
    // ];

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
                    data_khasbank = data.data;
                    if(data.data.length > 0){
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

                        $('#PenerimaanPembayaran').html(`
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        `);
                    } else {
                        $('#ListPembayaran').html(`
                            <tr>
                                <td colspan="10" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        `);

                        $('#PenerimaanPembayaran').html(`
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        `);
                    }
                }
            });

            hitungCeklist();
            
            $('#pilih_pembayaran').on('click', function () {
                data_akandibayar = [];
                let index = 0;
                $('#ListPembayaran tr').each(function (i, v) {
                    if ($(this).find('input[type="checkbox"]').is(':checked')) {
                        // memasukkan data yang di ceklist ke array
                        data_akandibayar[index] = {
                            no_faktur: $(this).find('td:nth-child(3)').text(),
                            tgl_faktur: $(this).find('td:nth-child(4)').text(),
                            jumlah: $(this).find('td:nth-child(2)').text(),
                            dealer: $(this).find('td:nth-child(8)').text(),
                        };
                        // end memasukkan data yang di ceklist ke array
                        index++;
                    }
                });

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
                                <td>${moment(v.tgl_faktur, 'YYYY-MM-DD').format('DD/MM/YYYY')}</td>
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
                            <td colspan="10" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    `);
                    // end jika data yang di ceklist kosong
                }
                $('#Modallistpembayaran').modal('hide');
            });
        } else {
            // $('#form_pp #no_kasbank').val('');
            // $('#form_pp #tgl').val('');
            $('#form_pp #kd_dealer').val('');
            $('#form_pp #nm_dealer').val('');
            $('#form_pp #total').val('');
            $('#form_pp #total').trigger('keyup');
            total();

            $('#ListPembayaran').html(`
                <tr>
                    <td colspan="8" class="text-center text-muted">Tidak ada data</td>
                </tr>
            `);

            $('#Modallistpembayaran #jml_faktur').val(0);
            $('.card-footer .row #jml_faktur').val(0);
            $('#total_bpk').val(0);
            $('#total_pemayaran').val(0);
            $('#sisa').val(0);
        }
    });

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
});