/* Sends the per-session CSRF token for all state-changing jQuery requests. */
(function () {
    function cookie(name) {
        var item = document.cookie.split('; ').find(function (part) { return part.indexOf(name + '=') === 0; });
        return item ? decodeURIComponent(item.substring(name.length + 1)) : '';
    }
    $.ajaxSetup({ beforeSend: function (xhr, settings) {
        if (!/^(GET|HEAD|OPTIONS)$/i.test(settings.type || 'GET')) {
            xhr.setRequestHeader('X-CSRF-Token', cookie('wongsibuk_csrf'));
        }
    }});
}());
