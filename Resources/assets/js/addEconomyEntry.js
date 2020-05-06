import { Danish as flatpickrDanish } from 'flatpickr/dist/l10n/da';

$(document).ready(function () {
    $('#economy_entry_type').change(function () {
        $(this).closest('form').submit();
        $('#base_economy_entry').hide();
        $('#service_economy_entry').hide();
        $('#js-economy-form-spinner').show();
    });

    $('#economy_entry_datepicker').click(function () {
        $('#period_datepicker_modal').modal();
    });

    $('.js-economy-entry-period-from').flatpickr({
        inline: true,
        dateFormat: 'd-m-Y',
        locale: flatpickrDanish,
        onChange: function (selectedDates, dateStr, instance) {
            $('.js-economy-entry-period-to').each(function () {
                this._flatpickr.set('minDate', selectedDates[0]);
            });
        }
    });

    $('.js-economy-entry-period-to').flatpickr({
        inline: true,
        dateFormat: 'd-m-Y',
        locale: flatpickrDanish,
        onChange: function (selectedDates, dateStr, instance) {
            $('.js-economy-entry-period-from').each(function () {
                this._flatpickr.set('maxDate', selectedDates[0]);
            });
        }
    });

    $('#datepicker_period_save').click(function () {
        $('#economy_entry_datepicker').text($('.js-economy-entry-period-from').val() + ' - ' + $('.js-economy-entry-period-to').val());

        $('#period_datepicker_modal').modal('toggle');
    });
});
