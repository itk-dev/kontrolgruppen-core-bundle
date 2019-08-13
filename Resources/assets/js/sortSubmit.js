$(document).ready(function () {
    // Attach sort direction and submit on change
    $('form.js-filters select').on('change', function () {
        $('.js-process-table-results').hide();
        $('#js-process-spinner').show();

        let sortDirection = $(this)
            .closest('form')
            .find('#js-sort-select')
            .val();

        $(this)
            .closest('form')
            .append('<input type="hidden" name="sort_direction" value="' + sortDirection + '"/>');

        $(this).closest('form').submit();
    });

    let searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('only_show_status')) {

        let onlyShowStatus = searchParams.get('only_show_status');

        if ('true' == onlyShowStatus) {

            $('#journal_filter_type').prop('disabled', function(i, v) { return !v });
            $('#onlyStatusCheckbox').attr('checked', true);
        }
    }

    $('#onlyStatusCheckbox').on('change', function() {

        if($(this).is(":checked")) {

            $('#journal_filter_type').prop('disabled', function(i, v) { return !v });

            let onlyStatus = $('#onlyStatusCheckbox').val();

            $(this)
                .closest('form')
                .append('<input type="hidden" name="only_show_status" value="' + onlyStatus + '"/>');
        }

        $(this).closest('form').submit();
    });
});
