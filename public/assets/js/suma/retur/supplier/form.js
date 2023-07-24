function detail_clear(){
    $('#no_ps').val('');
    // $('#no_dus').val('');
    $('#no_klaim').val('');
    $('#no_klaim').removeClass('is-valid');
    $('#no_klaim').removeClass('is-invalid');
    $('#tgl_claim').val('');
    $('#kd_part').val('');
    $('#kd_part').removeClass('is-valid');
    $('#kd_part').removeClass('is-invalid');
    $('#nm_part').val('');
    $('#qty_klaim').val('');
    $('#no_produksi').val('');
    $('#kode_claim_kualitas').val('').trigger('change');
    $('#kode_claim_non_kualitas').val('').trigger('change');
    $('#kode_claim').val('');
    $('#ket').val('');
    $('#diterima').val('');
}

function simpan(tamp){
    if($('#kd_supp').val() != ''){
        loading.block();
        let ket = '';
        if ($('#kode_claim_kualitas').val() == 'I' || $('#kode_claim_non_kualitas').val() == 'Q'){
            ket = $('#ket').val();
        } else if ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == ''){
            ket = $('#kode_claim_kualitas').val();
        } else if ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != ''){
            ket = $('#kode_claim_non_kualitas').val();
        } 


        $.post(base_url + "/retur/supplier/form",
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                no_retur: ((tamp==true)?$('#no_retur').val():Math.floor(Math.random() * 1000000000) + 1),
                kd_supp: $('#kd_supp').val(),
                tgl_retur: $('#tgl_retur').val(),
                
                no_ps: $('#no_ps').val(),
                // no_dus: $('#no_dus').val(),
                no_klaim: $('#no_klaim').val(),
                tgl_claim: $('#tgl_claim').val(),
                kd_part: $('#kd_part').val(),
                qty_klaim: $('#qty_klaim').val(),
                no_produksi: $('#no_produksi').val(),
                ket: ket,
                diterima: $('#diterima').val(),
            },
            function (response) {
                console.log(response);
                if (response.status == '1') {
                    if(tamp == false){
                        swal.fire({
                            title: 'Perhatian!',
                            html: response.message + '<br>dengan No Retur : <b>' + response.data + '</b>',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                        return false;
                    }
                    $('#detail_modal').modal('hide');

                    let dta_edt = JSON.stringify({
                        no_ps : $('#no_ps').val(),
                        // no_dus : $('#no_dus').val(),
                        no_klaim : $('#no_klaim').val(),
                        tgl_claim : $('#tgl_claim').val(),
                        no_produksi : $('#no_produksi').val(),
                        kd_part : $('#kd_part').val(),
                        nm_part : $('#nm_part').val(),
                        ket : (
                            ($('#kode_claim_kualitas').val() == 'I' || $('#kode_claim_non_kualitas').val() == 'Q')? $('#ket').val() :

                            ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == '')? $('#kode_claim_kualitas option:selected').text().replace(/\([A-Z]\)/g, "") :

                            ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != '')? $('#kode_claim_non_kualitas option:selected').text().replace(/\([A-Z]\)/g, "") : ''
                        ),
                        jumlah : $('#qty_klaim').val(),
                        diterima : $('#diterima').val(),
                    });
                    let dta_del = JSON.stringify({
                        no_klaim: $('#no_klaim').val(),
                        kd_part: $('#kd_part').val()
                    });

                    if($('#no_klaim').val() != '' && $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').length == 0){

                        $('#list-retur .text_not_data').remove();

                        $('#list-retur').append(`
                            <tr class="fw-bolder fs-8 border" data-key="${($('#no_klaim').val()+$('#kd_part').val())}">
                                <td class="text-center">${$('#list-retur tr').length + 1}</td>
                                <td>${$('#no_klaim').val()}</td>
                                <td>${$('#tgl_claim').val().split('-').join('/')}</td>
                                <td>${$('#kd_part').val()}</td>
                                <td>${$('#nm_part').val()}</td>
                                <td class="text-end">${$('#qty_klaim').val()}</td>
                                <td>${
                                    ($('#kode_claim_kualitas').val() == 'I' || $('#kode_claim_non_kualitas').val() == 'Q')? $('#ket').val() :

                                    ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == '')? $('#kode_claim_kualitas option:selected').text().replace(/\([A-Z]\)/g, "") :

                                    ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != '')? $('#kode_claim_non_kualitas option:selected').text().replace(/\([A-Z]\)/g, "") : '-'
                                }</td>
                                <td class="text-center">
                                    <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a='${btoa(dta_edt)}' class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                                    <a role="button" data-a='${btoa(dta_del)}' class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                                </td>
                            </tr>
                        `);
                    } else {

                        $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(2)').text($('#no_klaim').val());
                        $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(3)').text($('#tgl_claim').val().split('-').join('/'));
                        $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(4)').text($('#kd_part').val());
                        $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(5)').text($('#nm_part').val());
                        $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(5)').text($('#qty_klaim').val());
                        $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(6)').text(
                            ($('#kode_claim_kualitas').val() == 'I' || $('#kode_claim_non_kualitas').val() == 'Q')? $('#ket').val() :

                            ($('#kode_claim_kualitas').val() != '' && $('#kode_claim_non_kualitas').val() == '')? $('#kode_claim_kualitas option:selected').text().replace(/\([A-Z]\)/g, "") :

                            ($('#kode_claim_kualitas').val() == '' && $('#kode_claim_non_kualitas').val() != '')? $('#kode_claim_non_kualitas option:selected').text().replace(/\([A-Z]\)/g, "") : '-'
                        );
                        $('#list-retur tr[data-key="'+($('#no_klaim').val()+$('#kd_part').val())+'"]').find('td:eq(7)').html(`
                            <a role="button" data-bs-toggle="modal" href="#detail_modal" data-a='${btoa(dta_edt)}' class="btn_dtl_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                            <a role="button" data-a='${btoa(dta_del)}' class="btn_dtl_delete btn-sm btn-icon btn-danger my-1" data-bs-toggle="modal" data-bs-target="#delet-retur"><i class="fas fa-trash text-white"></i></a>
                        `);
                    }

                    detail_clear();
                }
                if (response.status == '0') {
                    // if (Array.isArray(response.data) && response.data.length > 0) {
                    //     $('#warning_modal .modal-title').text(response.message);
                    //         let view = `
                    //         <div id="list_detail" class="table-responsive border rounded-3">
                    //         <table id="datatable_classporduk" class="table table-row-dashed table-row-gray-300 align-middle border">
                    //             <thead class="border">
                    //                 <tr class="fs-8 fw-bolder text-muted text-center">
                    //                     <th rowspan="2" class="w-auto ps-3 pe-3">No</th>
                    //                     <th rowspan="2" class="w-auto ps-3 pe-3">part Number</th>
                    //                     <th colspan="2" class="w-auto ps-3 pe-3">Qty</th>
                    //                     <th rowspan="2" class="w-auto ps-3 pe-3">Keterangan</th>
                    //                     <th rowspan="2" class="w-auto ps-3 pe-3">Action</th>
                    //                 </tr>
                    //                 <tr class="fs-8 fw-bolder text-muted text-center">
                    //                     <th class="w-auto ps-3 pe-3">Retur</th>
                    //                     <th class="w-auto ps-3 pe-3">Stock</th>
                    //                 </tr>
                    //             </thead>
                    //             <tbody id="list-retur">`
                    //     response.data.forEach(function (data, index) {
                    //             view += `
                    //                 <tr class="fw-bolder fs-8 border">
                    //                     <td class="text-center">${index + 1}</td>
                    //                     <td>${data.kd_part}</td>
                    //                     <td class="text-end">${data.qty}</td>
                    //                     <td class="text-end">${data.stock}</td>
                    //                     <td class="text-center"><span class="badge badge-light-danger">${data.keterangan}</span></td>
                    //                     <td class="text-center">
                    //                         <a role="button" data-key="${data.kd_part}" class="btn_warning_edit btn-sm btn-icon btn-warning my-1"><i class="fas fa-edit text-dark"></i></a>
                    //                     </td>
                    //                 </tr>
                    //             `;
                    //     });

                    //     view +=`</tbody>
                    //         </table>
                    //     </div>`;

                    //     $('#warning_modal .modal-body').html(view);
                    //     $('#detail_modal').modal('hide');
                    //     $('#warning_modal').modal('show');
                    // } else {
                        toastr.error(response.message, "Error");
                    // }
                }
                if (response.status == '2') {
                    swal.fire({
                        title: 'Perhatian!',
                        text: response.message,
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
        }).fail(function (err) {
            swal.fire({
                title: 'Perhatian!',
                text: 'Maaf Terjadi Kesalahan!',
                icon: 'warning',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-secondary'
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        });
        loading.release();
    } else {
        if ($('#kd_supp').val() == '') {
            toastr.error('Kode Supplier Tidak Boleh Kosong', "Error");
            $('#kd_supp').focus();
        } else if ($('#no_klaim').val() == '') {
            toastr.error('No Klaim Tidak Boleh Kosong', "Error");
            $('#no_klaim').focus();
        } else if ($('#kd_part').val() == '') {
            toastr.error('Kode Part Tidak Boleh Kosong', "Error");
            $('#kd_part').focus();
        }
    }
}

function edit_detail(val){
    $('#no_ps').val(val.no_ps);
    // $('#no_dus').val(val.no_dus);
    $('#no_klaim').val(val.no_klaim);
    $('#no_klaim').addClass('is-valid');
    $('#tgl_claim').val(val.tgl_claim);
    $('#kd_part').val(val.kd_part);
    $('#kd_part').addClass('is-valid');
    $('#nm_part').val(val.nm_part);
    $('#qty_klaim').val(val.jumlah);
    $('#no_produksi').val(val.no_produksi);
    $('#kode_claim_kualitas option[value="I').prop('selected', true).trigger('change');
    $('#ket').val(val.ket);
    $('#diterima').val(val.diterima);
}

$(document).ready(function () {
    $("#tgl_retur").flatpickr().setDate(moment($("#tgl_retur").val()).format('YYYY-MM-DD'));
    $('#kd_supp').val(old.kd_supp).trigger('change');

    $("#list_detail").on('click','.btn_dtl_edit', function () {
        let val = JSON.parse(atob($(this).data('a')));
        console.log(val);
        edit_detail(val);
    });

    $(".btn_simpan").click(function (e) {
        swal.fire({
            title: 'Perhatian!',
            html: 'Apakah Anda Yakin Ingin Menyimpan Data Ini, <b>Data yang diSimpan tidak bisa di Ubah </b>?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Tidak!',
            customClass: {
                confirmButton: 'btn btn-warning',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                simpan(false);
            }
        });
    });

    $(".btn_simpan_tmp").click(function (e) {
        e.preventDefault();
        simpan(true);
    });
    
    $("#list_detail").on('click','.btn_dtl_delete', function (e) {
        let val = JSON.parse(atob($(this).data('a')));
        Swal.fire({
            html: `
                <div class="text-start">
                    Apakah Anda Yakin Menghapus Detail Retur dengan
                </div>
                    <table class="table table-row-dashed table-row-gray-300 align-middle">
                        <tbody class="fw-bolder fs-8 border">
                            <tr>
                                <td>No Klaim</td>
                                <td>${val.no_klaim}</td>
                            </tr>
                            <tr>
                                <td>Kode Part</td>
                                <td>${val.kd_part}</td>
                            </tr>
                        </tbody>
                    </table>
            `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus!",
            cancelButtonText: "Batal!",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-secondary"
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                loading.block();
                $.post(base_url + '/retur/supplier/delete',
                    {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        no_retur: $('#no_retur').val(),
                        no_klaim: val.no_klaim,
                        kd_part: val.kd_part,
                    },
                    function (response) {
                        if (response.status == '1') {
                            $('#list-retur').find('tr[data-key="'+(val.no_klaim+val.kd_part)+'"]').remove();
                            if ($('#list-retur').find('tr').length == 0) {
                                $('#list-retur').html(`
                                    <tr class="fw-bolder fs-8 border text_not_data">
                                        <td colspan="13" class="text-center">Tidak ada data</td>
                                    </tr>
                                `);
                            } else {
                                $('#list-retur tr').each(function (i) {
                                    $(this).find('td:eq(0)').text(i + 1);
                                });
                            }
                        }
                        if (response.status == '0') {
                            toastr.error(response.message, "Error");
                        }
                        if (response.status == '2') {
                            swal.fire({
                                title: 'Perhatian!',
                                text: response.message,
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-secondary'
                                },
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        }
                }).always(function () {
                    loading.release();
                }).fail(function (err) {
                    swal.fire({
                        title: 'Perhatian!',
                        text: 'Terjadi Kesalahan, Silahkan Coba Lagi',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                });
            }
        });

    });
    
    $("#add_detail").on('click', function (e) {
        detail_clear();
    });

    $('#warning_modal').on('click','.btn_warning_edit', function () {
        let val = JSON.parse(atob($('#list_detail').find('tr[data-key="'+$(this).data('key')+'"] td a.btn_dtl_edit').data('a')));
        
        edit_detail(val);
        $('#warning_modal').modal('show');
        $('#detail_modal').modal('show');
    });



    $('#detail_modal').on('change','#kode_claim_kualitas', function () {
        $('#detail_modal  #kode_claim_non_kualitas option[value=""]').prop('selected', true);
    });
    $('#detail_modal').on('change','#kode_claim_non_kualitas', function () {
        $('#detail_modal  #kode_claim_kualitas option[value=""]').prop('selected', true);
    });

    $('#detail_modal').on('change','#kode_claim_kualitas, #kode_claim_non_kualitas', function () {
        if($(this).val() == 'I' || $(this).val() == 'Q'){
            $('#detail_modal  #ket').attr('hidden', false);
        }else{
            $('#detail_modal  #ket').attr('hidden', true);
        }
    });
});