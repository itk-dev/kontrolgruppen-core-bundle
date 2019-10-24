$(document).ready(function () {
    // Hide revenue submit button when no forms present en revenue table
    if ($('#revenue-calculation-table').find('form').length === 0) {
        $('#save-revenue-calculation-button').hide();
    }

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
            format: 'MM-YY',
            useCurrent: false,
            defaultDate: false
        });

        $('#period-modal-to').datetimepicker({
            inline: true,
            format: 'MM-YY',
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

        let amountElement = $(periodModalTriggerElement.data('amount-input'));
        amountElement.prop('required', true);

        $('#period-modal').modal('toggle');

        $('#period-modal-from').datetimepicker('destroy');
        $('#period-modal-to').datetimepicker('destroy');
    });

    // Each row in the table on the page is a form. Instead of manually submitting every form, we have a button handler
    // that submits each form by ajax and render errors in the UI. Only form which contains new data is submitted though.
    $('#save-revenue-calculation-button').on('click', function (event) {
        let dirtyForms = new Map();

        $('#revenue-calculation-table').find('form').each(function () {
            let form = this;

            this.elements.forEach(function (element) {
                if (element.type === 'text' || element.type === 'hidden') {
                    if (element.defaultValue !== element.value) {
                        if (!dirtyForms.has(form.name)) {
                            dirtyForms.set(form.name, form);
                        }
                        return false;
                    }
                }
            });
        });

        dirtyForms.forEach(function (form) {
            if (!form.checkValidity()) {
                form.reportValidity();
                return true;
            }

            let futureSavingsPeriodDummy = $(form.parentElement).find('.future-savings-period');
            let futureSavingsPeriodToInput;
            let repaymentPeriodDummy = $(form.parentElement).find('.repayment-period');
            let repaymentPeriodToInput;
            let futureSavingsAmount;
            let repaymentAmount;

            form.elements.forEach(function (element) {
                if (element.classList.contains('future-savings-period-to')) {
                    futureSavingsPeriodToInput = element;
                    return true;
                }

                if (element.classList.contains('repayment-period-to')) {
                    repaymentPeriodToInput = element;
                    return true;
                }

                if (element.classList.contains('future-savings-amount')) {
                    futureSavingsAmount = element;
                    return true;
                }

                if (element.classList.contains('repayment-amount')) {
                    repaymentAmount = element;
                    return true;
                }
            });

            if ($.trim(futureSavingsAmount.value).length && futureSavingsPeriodToInput.value === '') {
                futureSavingsPeriodDummy.tooltip({ 'title': $('#empty-period-error-message').data('message') });
                futureSavingsPeriodDummy.tooltip('show');
                return false;
            }

            if (repaymentAmount.value !== '' && repaymentPeriodToInput.value === '') {
                repaymentPeriodDummy.tooltip({ 'title': $('#empty-period-error-message').data('message') });
                repaymentPeriodDummy.tooltip('show');
                return false;
            }

            $.ajax({
                type: 'POST',
                data: $(form).serialize(),
                beforeSend: function (request) {
                    $('#revenue-table-save-success').addClass('d-none');
                    $('#revenue-table-save-fail').addClass('d-none');
                    $('#revenue-table-spinner').removeClass('d-none');
                },
                success: function (data) {
                    $('#revenue-table-spinner').addClass('d-none');
                    $('#revenue-table-save-success').removeClass('d-none');
                    $(form).parent('tr').removeClass('table-danger');
                },
                error: function (data) {
                    $('#revenue-table-spinner').addClass('d-none');
                    $('#revenue-table-save-fail').removeClass('d-none');

                    let errorForms = JSON.parse(data.responseText);
                    errorForms.forEach(function (item) {
                        $('form[name="' + item + '"]').parent('tr').addClass('table-danger');
                    });
                }
            });
        });
    });
});
