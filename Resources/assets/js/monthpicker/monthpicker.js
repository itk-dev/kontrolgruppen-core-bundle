/**
 * Month range picker jQuery plugin.
 *
 * Sets the value of the selected fields in the month/year format.
 *
 * Example use:
 *
 * <input type="text" class="monthpicker-from">
 * <input type="text" class="monthpicker-to">
 * <div class="monthpicker"></div>
 * <script>
 * $(document).ready(function () {
 *   $('.monthpicker').monthpicker($('.monthpicker-from'), $('.monthpicker-to'));
 * });
 * </script>
 */
(function ($) {
    let months = [];
    for (var i = 0; i < 12; i++) {
        months.push(moment().month(i).format('MMM'));
    }

    const now = moment();
    const currentMonth = now.month();
    const currentYear = now.year();

    const createView = function (content, source) {
        let yearElement = $('<div class="itkdev-year"><button class="btn btn-primary year-dec-' + source + ' m-1">-</button><button class="btn btn-light year-' + source + ' m-1"></button><button class="btn btn-primary year-inc-' + source + ' m-1">+</button></div>');

        content.append(yearElement);

        let monthsElement = $('<div class="itkdev-months months"></div>');

        for (monthKey in months) {
            let month = months[monthKey];
            let monthValue = parseInt(monthKey) + 1;

            let monthElement = $('<button class="btn btn-secondary itkdev-month-button month-button-' + source + ' m-1" data-month="' + monthValue + '">' + month + '</button>');

            monthsElement.append(monthElement);
        }

        content.append(monthsElement);
    };

    const applyValues = function (monthPicker) {
        monthPicker.find('.current-values .first-value')
            .html(months[monthPicker.from.month - 1] + '. ' + monthPicker.from.year);
        monthPicker.find('.current-values .second-value')
            .html(months[monthPicker.to.month - 1] + '. ' + monthPicker.to.year);

        monthPicker.fromElement.val(monthPicker.from.month + '/' + monthPicker.from.year);
        monthPicker.toElement.val(monthPicker.to.month + '/' + monthPicker.to.year);
    };

    const getMonth = function (value) {
        return parseInt(value.split('/')[0]);
    };

    const getYear = function (value) {
        return parseInt(value.split('/')[1]);
    };

    const enforceConstraints = function (monthPicker) {
        if (monthPicker.from.year > monthPicker.to.year) {
            monthPicker.from.year = monthPicker.to.year;
        }

        if (monthPicker.from.year === monthPicker.to.year) {
            if (monthPicker.from.month > monthPicker.to.month) {
                monthPicker.from.month = monthPicker.to.month;
            }
        }
    };

    const setValuesFromInput = function (monthPicker) {
        // @TODO: Validate input values.
        monthPicker.fromValue = monthPicker.fromElement.val() ? monthPicker.fromElement.val() : currentMonth + '/' + currentYear;
        monthPicker.toValue = monthPicker.toElement.val() ? monthPicker.toElement.val() : currentMonth + '/' + currentYear;

        monthPicker.from = {
            month: getMonth(monthPicker.fromValue),
            year: getYear(monthPicker.fromValue)
        };

        monthPicker.to = {
            month: getMonth(monthPicker.toValue),
            year: getYear(monthPicker.toValue)
        };
    };

    const setHtml = function (monthPicker) {
        let currentElement = $('<div class="current-values"><span class="first-value"></span> - <span class="second-value"></span></div>');
        monthPicker.append(currentElement);

        monthPicker.find('.current-values .first-value')
            .html(months[monthPicker.from.month - 1] + '. ' + monthPicker.from.year);
        monthPicker.find('.current-values .second-value')
            .html(months[monthPicker.to.month - 1] + '. ' + monthPicker.to.year);

        let fromContent = $('<div class="itkdev-monthpicker-content-from itkdev-monthpicker-content"></div>');
        let toContent = $('<div class="itkdev-monthpicker-content-to itkdev-monthpicker-content"></div>');

        createView(fromContent, 'from');
        createView(toContent, 'to');

        let modalElement = $(
            '<div class="modal fade js-month-picker-modal" id="itkdevMonthPicker" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">\n' +
            '  <div class="modal-dialog modal-lg" role="document">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
            '          <span aria-hidden="true">&times;</span>\n' +
            '        </button>\n' +
            '      </div>\n' +
            '      <div class="modal-body itkdev-month-picker-modal-content"></div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="itkdev-cancel-button btn btn-secondary" class="btn btn-secondary" data-dismiss="modal">Cancel</button>\n' +
            '        <button type="button" class="js-apply-button btn btn-primary" data-dismiss="modal">Apply</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>');

        fromContent.appendTo(modalElement.find('.modal-body'));
        toContent.appendTo(modalElement.find('.modal-body'));

        monthPicker.append(modalElement);
    };

    const updateHtmlValues = function (monthPicker) {
        enforceConstraints(monthPicker);

        monthPicker.find('.year-from').html(monthPicker.from.year);
        monthPicker.find('.year-to').html(monthPicker.to.year);

        monthPicker.find('.month-button-from').removeClass('active').removeClass('btn-primary').addClass('btn-secondary').removeClass('btn-info');
        monthPicker.find('.month-button-from[data-month=' + monthPicker.from.month + ']')
            .addClass('active')
            .removeClass('btn-secondary')
            .addClass('btn-primary');

        let max = (monthPicker.from.year < monthPicker.to.year) ? 12 : monthPicker.to.month;
        for (let i = monthPicker.from.month; i <= max; i++) {
            monthPicker.find('.month-button-from[data-month=' + i + ']').addClass('btn-info');
        }

        monthPicker.find('.month-button-to').removeClass('active').removeClass('btn-primary').addClass('btn-secondary').removeClass('btn-info');
        monthPicker.find('.month-button-to[data-month=' + monthPicker.to.month + ']')
            .addClass('active')
            .removeClass('btn-secondary')
            .addClass('btn-primary');

        let min = (monthPicker.from.year < monthPicker.to.year) ? 1 : monthPicker.from.month;
        for (let i = min; i <= monthPicker.to.month; i++) {
            monthPicker.find('.month-button-to[data-month=' + i + ']').addClass('btn-info');
        }
    };

    $.fn.monthpicker = function (fromElement, toElement) {
        let self = $(this);

        self.fromElement = fromElement;
        self.toElement = toElement;

        setValuesFromInput(self);
        setHtml(self);
        updateHtmlValues(self);

        // Register listeners.
        self.find('.month-button-from').on('click', function () {
            event.preventDefault();
            event.stopPropagation();
            self.from.month = $(this).attr('data-month');
            updateHtmlValues(self);
        });
        self.find('.month-button-to').on('click', function () {
            event.preventDefault();
            event.stopPropagation();
            self.to.month = $(this).attr('data-month');
            updateHtmlValues(self);
        });
        self.find('.year-dec-from').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            self.from.year = self.from.year - 1;
            updateHtmlValues(self);
        });
        self.find('.year-inc-from').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            self.from.year = self.from.year + 1;
            updateHtmlValues(self);
        });
        self.find('.year-dec-to').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            self.to.year = self.to.year - 1;
            updateHtmlValues(self);
        });
        self.find('.year-inc-to').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            self.to.year = self.to.year + 1;
            updateHtmlValues(self);
        });
        self.find('.js-apply-button').on('click', function (event) {
            console.log('apply');
            event.preventDefault();
            event.stopPropagation();
            self.find('.js-month-picker-modal').modal('hide');
            applyValues(self);
        });
        self.find('.current-values').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            self.find('.js-month-picker-modal').modal();
            setValuesFromInput(self);
            updateHtmlValues(self);
        });

        return this;
    };
}(jQuery));
