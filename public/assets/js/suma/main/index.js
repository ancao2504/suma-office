function enter_input() {
    // menganti enter ke tab jika dia input, select, textarea
    $('body').find("input, select, textarea").on('keypress',function(e) {
        if (e.key === 'Enter') {
            var inputs = $('body').find("input, select, textarea").filter(':visible:not([disabled]):not([readonly])');
            inputs[inputs.index(this) + 1].focus();
            e.preventDefault();
        }
    });
}

$(document).ready(function() {
    $('body').on('click', '.menu-item a', function(e) {
        loading.block();
    });

    $(window).on('offline', function(e) {
        Swal.fire({
            title: "Anda sedang offline",
            text: "Silakan periksa koneksi internet Anda.",
            icon: "warning",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false
        });
    });

    $(window).on('online', function(e) {
        Swal.close();
    });

    enter_input();

    
    // let href = '';
    // $(document).on('contextmenu', function (e) {
    //     e.preventDefault();
    //     e.stopPropagation();
    //     href = ($(e.target).hasClass('menu-title') && $(e.target).parent('a').hasClass('menu-link'))?$(e.target).parent('a').attr('href'):'';
        
    //     ($('body #menu_klik_Kanan').length > 0)?$('body #menu_klik_Kanan').remove():'';

    //     $('body').append(`
    //         <div class="card shadow-sm" id="menu_klik_Kanan" style="width: 18rem; display: block; position: absolute; left: ${e.pageX}px; top: ${e.pageY}px; z-index: 9999;">
    //             <ul class="list-group list-group-flush m-3">
    //                 ${
    //                     (href != '')?`<li class="list-group-item" id="tab" style="cursor: pointer;"> <i class="bi bi-box-arrow-up-right me-3"></i>Open in new tab</li>`:''
    //                 }
    //                 <li class="list-group-item" id="reload" style="cursor: pointer;"><i class="bi bi-arrow-clockwise me-3"></i>Reload</li>
    //                 <li class="list-group-item" id="copy" style="cursor: pointer;"><i class="bi bi-clipboard me-3"></i>Copy</li>
    //                 <li class="list-group-item" id="paste" style="cursor: pointer;"><i class="bi bi-clipboard me-3"></i>Paste</li>
    //                 <li class="list-group-item" id="print" style="cursor: pointer;"><i class="bi bi-printer me-3"></i>Print</li>
    //             </ul>
    //         </div>
    //     `);
    // });
    // $(document).on('click','body #menu_klik_Kanan #tab', function () {
    //     window.open(href, '_blank');
    // });
    // $(document).on('click','body #menu_klik_Kanan #print', function () {
    //     window.print();
    // });
    // $(document).on('click','body #menu_klik_Kanan #reload', function () {
    //     location.reload();
    // });
    // $(document).on('click','body #menu_klik_Kanan #copy', function (e) {
    //     e.stopPropagation();
    //     var selectedText = window.getSelection().toString(); // Mendapatkan teks yang ingin disalin

    //     if (selectedText) {
    //         // Menggunakan Clipboard API untuk menyalin teks ke clipboard
    //         navigator.clipboard.writeText(selectedText).then(function () {
    //             console.log("Teks telah disalin ke clipboard.");
    //         }).catch(function (error) {
    //             console.error("Gagal menyalin teks: " + error);
    //         });
    //     }
    // });
    // $(document).on('click','body #menu_klik_Kanan #paste', function () {
    // });
    // document.addEventListener('click', function () {
    //     $('body #menu_klik_Kanan').css('display', 'none');
    // });
});
