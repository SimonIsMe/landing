$(document).ready(function(){
    $('.table-name').bind('click', function(){
        var details = $(this).parent().data('details');
        $(this).parent().parent().find('.active').removeClass('active');
        $(this).parent().addClass('active');
        activeLine();
        $('.db-details').hide();
        $('#' + details).show();
    });

    $('.table-column').bind('click', function(){
        var details = $(this).data('details');
        $(this).parent().parent().find('.active').removeClass('active');
        $(this).addClass('active');
        activeLine();
        $('.db-details').hide();
        $('#' + details).show();
    })
});

function activeLine()
{
    if ($('div[data-details="db-details-comments-user-id"]').hasClass('active')) {
        $('#users-comments-id-top-right').addClass('active');
        $('#users-comments-id-bottom').addClass('active');
    }
    if ($('div[data-details="db-details-comments-article-id"]').hasClass('active')) {
        $('#articles-comments-id-top-right').addClass('active');
        $('#articles-comments-id-bottom').addClass('active');
    }
}