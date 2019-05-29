'use strict';

var parse = require('url-parse');

$(document).ready(function () {
    let globalSearch = $('#kontrolgruppenGlobalSearch');

    let url = parse(window.location, true);

    $('#kontrolgruppenGlobalSearch > input').val(url.query.search);

    globalSearch.on('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        let input = $(this).find('input').val();

        window.location.href = '/search/?search=' + input;

        return false;
    });
});
