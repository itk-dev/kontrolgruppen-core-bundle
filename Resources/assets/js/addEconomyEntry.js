$(document).ready(function () {
    $('#economy_entry_type').change(function () {
        $(this).closest('form').submit();
        $('#base_economy_entry').hide();
        $('#service_economy_entry').hide();
        $('#js-economy-form-spinner').show();
    });

    // Apply datetimepicker to all js-datepicker elements.
    $('.js-datepicker').datetimepicker();

    $('#service_economy_entry_datepicker').click(function () {
        $('#service_economy_entry_datepicker_modal').modal();
    });

    $('#income_economy_entry_datepicker').click(function () {
        $('#income_economy_entry_datepicker_modal').modal();
    });

    $('.datepicker-period').datetimepicker({
        inline: true,
        format: 'L',
        useCurrent: false,
        defaultDate: false
    });

    $('#datepicker_from').on('change.datetimepicker', function (e) {
        $('#datepicker_to').datetimepicker('minDate', e.date);
    });

    $('#datepicker_to').on('change.datetimepicker', function (e) {
        $('#datepicker_from').datetimepicker('maxDate', e.date);
    });

    $('#datepicker_period_save').click(function () {
        if ($('#service_economy_entry_periodFrom').val()) {
            let from = $('#service_economy_entry_periodFrom').val();
            let to = $('#service_economy_entry_periodTo').val();

            $('#service_economy_entry_datepicker').text(from + ' - ' + to);

            $('#service_economy_entry_datepicker_modal').modal('toggle');
        }

        if ($('#income_economy_entry_periodFrom').val()) {
            let from = $('#income_economy_entry_periodFrom').val();
            let to = $('#income_economy_entry_periodTo').val();

            $('#income_economy_entry_datepicker').text(from + ' - ' + to);

            $('#income_economy_entry_datepicker_modal').modal('toggle');
        }
    });
});
