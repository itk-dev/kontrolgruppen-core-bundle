'use strict';

$(document).ready(function () {
    $('#process_status_form #form_processStatus').on('change', function () {
        $('#process_status_form #form_save').show();
    });
});
