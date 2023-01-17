// dokumen ready
$(document).ready(function () {
    
    // pagination, card 
    $('#kt_project_users_card_pane > div.d-flex.flex-stack.flex-wrap.pt-10 > ul > li').on('click', function () {
        if ($(this).hasClass('disabled') === false) {
            gantiUrl($(this).find('a').data('page'));
        }
    });
    // end pagination
});