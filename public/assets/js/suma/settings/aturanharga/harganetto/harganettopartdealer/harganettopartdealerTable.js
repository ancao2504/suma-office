// dokumen ready
$(document).ready(function () {
    
    // per_page
    $('#kt_project_users_table_length > label > select > option[value="' + per_page + '"]').prop('selected', true);

    $('#kt_project_users_table_length > label > select').on('change', function () {
        gantiUrl(1);
    });
    // end per_page

    
    // pagination
    $('#kt_project_users_table_paginate > ul > li').on('click', function () {
        if ($(this).hasClass('disabled') === false) {
            gantiUrl($(this).find('a').data('page'));
        }
    });
    // end pagination
});