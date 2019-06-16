/**
 * Month range picker jQuery plugin.
 *
 * Sets the value of the selected fields in the month/year format.
 *
 * Example use:
 *
 * <input type="text" class="js-monthpicker-from">
 * <input type="text" class="js-monthpicker-to">
 * <div class="js-monthpicker"></div>
 * <script>
 * $(document).ready(function () {
 *   $('.js-monthpicker').monthpicker($('.js-monthpicker-from'), $('.js-monthpicker-to'));
 * });
 * </script>
 */
(function ($) {
    let self = null;

    let months = [];
    for (var i = 0; i < 12; i++) {
        months.push(moment().month(i).format("MMM"));
    }

    const now = moment();
    const currentMonth = now.month();
    const currentYear = now.year();

    const createView = function (content, selectedMonth, selectedYear) {
        let yearElement = $('<div class="itkdev-year"><button class="btn btn-primary year-dec m-1">-</button><button class="btn btn-light year m-1">' + selectedYear + '</button><button class="btn btn-primary year-inc m-1">+</button></div>');

        content.append(yearElement);

        let monthsElement = $('<div class="itkdev-months months"></div>');

        for (monthKey in months) {
            let month = months[monthKey];
            let monthValue = parseInt(monthKey) + 1;

            let monthElement = $('<button class="btn btn-secondary itkdev-month-button month-button m-1" data-month="' + monthValue + '">' + month + '</button>');

            // Select current month.
            if (monthValue === selectedMonth) {
                monthElement.attr('data-selected', true);
                monthElement.addClass('active');
            }

            monthsElement.append(monthElement);
        }

        content.append(monthsElement);
    };

    const calculateValues = function () {
        const from = self.find('.itkdev-monthpicker-content-from');
        let year = from.find('.year').text();
        let month = from.find('.month-button[data-selected="true"]').data('month');
        self.fromElement.val((month) + '/' + year);

        const to = self.find('.itkdev-monthpicker-content-to');
        year = to.find('.year').text();
        month = to.find('.month-button[data-selected="true"]').data('month');
        self.toElement.val((month) + '/' + year);
    };

    const getMonth = function (value) {
        return parseInt(value.split('/')[0]);
    };

    const getYear = function (value) {
        return parseInt(value.split('/')[1]);
    };

    $.fn.monthpicker = function (fromElement, toElement) {
        self = $(this);

        self.fromElement = fromElement;
        self.toElement = toElement;

        // @TODO: Validate input values.
        self.fromValue = fromElement.val() ? fromElement.val() : currentMonth + '/' + currentYear;
        self.toValue = toElement.val() ? toElement.val() : currentMonth + '/' + currentYear;

        let fromContent = $('<div class="itkdev-monthpicker-content-from itkdev-monthpicker-content"></div>');
        let toContent = $('<div class="itkdev-monthpicker-content-to itkdev-monthpicker-content"></div>');

        createView(fromContent, getMonth(self.fromValue), getYear(self.fromValue));
        createView(toContent, getMonth(self.toValue), getYear(self.toValue));

        self.append(fromContent);
        self.append(toContent);

        // Register listeners.
        $('.month-button').on('click', function () {
            $(this).siblings('.month-button').attr('data-selected', null).removeClass('active');
            $(this).attr('data-selected', true).addClass('active');
            calculateValues();
        });

        $('.year-dec').on('click', function () {
            $(this).next().text(parseInt($(this).next().text()) - 1);
            calculateValues();
        });

        $('.year-inc').on('click', function () {
            $(this).prev().text(parseInt($(this).prev().text()) + 1);
            calculateValues();
        });

        return this;
    };
}(jQuery));
