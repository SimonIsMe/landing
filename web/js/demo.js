$(document).ready(function(){
    $('.tabs li').bind('click', function(){
        $(this).parent().children('li.active').removeClass('active');
        $(this).addClass('active');
        var index = $(this).index();

        console.log(index, $(this).parent().parent().children('.tab-content-container'));

        $(this).parent().parent().children('.tab-content-container')
            .children('.tab-content.active').removeClass('active');
        $(this).parent().parent().children('.tab-content-container')
            .children('.tab-content').eq(index).addClass('active');
    });
});