$(document).ready(function () {
    const processTypeInput = $('#process_processType');
    // Ids of the elements we want to replace.
    const replacementIds = ['process_service', 'process_channel'];

    // Load choices. Replaces form element with ajax loaded element.
    function loadChoices (element) {
        // Disable and clear elements.
        replacementIds.forEach(id => $('#' + id).attr('disabled', 'disabled').val(null));

        const $form = element.closest('form');

        const data = {
            [processTypeInput.attr('name')]: processTypeInput.val()
        };

        // Submit the form.
        $.ajax({
            url: $form.attr('action'),
            data: data,
            success: function (html) {
                // Replace each element with same element from response.
                replacementIds.forEach(id => $('#' + id).replaceWith($(html).find('#' + id)));
            }
        });
    }

    // Load for the present selected process type if variable is set.
    if (window.PROCESS_FORM_JAVASCRIPT_RUN_FIRST) {
        loadChoices(processTypeInput);
    }

    // Register listener for changes to process type.
    processTypeInput.change(function () {
        loadChoices($(this));
    });
});
