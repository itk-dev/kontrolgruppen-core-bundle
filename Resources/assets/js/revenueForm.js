/* global translate */

/**
 * Add javascript to control revenue form.
 */
(function ($) {
    const canEdit = window.REVENUE_FORM_CAN_EDIT;
    const removeButtonText = translate('economy.revenue.remove');

    // Add buttons to form when the page is loaded.
    $(document).ready(function () {
        let $defaultHolder;

        // Get the element that holds the collection.
        $defaultHolder = $('tbody.revenue_entries');

        if (canEdit) {
            // Add "remove" buttons to existing entries.
            $defaultHolder.find('tr').each(function () {
                addRemoveRevenueEntryButton($(this));
            });

            // Setup index as number of entries. The index is used to name the forms.
            $defaultHolder.data('index', $defaultHolder.find(':input').length);

            // When add button is pressed, add a new form element.
            $('.js-add-future-savings').on('click', function (event) {
                event.stopPropagation();
                event.preventDefault();

                const serviceId = $(this).data('service-id');
                addRevenueEntryForm($defaultHolder, $('#revenue-calculation-table-' + serviceId), serviceId, 'FUTURE_SAVINGS');
            });
            $('.js-add-repayment').on('click', function (event) {
                event.stopPropagation();
                event.preventDefault();

                const serviceId = $(this).data('service-id');
                addRevenueEntryForm($defaultHolder, $('#revenue-calculation-table-' + serviceId), serviceId, 'REPAYMENT');
            });
        }

        // Move entries from default holder table to service tables.
        $defaultHolder.find('tr.revenue-entry').each(function () {
            const $element = $(this);
            const selectedServiceId = $element.find('option:selected').val();
            const $serviceElement = $('#service-elements-' + selectedServiceId);

            if ($serviceElement.length === 1) {
                $element.find('.service').hide();
                if ($element.find('.type select').val() === 'REPAYMENT') {
                    $element.find('.future-savings-select').hide();
                }
                $element.find('.type .text').text($element.find('.type option:selected').text());

                $serviceElement.append($element.detach());
            } else {
                $('#revenue-lost-entries').show();
            }
        });
    });

    /**
     * Add remove revenue entry button.
     */
    function addRemoveRevenueEntryButton ($element) {
        const $removeFormButton = $('<button class="btn btn-sm btn-danger mt-3 mb-3" type="button">' + removeButtonText + '</button>');
        $element.append($removeFormButton);

        // When the button is pressed, remove the element.
        $removeFormButton.on('click', function () {
            $element.remove();
        });
    }

    /**
     * Add a new revenue entry form.
     *
     * @param $defaultHolder
     *   The default holder, that also has the form prototype
     * @param $holder
     *   The holder to add the new form element to
     * @param service
     *   The service to add the revenue entry to
     * @param type
     *   The type, either 'FUTURE_SAVINGS' or 'REPAYMENT'
     */
    function addRevenueEntryForm ($defaultHolder, $holder, service, type) {
        // Get the data-prototype for revenue entries.
        const prototype = $defaultHolder.data('prototype');

        // Get the new index.
        const index = $defaultHolder.data('index');

        let newForm = prototype;

        // Replace '__name__' in the prototype's HTML to instead be a number based on how many items we have.
        newForm = newForm.replace(/__name__/g, index);

        // Increase the index with one for the next item.
        $defaultHolder.data('index', index + 1);

        // Display the form in the page.
        const $element = $(newForm);

        $element.find('.service select option[value="' + service + '"]').prop('selected', 'selected');
        $element.find('.service').hide();
        $element.find('.type select option[value="' + type + '"]').prop('selected', 'selected');
        $element.find('.type .text').text($element.find('.type option:selected').text());

        if (type !== 'FUTURE_SAVINGS') {
            $element.find('.future-savings-select').hide();
        } else {
            $element.find('.future-savings-select select option[value="FIXED_VALUE"]').prop('selected', 'selected');
        }

        $holder.append($element);
        if (canEdit) {
            addRemoveRevenueEntryButton($element);
        }
    }
}(jQuery));
