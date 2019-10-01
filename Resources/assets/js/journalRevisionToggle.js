$('.js-revision-toggle').click(function () {
    var journalId = $(this).data('journal-id');

    $('.js-revision-list[data-journal-id=' + journalId + ']').toggle();
});
