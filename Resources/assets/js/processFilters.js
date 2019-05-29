$(document).ready(function () {
    $('form select').on('change', function () {
        $('.js-process-table-results').hide();
        $('#js-process-spinner').show();
        $(this).closest('form').submit();
    });
});
