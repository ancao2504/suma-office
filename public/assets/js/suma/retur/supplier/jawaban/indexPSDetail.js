let formInputDetail = {
    values: function () {
        return {
            no_retur: $("#add_modal_retur #no_retur").val().trim(),
            no_ca: $("#add_modal_retur #no_ca").val().trim(),
            jml: $("#add_modal_retur #jml").val().replace(/\./g, ""),
            alasan: $("#add_modal_retur #alasan").val().trim(),
            ca: $("#add_modal_retur #ca").val().replace(/\./g, ""),
            keputusan: $("#add_modal_retur #keputusan").val(),
            ket: $("#add_modal_retur #ket").val(),
        };
    },
    clear: function () {
        $("#add_modal_retur #no_retur").val("");
        $("#add_modal_retur #no_retur").removeClass("is-invalid");
        $("#add_modal_retur #no_ca").val("");
        $("#add_modal_retur #jml").val("");
        $("#add_modal_retur #jml").removeClass("is-invalid");
        $("#add_modal_retur #ca").val("");
        $("#add_modal_retur #ca").removeClass("is-invalid");
        // $("#add_modal_retur #ket").val("");
        // $("#add_modal_retur #ket").removeClass("is-invalid");
        $("#add_modal_retur #alasan").val("RETUR");
        $("#add_modal_retur #alasan").removeClass("is-invalid");
        $("#add_modal_retur #keputusan").val("TERIMA");
        $("#add_modal_retur #keputusan").removeClass("is-invalid");


    },
    validation: function () {
        const data = this.values();
        let isvalid = true;

        if (data.no_retur == "") {
            isvalid = false;
            $("#add_modal_retur #no_retur").addClass("is-invalid");
            $("#add_modal_retur #error_no_retur").text("No Retur Harus diisi");
        }

        if (data.jml == "") {
            isvalid = false;
            $("#add_modal_retur #jml").addClass("is-invalid");
            $("#add_modal_retur #error_jml").text("Jumlah Harus diisi");
        } else {
            if (!/^[0-9]+$/.test(data.jml)) {
                isvalid = false;
                $("#add_modal_retur #jml").addClass("is-invalid");
                $("#add_modal_retur #error_jml").text("Jumlah harus angka");
            }

            if (parseInt(data.jml) < 1) {
                isvalid = false;
                $("#add_modal_retur #jml").addClass("is-invalid");
                $("#add_modal_retur #error_jml").text("Jumlah minimal 1 item");
            }

            if (parseInt(data.jml) > parseInt(this.limit.rtoko)) {
                isvalid = false;
                $("#add_modal_retur #jml").addClass("is-invalid");
                $("#add_modal_retur #error_jml").text(
                    "Jumlah Ganti Melebihi Jumlah part yang di Retur"
                );
            }

            if (parseInt(data.jml) > parseInt(this.modal.qty)) {
                isvalid = false;
                $("#add_modal_retur #jml").addClass("is-invalid");
                $("#add_modal_retur #error_jml").text(
                    "Jumlah Ganti Melebihi Jumlah barang Packing Sheet"
                );
            }
        }
        if (data.alasan == "") {
            isvalid = false;
            $("#add_modal_retur #alasan").addClass("is-invalid");
            $("#add_modal_retur #error_alasan").text("alasan Harus diisi");
        } else if (data.alasan == "CA") {
            if (data.ca == "") {
                isvalid = false;
                $("#add_modal_retur #ca").addClass("is-invalid");
                $("#add_modal_retur #error_ca").text("Jumlah Uang Harus diisi");
            }
        }

        if (data.keputusan == "") {
            isvalid = false;
            $("#add_modal_retur #keputusan").addClass("is-invalid");
            $("#add_modal_retur #error_keputusan").text("ca Harus diisi");
        }

        // if (data.ket == "") {
        //     isvalid = false;
        //     $("#add_modal_retur #ket").addClass("is-invalid");
        //     $("#add_modal_retur #error_ket").text("keterangan Harus diisi");
        // }

        if (isvalid) {
            $("#add_modal_retur #error_no_retur").text("");
            $("#add_modal_retur #error_jml").text("");
            $("#add_modal_retur #error_alasan").text("");
            $("#add_modal_retur #error_ca").text("");
            $("#add_modal_retur #error_keputusan").text("");

            $("#add_modal_retur #no_retur").removeClass("is-invalid");
            $("#add_modal_retur #jml").removeClass("is-invalid");
            $("#add_modal_retur #alasan").removeClass("is-invalid");
            $("#add_modal_retur #ca").removeClass("is-invalid");
            $("#add_modal_retur #keputusan").removeClass("is-invalid");
        }

        return isvalid;
    },
    autoComplete: {
        rtoko: async function (requst) {
            const respon = await service.get({
                link: base_url + "/Rtoko",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    option: requst.option,
                    no_retur: requst.no_retur,
                    kd_part: requst.kd_part,
                    page: requst.page,
                    per_page: requst.per_page,
                },
            });
            if (!respon) {
                $("#add_modal_retur #no_ca").val("");
                return;
            }

            if (requst.option[0] == "first") {
                if (jQuery.isEmptyObject(respon)) {
                    $("#add_modal_retur #no_retur").val("");
                    $("#add_modal_retur #no_retur").addClass("is-invalid");
                } else {
                    $("#add_modal_retur #no_retur").val(respon.no_klaim);
                    $("#add_modal_retur #no_retur").removeClass("is-invalid");

                    $("#add_modal_retur #no_ca").val(respon.no_retur);
                    formInputDetail.limit.rtoko = respon.jml_rtoko;
                }
            } else if (requst.option[0] == "page") {
                if (jQuery.isEmptyObject(respon)) {
                    $("#klaim-list").find("tbody").html(`
                    <tr>
                        <td colspan="99" class="text-center text-primary">
                            <div class="text-center">
                                <h5>Data Tidak Ditemukan!</h5>
                            </div>
                        </td>
                    </tr>`);
                }
                $("#klaim-list").html(respon);
                $("#klaim-list").modal("show");
            }
        },
    },
    limit: {
        rtoko: 0,
    },
    modal: {
        kd_ps: "",
        tgl_ps: "",
        kd_part: "",
        nm_part: "",
        qty: 0,
    },
};

