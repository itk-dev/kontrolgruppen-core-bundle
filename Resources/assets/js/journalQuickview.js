/* global fetch:readonly */
$(document).ready(function () {
    function loadJournal (processId) {
        $('#journalQuickviewModal .js-journal-entry-modal-content').html('');
        $('#journalQuickviewModal .js-journal-entry-modal-spinner').show();

        fetch('/process/' + processId + '/journal/latest/')
            .then(function (response) {
                $('#journalQuickviewModal .js-journal-entry-modal-spinner')
                    .hide();
                return response.text();
            })
            .then(function (body) {
                $('#journalQuickviewModal .js-journal-entry-modal-content')
                    .html(body);
            });
    }

    $('.js-process-journal-quickview-button').on('click', function (event) {
        let journalQuickviewModal = $('#journalQuickviewModal');
        let processId = $(this).data('process-id');

        // Set path to all journal entries.
        journalQuickviewModal.find('.js-journal-entry-modal-show-all-link')
            .attr('href', '/process/' + processId + '/journal/');

        journalQuickviewModal.modal({});

        loadJournal(processId);
    });
});
