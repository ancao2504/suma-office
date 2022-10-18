let loading = {
    block: function () {
        $('#loading #loading-massage').html(`<div class="bg-white p-3 rounded d-flex align-items-center px-3">
            <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
            <span class="ms-2 fw-semibold">Loading...</span>
        </div>`);
        $('#loading').css('display', '');
    },
    release: function () {
        $('#loading #loading-massage').html('');
        $('#loading').css('display', 'none');
    }
};


