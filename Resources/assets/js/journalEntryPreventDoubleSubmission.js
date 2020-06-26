$(() => {
    let journalEntryForm = $('#journal-entry-form');

    journalEntryForm.on('submit', function (e) {
        if (journalEntryForm.data('submitted')) {
            e.preventDefault();
        } else {
            journalEntryForm.data('submitted', true);
        }
    });
});
