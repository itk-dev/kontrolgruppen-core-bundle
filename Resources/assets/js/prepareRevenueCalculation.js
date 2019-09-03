$(document).ready(function () {
    // Disabling other possibilities than already selected.
    $('select.readonly option:not(:selected)').attr('disabled', true);
    $('select:not([readonly]) option').removeAttr('disabled');

    // Period modals
    let periodModalTriggerElement;

    $('.period-modal-trigger').click(function () {
        periodModalTriggerElement = $(this);

        $('#period-modal-from').data('target-input', $(periodModalTriggerElement).data('target-input-from'));
        $('#period-modal-to').data('target-input', $(periodModalTriggerElement).data('target-input-to'));

        $('#period-modal-from').datetimepicker({
            inline: true,
            format: 'L',
            useCurrent: false,
            defaultDate: false
        });

        $('#period-modal-to').datetimepicker({
            inline: true,
            format: 'L',
            useCurrent: false,
            defaultDate: false
        });

        $('#period-modal').modal();
    });

    $('#period-modal-close-button').click(function () {
        $('#period-modal-from').datetimepicker('destroy');
        $('#period-modal-to').datetimepicker('destroy');
    });

    $('#period-modal-save-button').click(function () {
        let from = $(periodModalTriggerElement.data('target-input-from')).val();
        let to = $(periodModalTriggerElement.data('target-input-to')).val();

        periodModalTriggerElement.text(from + '-' + to);

        $('#period-modal').modal('toggle');

        $('#period-modal-from').datetimepicker('destroy');
        $('#period-modal-to').datetimepicker('destroy');
    });

    $('#period-modal-from').on('change.datetimepicker', function (event) {
        $('#period-modal-to').datetimepicker('date', event.date.add('1', 'year'));
    });
});
