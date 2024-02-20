function formatRibuan(angka) {
    angka = Number(angka.replace(/[^0-9]/g, ""));
    if (angka === 0) return "0";
    return angka.toLocaleString("id-ID");
}

let formInput = {
    values: function () {
        return {
            no_ps: $("#form_ps #no_ps").val().trim(),
            tgl_ps: $("#form_ps #tgl_ps").val(),
            kd_part: $("#form_ps #kd_part").val().trim(),
            qty: $("#form_ps #qty").val().replace(/\./g, ""),
            ket: $("#form_ps #ket").val(),
        };
    },
    clear: function () {
        // $("#form_ps #no_ps").val("");
        $("#form_ps #tgl_ps").removeClass("is-invalid");
        // $("#form_ps #tgl_ps")
        //     .flatpickr()
        //     .setDate(moment().format("YYYY-MM-DD"));
        $("#form_ps #kd_part").val("");
        $("#form_ps #kd_part").removeClass("is-invalid");
        $("#form_ps #nm_part").val("");
        $("#form_ps #qty").val("");
        $("#form_ps #qty").removeClass("is-invalid");
        $("#form_ps #ket").val("");
    },
    validation: function () {
        // const data = formInput.values();
        const data = this.values();
        let isvalid = true;

        if (data.no_ps == "") {
            isvalid = false;
            $("#form_ps #no_ps").addClass("is-invalid");
            $("#form_ps #error_no_ps").text("No Packing Sheet Harus diisi");
        }

        if (data.tgl_ps == "") {
            isvalid = false;
            $("#form_ps #tgl_ps").addClass("is-invalid");
            $("#form_ps #error_tgl_ps").text("Tgl Packing Sheet Harus diisi");
        }

        if (data.kd_part == "") {
            isvalid = false;
            $("#form_ps #kd_part").addClass("is-invalid");
            $("#form_ps #error_kd_part").text("Kode Part Harus diisi");
        }

        if (data.qty == "") {
            isvalid = false;
            $("#form_ps #qty").addClass("is-invalid");
            $("#form_ps #error_qty").text("Jumlah Harus diisi");
        } else {
            if (!/^[0-9]+$/.test(data.qty)) {
                isvalid = false;
                $("#form_ps #qty").addClass("is-invalid");
                $("#form_ps #error_qty").text("Jumlah harus angka");
            }
        }

        if (isvalid) {
            $("#form_ps #error_no_ps").text("");
            $("#form_ps #error_tgl_ps").text("");
            $("#form_ps #error_kd_part").text("");
            $("#form_ps #error_qty").text("");

            $("#form_ps #no_ps").removeClass("is-invalid");
            $("#form_ps #tgl_ps").removeClass("is-invalid");
            $("#form_ps #kd_part").removeClass("is-invalid");
            $("#form_ps #qty").removeClass("is-invalid");
        }

        return isvalid;
    },
    autocomplete: {
        kd_part: async function (kd_part) {
            const respon = await service.get({
                link: base_url + "/part",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    option: ["first"],
                    kd_part: kd_part,
                },
            });

            if (respon) {
                $("#form_ps #kd_part").val(respon.kd_part);
                $("#form_ps #nm_part").val(respon.nm_part);

                $("#form_ps #kd_part").removeClass("is-invalid");
                $("#form_ps #error_kd_part").text("");
            } else {
                $("#form_ps #kd_part").val("");
                $("#form_ps #kd_part").addClass("is-invalid");
                $("#form_ps #error_kd_part").text("Kode Part tidak ditemukan");
                $("#form_ps #nm_part").val("");
            }
        },
    },
    limit: {
        ps: 0,
    },
};

