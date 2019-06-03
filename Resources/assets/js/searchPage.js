import Mark from 'mark.js';

$(document).ready(function () {
    var context = document.querySelector('.js-search-result-rows');
    var instance = new Mark(context);
    instance.mark($('.js-search-text').data('search'));
});
