/* global alert:readonly kontrolgruppenMessages:readonly */
/**
 * Prevent CPR numbers in text field. Displays an alert.
 */
(function ($) {
    $.fn.preventCPRinText = function (options) {
        let self = this;
        let inputElements = $(this);

        self.options = $.extend({
            text: ('prevent_cpr.cpr_in_content' in kontrolgruppenMessages) ? kontrolgruppenMessages['prevent_cpr.cpr_in_content'] : 'Indeholder følgende CPR-numre (fjern venligst): %list'
        }, options);

        function checkInputField (event, inputElement) {
            let contents = '';

            let cke = inputElement.siblings('div .cke');

            if (cke.length > 0) {
                contents = inputElement.siblings('div .cke').find('iframe').contents().find('body').text();
            } else {
                contents = inputElement.val();
            }

            let matches = contents.match(/\d{6}-?\d{4}/g);

            // Prevent submit if CPR in element.
            if (matches) {
                event.preventDefault(event);

                // Display error, and how to correct.
                alert(self.options.text.replace('%list%', matches.reduce(function (accumulator, currentValue) {
                    return accumulator !== '' ? accumulator + ', ' + currentValue : currentValue;
                })));
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
