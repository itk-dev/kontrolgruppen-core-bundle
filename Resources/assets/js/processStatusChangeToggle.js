$(document).ready(function () {
    let searchParams = new URLSearchParams(window.location.search);

    if (searchParams.has('only_show_status')) {
        let onlyShowStatus = searchParams.get('only_show_status');

        if (onlyShowStatus === 'true') {
            $('#journal_filter_type').prop('disabled', function (i, v) { return !v; });
            $('#onlyStatusCheckbox').attr('checked', true);
        }
    }

    $('#onlyStatusCheckbox').on('change', function () {
        if ($(this).is(':checked')) {
            $('#journal_filter_type').prop('disabled', function (i, v) { return !v; });

            let onlyStatus = $('#onlyStatusCheckbox').val();

            $(this)
                .closest('form')
                .append('<input type="hidden" name="only_show_status" value="' + onlyStatus + '"/>');
        }

        $(this).closest('form').submit();
    });
});
