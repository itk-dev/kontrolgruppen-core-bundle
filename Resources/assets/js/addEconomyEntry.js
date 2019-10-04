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
        format: 'DD-MM-YYYY',
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
        let serviceEconomyPeriodFrom = $('#service_economy_entry_periodFrom');
        let serviceEconomyPeriodTo = $('#service_economy_entry_periodTo');

        if (serviceEconomyPeriodFrom.val() && serviceEconomyPeriodTo.val()) {
            $('#service_economy_entry_datepicker').text(serviceEconomyPeriodFrom.val() + ' - ' + serviceEconomyPeriodTo.val());

            $('#service_economy_entry_datepicker_modal').modal('toggle');
        }

        let incomeEconomyPeriodFrom = $('#income_economy_entry_periodFrom');
        let incomeEconomyPeriodTo = $('#income_economy_entry_periodTo');

        if (incomeEconomyPeriodFrom.val() && incomeEconomyPeriodTo.val()) {
            $('#income_economy_entry_datepicker').text(incomeEconomyPeriodFrom.val() + ' - ' + incomeEconomyPeriodTo.val());

            $('#income_economy_entry_datepicker_modal').modal('toggle');
        }
    });
});
