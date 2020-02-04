/* global kontrolgruppenMessages */

/**
 * Prevent CPR numbers in text field. Displays an alert.
 */
(function ($) {
    $.fn.preventCPRinText = function (options) {
        let self = this;
        let inputElements = $(this);

        self.options = $.extend({
            text: ('prevent_cpr.cpr_in_content' in kontrolgruppenMessages) ? kontrolgruppenMessages['prevent_cpr.cpr_in_content'] : 'Vi har fundet følgende, der kan være CPR-numre: %list% \nEr du sikker på at du vil gemme?\n Husk at du ikke må gemme CPR-numre.'
        }, options);

        function checkInputField (event, inputElement) {
            let contents = '';

            let cke = inputElement.siblings('div .cke');

            if (cke.length > 0) {
                contents = inputElement.siblings('div .cke').find('iframe').contents().find('body').text();
            } else {
                contents = inputElement.val();
            }

            let matches = contents.match(/[0-3][0-9][0-1][0-9]{3}-?\d{4}/g);

            // Prevent submit if CPR in element.
            if (matches) {
                // Display error, and how to correct.
                let confirmSubmit = window.confirm(self.options.text.replace('%list%', matches.reduce(function (accumulator, currentValue) {
                    return accumulator !== '' ? accumulator + ', ' + currentValue : currentValue;
                })));

                // Only submit if user confirmed the action.
                if (!confirmSubmit) {
                    event.preventDefault(event);
                }
            }
        }

        // Register form listener.
        $.each(inputElements, function (index, el) {
            const element = el;

            $(element).closest('form').submit(function (event) {
                checkInputField(event, $(element));
            });
        });

        return this;
    };
}(jQuery));