let master = {
    data: [],
    list: async function (data = { type: "server", param: {} }) {
        if (data.type == "server") {
            this.data = await service.get({
                link: window.location.href,
                data: { ...data.param },
            });
        }

        const target = $("#daftarReturSupplier");
        if (
            this.data == null ||
            this.data == undefined ||
            this.data?.length <= 0 ||
            Object.keys(this.data[0]).length <= 0
        ) {
            target.html(`<tr class="fw-bolder fs-8 border text_not_data">
                <td colspan="99" class="text-center">Tidak ada data</td>
            </tr>`);

            return;
        }

        target.empty();
        $.each(this.data, function (index, item) {
            const count = item.jawab.filter(
                (jawab) => jawab.sts_end == 0
            ).length;
            target.append(`
                <tr class="fw-bolder fs-8 border">
                    <td class="text-center">${index + 1}</td>
                    <td>${item.no_ps}</td>
                    <td>${
                        item.tgl_ps ? item.tgl_ps.replace(/-/g, "/") : "-"
                    }</td>
                    <td>${item.kd_part}</td>
                    <td>${item.nm_part}</td>
                    <td class="text-end">${item.qty_jwb}</td>
                    <td class="text-end">${item.qty_terpakai}</td>
                    <td class="${
                        item.ket == null ? "text-center" : "text-start"
                    } text-be">${item.ket ?? "-"}</td>
                    <td class="text-center">${
                        item.list_no_retur.length > 0
                            ? item.list_no_retur.join("<br>")
                            : "-"
                    }</td>
                    <td class="text-center">${
                        item.list_no_klaim.length > 0
                            ? item.list_no_klaim.join("<br>")
                            : "-"
                    }</td>
                    <td class="text-center">${
                        item.list_kd_dealer.length > 0
                            ? item.list_kd_dealer.join("<br>")
                            : "-"
                    }</td>
                    <td class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#add_modal_retur" class="position-relative btn-sm btn btn-success text-white" onclick='master.listDetail(${JSON.stringify(
                            {
                                no_ps: item.no_ps,
                                tgl_ps: item.tgl_ps,
                                kd_part: item.kd_part,
                                nm_part: item.nm_part,
                                qty_jwb: item.qty_jwb,
                                jawab: item.jawab,
                            }
                        )})'><i class="bi bi-plus-circle"></i>
                        ${
                            count > 0
                                ? `<span class="position-absolute top-0 start-0 translate-middle  badge badge-circle badge-warning">${count}</span>`
                                : ""
                        }

                        </button>
                        ${
                            item.qty_terpakai > 0
                                ? ""
                                : `<button type="button" class="btn-sm btn btn-danger text-white" onclick='master.hapus(${JSON.stringify(
                                      {
                                          no_ps: item.no_ps,
                                          tgl_ps: item.tgl_ps,
                                          kd_part: item.kd_part,
                                          nm_part: item.nm_part,
                                          qty_jwb: item.qty_jwb,
                                      }
                                  )})'><i class="bi bi-trash"></i></button>`
                        }
                    </td>
                </tr>
            `);
        });
    },
    listDetail: async function (data) {
        $("#add_modal_retur #jwb_no_ps").text(data.no_ps);
        $("#add_modal_retur #jwb_kd_part").text(data.kd_part);
        $("#add_modal_retur #jwb_nm_part").text("(" + data.nm_part + ")");

        formInputDetail.modal.kd_ps = data.no_ps;
        formInputDetail.modal.tgl_ps = data.tgl_ps;
        formInputDetail.modal.kd_part = data.kd_part;
        formInputDetail.modal.nm_part = data.nm_part;
        formInputDetail.modal.qty = data.qty_jwb;

        formInputDetail.clear();

        const target = $("#list-jwb");
        target.empty();

        if (
            data.jawab == null ||
            data.jawab == undefined ||
            data.jawab.length <= 0 ||
            Object.keys(data.jawab).length <= 0
        ) {
            target.html(`<tr class="fw-bolder fs-8 border text_not_data">
                <td colspan="99" class="text-center">Tidak ada data</td>
            </tr>`);
            return;
        }

        let tgl_tamp = null;
        $.each(data.jawab, function (index, item) {
            target.append(`
                <tr class="fw-bolder fs-8 border">
                    <td class="text-center">${
                        moment(
                            item.tgl_jwb,
                            "DD-MM-YYYY HH:mm:ss:SSS"
                        ).format("DD MMMM YYYY HH:mm")
                        // !tgl_tamp ||
                        // !moment(tgl_tamp).isSame(
                        //     moment(item.tgl_jwb, "DD-MM-YYYY HH:mm:ss:SSS"),
                        //     "day"
                        // )
                        //     ? moment(
                        //           item.tgl_jwb,
                        //           "DD-MM-YYYY HH:mm:ss:SSS"
                        //       ).format("DD MMMM YYYY HH:mm")
                        //     : moment(
                        //           item.tgl_jwb,
                        //           "DD-MM-YYYY HH:mm:ss:SSS"
                        //       ).format("HH:mm")
                    }</td>
                    <td class="text-center">${item.no_retur}</td>
                    <td class="text-center">${item.no_klaim}</td>
                    <td class="text-end">${item.qty_jwb}</td>
                    <td class="text-center">${
                        item.alasan == "RETUR" ? "Ganti Barang" : "Ganti Uang"
                    }</td>
                    <td class="text-end">${
                        item.ca ? formatRibuan(item.ca) : "-"
                    }</td>
                    <td class="text-start">${item.keputusan ?? "-"}</td>
                    <td>${item.ket ?? "-"}</td>
                    <td class="text-center">
                    ${
                        item.sts_end == 1
                            ? "-"
                            : `<button type="button" class="btn-sm btn btn-danger text-white" onclick='Detail.hapus(${JSON.stringify(
                                  {
                                      no_jwb: item.no_jwb,
                                      no_ps: data.no_ps,
                                      tgl_ps: data.tgl_ps,
                                      kd_part: data.kd_part,
                                      qty_jwb: item.qty_jwb,
                                      no_retur: item.no_retur,
                                      no_klaim: item.no_klaim,
                                  }
                              )})'><i class="bi bi-trash"></i></button>`
                    }</td>
                </tr>
            `);

            tgl_tamp = moment(item.tgl_jwb, "DD-MM-YYYY HH:mm:ss:SSS");
        });
    },
    filter: {
        values: function () {
            return {
                search: {
                    value: $("#filter_table #search_input").val(),
                    field: $("#filter_table #select_search").val(),
                },
                tanggal: $("#filter_table #tgl_input").val().split(" to "),
            };
        },
    },
    simpan: async function () {
        swal.fire({
            title: "Apakah anda yakin menyimpan data ini ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Simpan!",
            cancelButtonText: "Batal",
        }).then(async (result) => {
            if (result.isConfirmed) {
                const respon = await service.post({
                    link: window.location.href + "/store",
                    param: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                if (respon) {
                    if (respon.hasOwnProperty("no_retur")) {
                        swal.fire({
                            title: "Warning!",
                            html: `<div class="d-inline-flex">
                                        <b>No retur :</b>
                                    </div>
                                    <div class="d-inline-flex">
                                        <span class="text-danger fw-bold">
                                            ${respon.no_retur.join("<br>")}
                                        </span>
                                    </div>
                                    <div class="d-block">
                                        Nomer Retur Toko di atas tidak ditemukan
                                    </div>`,
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                        });
                    }
                    if (respon.hasOwnProperty("no_ca")) {
                        swal.fire({
                            title: "Warning!",
                            html: `<div class="d-inline-flex">
                                        <b>No retur :</b>
                                    </div>
                                    <div class="d-inline-flex">
                                        <span class="text-danger fw-bold">
                                            ${respon.no_ca.join("<br>")}
                                        </span>
                                    </div>
                                    <div class="d-block">
                                        Nomer Retur Supplier di atas tidak ditemukan
                                    </div>`,
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                        });
                    }
                    if (respon.hasOwnProperty("no_ps")) {
                        swal.fire({
                            title: "Warning!",
                            html: `<div class="d-inline-flex">
                                        <b>No PS :</b>
                                    </div>
                                    <div class="d-inline-flex">
                                        <span class="text-danger fw-bold">
                                            ${respon.no_ps.join("<br>")}
                                        </span>
                                    </div>
                                    <div class="d-block">
                                        Nomer Retur Supplier di atas tidak ditemukan
                                    </div>`,
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                        });
                    }

                    swal.fire({
                        title: "Berhasil!",
                        text: "Data Berhasil",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1000,
                    });

                    master.list();
                }
            }
        });
    },
    tambah: async function () {
        loading.block();
        if (!formInput.validation()) {
            loading.release();
            return;
        }

        const respon = await service.post({
            link: window.location.href,
            param: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            data: formInput.values(),
        });

        if (respon) {
            swal.fire({
                title: "Berhasil!",
                text: "Data Berhasil",
                icon: "success",
                showConfirmButton: false,
                timer: 1000,
            });

            formInput.clear();
            master.list();
        }

        loading.release();
    },
    hapus: function (request) {
        loading.block();
        swal.fire({
            title: "Apakah anda yakin?",
            html:
                "Hapus Part Number : <b>" +
                request.kd_part +
                "</b> (" +
                request.nm_part +
                ") Pada No PS : <b>" +
                request.no_ps +
                "</b> ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then(async (result) => {
            if (result.isConfirmed) {
                const respon = await service.post({
                    link: window.location.href + "/delete",
                    param: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: request,
                });
                if (respon) {
                    swal.fire({
                        title: "Berhasil!",
                        text: "Data Berhasil dihapus",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1000,
                    });

                    master.data.forEach((item, index) => {
                        if (
                            item.no_ps == request.no_ps &&
                            item.tgl_ps == request.tgl_ps &&
                            item.kd_part == request.kd_part &&
                            item.qty_jwb == request.qty_jwb
                        ) {
                            master.data.splice(index, 1);
                        }
                    });
                    master.list({ type: "local" });
                }
            }
        });
        loading.release();
    },
};

$(document).ready(async function () {
    $("#form_ps #tgl_ps")
        .flatpickr()
        .setDate(moment($("#tgl_ps").val()).format("YYYY-MM-DD"));

    flatpickr("#filter_table #tgl_input", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [
            moment().startOf("month").toDate(),
            moment().endOf("month").toDate(),
        ],
    });

    master.list();

    $("#filter_table #btn_search").on("click", function () {
        const data = master.filter.values();
        if (data.search.field == "") {
            return;
        }

        master.list({ type: "server", param: data });
    });

    $("#filter_table #search_input").on("search", function () {
        $(this)
            .next()
            .find(".clear-icon")
            .addClass("bg-secondary text-secondary");
        const data = master.filter.values();
        master.list({ type: "server", param: data });
    });

    let a = 0;
    $("#filter_table #tgl_input").on("change", function () {
        a++;
        if (a == 2) {
            master.list({ type: "server", param: master.filter.values() });
            a = 0;
        }
    });

    $("#form_ps #tambah_ps").on("click", async function () {
        master.tambah();
    });

    $("#simpan_jwb").on("click", async function () {
        master.simpan();
    });

    $("#form_ps #kd_part").on("change", function () {
        if ($(this).val().length < 3) {
            return;
        }

        formInput.autocomplete.kd_part($(this).val());
    });
});
