const $ = require('jquery');

$(() => {
    const updateUI = () => {
        // Disable all parameter form controls
        $('.export-parameters :input').prop('disabled', true);
        $('.export-parameters').hide();
        const id = $('#export').val();
        // Enable parameter form controls for selected export
        $('#export-parameters-' + id + ' :input').prop('disabled', false);
        $('#export-parameters-' + id).show();
    };

    $('#export').on('change', updateUI);
    updateUI();
});
