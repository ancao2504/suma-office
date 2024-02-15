const service = {
    async post(params) {
        loading.block();
        const data = await $.post({
            url:
                params.link +
                "?" +
                (params.param
                    ? Object.entries(params.param)
                          .map(([key, val]) => `${key}=${val}`)
                          .join("&")
                    : ""),
            type: "POST",
            data: params.data || null,
            dataType: "JSON",
        })
            .done(function (data, textStatus, jqXHR) {
                const code = jqXHR.status.toString()[0];

                if (code != "2") {
                    try {
                        swal.fire({
                            title: "Perhatian!",
                            html: response.message + "<br>[" + code + "]",
                            icon: "warning",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    } catch (error) {
                        swal.fire({
                            title: "Error!",
                            html:
                                "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi" +
                                "<br>[" +
                                code +
                                "]",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    }
                }

                if (data.status != "1") {
                    swal.fire({
                        title: "Perhatian!",
                        html: data.message,
                        icon: "warning",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-secondary",
                        },
                        allowOutsideClick: false,
                    });
                }

                loading.release();
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                const code = jqXHR.status.toString()[0];

                if (code == "3" || code == "4") {
                    try {
                        swal.fire({
                            title: "Perhatian!",
                            html: response.message + "<br>[" + code + "]",
                            icon: "warning",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    } catch (error) {
                        swal.fire({
                            title: "Error!",
                            html:
                                "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi" +
                                "<br>[" +
                                code +
                                "]",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    }
                }

                if (code == "5") {
                    swal.fire({
                        title: "Error!",
                        html:
                            "Gagal Terhubung ke server, silahkan coba beberapa saat lagi" +
                            "<br>[" +
                            code +
                            "]",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-secondary",
                        },
                        allowOutsideClick: false,
                    });
                }

                loading.release();
            });

        loading.release();
        return data.data;
    },
    async get(params) {
        loading.block();
        const data = await $.get({
            url:
                params.link +
                "?" +
                (params.param
                    ? Object.entries(params.param)
                          .map(([key, val]) => `${key}=${val}`)
                          .join("&")
                    : ""),
            type: "GET",
            data: params.data || null,
            dataType: "JSON",
        })
            .done(function (data, textStatus, jqXHR) {
                const code = jqXHR.status.toString()[0];

                if (code != "2") {
                    try {
                        swal.fire({
                            title: "Perhatian!",
                            html: response.message + "<br>[" + code + "]",
                            icon: "warning",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    } catch (error) {
                        swal.fire({
                            title: "Error!",
                            html:
                                "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi" +
                                "<br>[" +
                                code +
                                "]",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    }
                }

                if (data.status != "1") {
                    swal.fire({
                        title: "Perhatian!",
                        html: data.message,
                        icon: "warning",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-secondary",
                        },
                        allowOutsideClick: false,
                    });
                }

                loading.release();
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                const code = jqXHR.status.toString()[0];

                if (code == "3" || code == "4") {
                    try {
                        swal.fire({
                            title: "Perhatian!",
                            html: response.message + "<br>[" + code + "]",
                            icon: "warning",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    } catch (error) {
                        swal.fire({
                            title: "Error!",
                            html:
                                "Maaf terjadi kesalahan, silahkan coba beberapa saat lagi" +
                                "<br>[" +
                                code +
                                "]",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-secondary",
                            },
                            allowOutsideClick: false,
                        });
                    }
                }

                if (code == "5") {
                    swal.fire({
                        title: "Error!",
                        html:
                            "Gagal Terhubung ke server, silahkan coba beberapa saat lagi" +
                            "<br>[" +
                            code +
                            "]",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-secondary",
                        },
                        allowOutsideClick: false,
                    });
                }

                loading.release();
            });

        loading.release();
        return data.data;
    },
};
