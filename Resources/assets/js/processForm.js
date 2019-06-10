$(document).ready(function () {
    var processTypeInput = $('#process_processType');

    // Load service choices. Replaces form element with ajax loaded element.
    function loadServiceChoices (element) {
        $('#process_service').attr('disabled', 'disabled').val(null);

        var $form = element.closest('form');

        var data = {};
        data[processTypeInput.attr('name')] = processTypeInput.val();

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                $('#process_service').replaceWith(
                    $(html).find('#process_service')
                );
            }
        });
    }

    // Load for the present selected process type.
    loadServiceChoices(processTypeInput);

    // Register listener for changes to process type.
    processTypeInput.change(function () {
        loadServiceChoices($(this));
    });
});
