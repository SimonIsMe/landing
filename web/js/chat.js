var Chat = {
    ws : null,
    user: 'Szymon11',
    token: 'aaa',
    init: function()
    {
        Chat.ws = new WebSocket('ws://164.132.230.241:8080');
        Chat.ws.onmessage = function (data)
        {
            var json = JSON.parse(data.data);

            var messageHtml = $('<div class="message invisible">' +
                    '<div class="user">' + json.user + '</div>' +
                    '<div class="content">' + json.message + '</div>' +
                '</div>');
            $('#messages').append(messageHtml);
            setTimeout(function(){
                $('#messages .invisible').removeClass('invisible');
                $('#messages-container').scrollTo($('.message').last());
            }, 100);
        };
        Chat.ws.onmlose = Chat.onClose;
        Chat.ws.onopen = function (){
            Chat.send('aaa', '', '');
        }
    },
    send: function (type, message) {
        Chat.ws.send(
            JSON.stringify({
                token: Chat.token,
                user: Chat.user,
                type: type,
                message: message
            })
        );
    },
    onClose: function (data)
    {
        console.log("onClose");
    }
}

$(document).ready(function(){
    Chat.init();

    $('form').bind('submit', function(){
        var message = $(this).find('input[type="text"]').val().trim();
        if (message == '')
            return false;

        $(this).find('input[type="text"]').val('')
        Chat.send('message', message);

        var messageHtml = $('<div class="message message-from-me invisible">' +
            '<div class="content">' + message + '</div>' +
            '</div>');
        $('#messages').append(messageHtml);
        setTimeout(function(){
            $('#messages .invisible').removeClass('invisible');
            $('#messages-container').scrollTo($('.message').last());
        }, 100);

        return false;
    });
});






$.fn.scrollTo = function( target, options, callback ){
    if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
    var settings = $.extend({
        scrollTarget  : target,
        offsetTop     : 50,
        duration      : 500,
        easing        : 'swing'
    }, options);
    return this.each(function(){
        var scrollPane = $(this);
        var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
        var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
        scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
            if (typeof callback == 'function') { callback.call(this); }
        });
    });
}