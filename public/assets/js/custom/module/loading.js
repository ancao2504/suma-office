let loading = {
    block: function () {
        $('#loading #loading-massage').html(`
            <div class="bg-white py-3 px-5 rounded d-flex align-items-center">
                <span class="spinner-border text-primary" role="status" aria-hidden="true"></span>
                <span class="ms-2 fw-semibold">Loading...</span>
            </div>
        `);
        $('#loading').css('display', '');
    },
    release: function () {
        $('#loading #loading-massage').html('');
        $('#loading').css('display', 'none');
    }
};


