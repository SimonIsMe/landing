var pusher = new Pusher('232a0606aba9004dcbe2', {
    authEndpoint: '/pusher_auth.php',
    cluster: 'eu',
    encrypted: true
});

var channel = pusher.subscribe('private-channel');
channel.bind('client-create', function(data) {
    if ($('#listen').is(':checked') == false)
        return;

    $('#content .container').prepend('<div id="art_' + data.id + '" class="article">' +
        '<div class="edit" title="Edytuj"><i class="fa fa-pencil-square-o"></i></div>' +
            '<h2>' + data.title + '</h2>' +
        '<p>' + data.content + '</p>' +
    '</div>');
    rebindEvents();
});
channel.bind('client-update', function(data) {
    if ($('#listen').is(':checked') == false)
        return;

    $('#content .container #art_' + data.id + ' h2').text(data.title);
    $('#content .container #art_' + data.id + ' p').text(data.content);
});

var editArticleId = null;

function rebindEvents() {
    $('.edit').unbind('click').bind('click', function(){
        //console.log($(this).parent().find('h2'));
        var title = $(this).parent().find('h2').text();
        var content = $(this).parent().find('p').text();
        $('form input[type="text"]').val(title);
        $('form textarea').val(content);
        editArticleId = $(this).parent().attr('id').substr(4);
        $('form').show();
    });

    $('#create-new').unbind('click').bind('click', function(){
        $('form input[type="text"]').val('');
        $('form textarea').val('');
        editArticleId = null;
        $('form').show();
    });

    $('form a').unbind('click').bind('click', function() {
        $('form').hide();
        return false;
    });

    $('form').unbind('submit').bind('submit', function() {
        var title = $('form input[type="text"]').val();
        var content = $('form textarea').val();

        if (editArticleId == null) {
            editArticleId = makeId(32);
            $('#content .container').prepend('<div id="art_' + editArticleId + '" class="article">' +
                '<div class="edit" title="Edytuj"><i class="fa fa-pencil-square-o"></i></div>' +
                '<h2>' + title + '</h2>' +
                '<p>' + content + '</p>' +
            '</div>');
            channel.trigger('client-create', {
                id: editArticleId,
                title: title,
                content: content
            });
        } else {
            $('#content .container #art_' + editArticleId + ' h2').text(title);
            $('#content .container #art_' + editArticleId + ' p').text(content);
            channel.trigger('client-update', {
                id: editArticleId,
                title: title,
                content: content
            });
        }

        editArticleId = null;
        $('form').hide();
        return false;
    });
}

$(document).ready(function()
{
    rebindEvents();
});

function makeId(length)
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < length; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}