let Detail = {
    tambah: async function () {
        loading.block();
        if (!formInputDetail.validation()) {
            loading.release();
            return;
        }

        return false;

        const respon = await service.post({
            link: window.location.href + "/detail",
            param: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                ...formInputDetail.values(),
                kd_ps: formInputDetail.modal.kd_ps,
                tgl_ps: formInputDetail.modal.tgl_ps,
                jml_ps: formInputDetail.modal.qty,
                kd_part: formInputDetail.modal.kd_part,
            },
        });

        if (respon) {
            swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Data Berhasil di simpan",
                showConfirmButton: false,
                timer: 1000,
            });

            await master.list();

            const data_detail = master.data.filter(
                (item) =>
                    item.no_ps == formInputDetail.modal.kd_ps &&
                    item.tgl_ps == formInputDetail.modal.tgl_ps &&
                    item.kd_part == formInputDetail.modal.kd_part &&
                    item.qty_jwb == formInputDetail.modal.qty
            );

            if (data_detail.length <= 0) {
                $("#add_modal_retur").modal("hide");
                return;
            }

            await master.listDetail(data_detail[0]);
        }

        loading.release();
    },
    hapus: async function (request) {
        loading.block();

        const respon = await service.post({
            link: window.location.href + "/detail/delete",
            param: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            data: request,
        });

        if (respon) {
            master.data.forEach((item) => {
                if (
                    item.no_ps == request.no_ps &&
                    item.tgl_ps == request.tgl_ps &&
                    item.kd_part == request.kd_part
                ) {
                    item.jawab.forEach((value, key) => {
                        if (
                            value.no_retur == request.no_retur &&
                            value.no_klaim == request.no_klaim &&
                            value.qty_jwb == request.qty_jwb
                        ) {
                            item.qty_terpakai -= value.qty_jwb;
                            item.jawab.splice(key, 1);

                            if (
                                item.jawab.filter(
                                    (value) =>
                                        value.no_retur == request.no_retur
                                ).length == 0
                            ) {
                                item.list_no_retur = item.list_no_retur.filter(
                                    (value) => value != request.no_retur
                                );
                            }

                            if (
                                item.jawab.filter(
                                    (value) =>
                                        value.no_klaim == request.no_klaim
                                ).length == 0
                            ) {
                                item.list_no_klaim = item.list_no_klaim.filter(
                                    (value) => value != request.no_klaim
                                );
                            }
                        }
                    });
                }
            });

            master.list();
            master.listDetail(
                master.data.filter(
                    (item) =>
                        item.no_ps == request.no_ps &&
                        item.tgl_ps == request.tgl_ps &&
                        item.kd_part == request.kd_part
                )[0]
            );
        }

        loading.release();
    },
};

$(document).ready(function () {
    $("#add_modal_retur #alasan").on("change", function (e) {
        if ($(this).val() == "CA") {
            $("#add_modal_retur #ca").attr("disabled", false);
        } else if ($(this).val() == "RETUR") {
            $("#add_modal_retur #ca").attr("disabled", true);
        }
    });

    $("#add_modal_retur #simpan_ps_detail").click(async function () {
        Detail.tambah();
    });

    $("#klaim-list").on("click", "#btn_cari", function () {
        formInputDetail.autoComplete.rtoko({
            option: ["page"],
            no_retur: $("#klaim-list").find("#cari").val(),
            kd_part: formInputDetail.modal.kd_part,
            page: 1,
            per_page: $("#klaim-list").find("#per_page").val(),
        });
    });

    $("#klaim-list").on("click", ".pagination .page-item", function () {
        formInputDetail.autoComplete.rtoko({
            option: ["page"],
            kd_part: formInputDetail.modal.kd_part,
            no_retur: $("#klaim-list").find("#cari").val(),
            page: $(this).find("a").attr("href").split("page=")[1],
            per_page: $("#klaim-list").find("#per_page").val(),
        });
    });

    $("#klaim-list").on("change", "#per_page", function () {
        formInputDetail.autoComplete.rtoko({
            option: ["page"],
            no_retur: $("#klaim-list").find("#cari").val(),
            kd_part: formInputDetail.modal.kd_part,
            page: 1,
            per_page: $(this).val(),
        });
    });

    $(".list-klaim").on("click", function () {
        formInputDetail.autoComplete.rtoko({
            option: ["page"],
            kd_part: formInputDetail.modal.kd_part,
            page: 1,
            per_page: 10,
        });
    });

    $("#add_modal_retur #no_retur").on("change", function () {
        formInputDetail.autoComplete.rtoko({
            option: ["first"],
            no_retur: $(this).val(),
            kd_part: formInputDetail.modal.kd_part,
            page: 1,
            per_page: 10,
        });
    });

    $("#klaim-list").on("click", ".pilih", function () {
        const data = JSON.parse(atob($(this).data("a")));
        $("#add_modal_retur #no_retur").val(data.no_klaim);
        $("#add_modal_retur #no_retur").removeClass("is-invalid");
        $("#add_modal_retur #no_ca").val(data.no_retur);
        formInputDetail.limit.rtoko = data.jml_rtoko;

        $("#klaim-list").modal("hide");
    });
});
