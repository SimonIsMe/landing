$(document).ready(function(){
    $('.table-name').bind('click', function(){
        var details = $(this).parent().data('details');
        $(this).parent().parent().find('.active').removeClass('active');
        $(this).parent().addClass('active');
        $('.db-details').hide();
        $('#' + details).show();
    });

    $('.table-column').bind('click', function(){
        var details = $(this).data('details');
        $(this).parent().parent().find('.active').removeClass('active');
        $(this).addClass('active');
        $('.db-details').hide();
        $('#' + details).show();
    })
});