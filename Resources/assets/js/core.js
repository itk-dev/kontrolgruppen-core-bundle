/* global kontrolgruppenMessages */

import 'whatwg-fetch';

// Add fontawesome
import { library, dom } from '@fortawesome/fontawesome-svg-core';
import { faTachometerAlt } from '@fortawesome/free-solid-svg-icons/faTachometerAlt';
import { faTasks } from '@fortawesome/free-solid-svg-icons/faTasks';
import { faIdCard } from '@fortawesome/free-solid-svg-icons/faIdCard';
import { faUsersCog } from '@fortawesome/free-solid-svg-icons/faUsersCog';
import { faCog } from '@fortawesome/free-solid-svg-icons/faCog';
import { faClock } from '@fortawesome/free-solid-svg-icons/faClock';
import { faUserPlus } from '@fortawesome/free-solid-svg-icons/faUserPlus';
import { faArchive } from '@fortawesome/free-solid-svg-icons/faArchive';
import { faEye } from '@fortawesome/free-solid-svg-icons/faEye';
import { faEyeSlash } from '@fortawesome/free-solid-svg-icons/faEyeSlash';
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons/faPencilAlt';
import { faHouseDamage } from '@fortawesome/free-solid-svg-icons/faHouseDamage';
import { faFileDownload } from '@fortawesome/free-solid-svg-icons/faFileDownload';
import { faPrint } from '@fortawesome/free-solid-svg-icons/faPrint';
import { faCheck } from '@fortawesome/free-solid-svg-icons/faCheck';
import { faLayerGroup } from '@fortawesome/free-solid-svg-icons/faLayerGroup';
import { faSort } from '@fortawesome/free-solid-svg-icons/faSort';
import { faSortUp } from '@fortawesome/free-solid-svg-icons/faSortUp';
import { faSortDown } from '@fortawesome/free-solid-svg-icons/faSortDown';
import { faCalendar } from '@fortawesome/free-solid-svg-icons/faCalendar';
import { faArrowUp } from '@fortawesome/free-solid-svg-icons/faArrowUp';
import { faArrowDown } from '@fortawesome/free-solid-svg-icons/faArrowDown';
import { faChevronRight } from '@fortawesome/free-solid-svg-icons/faChevronRight';
import { faChevronLeft } from '@fortawesome/free-solid-svg-icons/faChevronLeft';
import { faCalendarCheck } from '@fortawesome/free-solid-svg-icons/faCalendarCheck';
import { faTrash } from '@fortawesome/free-solid-svg-icons/faTrash';
import { faTimes } from '@fortawesome/free-solid-svg-icons/faTimes';
import { faDoorClosed } from '@fortawesome/free-solid-svg-icons/faDoorClosed';
import { faDoorOpen } from '@fortawesome/free-solid-svg-icons/faDoorOpen';
import { faUserCircle } from '@fortawesome/free-solid-svg-icons/faUserCircle';
import { faSave } from '@fortawesome/free-solid-svg-icons/faSave';
import { faChartPie } from '@fortawesome/free-solid-svg-icons/faChartPie';

const $ = require('jquery');
global.$ = global.jQuery = $;

require('bootstrap');
require('select2');

// Add Moment.js.
const moment = require('moment');
// Set moment locale to danish.
moment.locale('da');
global.moment = moment;

library.add(
    faTachometerAlt, faTasks, faIdCard, faUsersCog, faCog, faClock,
    faUserPlus, faArchive, faEye, faPencilAlt, faHouseDamage, faFileDownload,
    faPrint, faCheck, faLayerGroup, faSort, faSortUp, faSortDown, faCalendar,
    faArrowUp, faArrowDown, faChevronRight, faChevronLeft, faCalendarCheck,
    faTrash, faTimes, faEyeSlash, faDoorClosed, faDoorOpen, faUserCircle, faSave,
    faChartPie
);
dom.watch();

require('jquery-confirm');

// https://tempusdominus.github.io/bootstrap-4
require('tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4');
require('tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css');

require('../css/core.scss');

require('./preventCPR/preventCPR');

let translate = (text) => {
    return (typeof (kontrolgruppenMessages) !== 'undefined' && typeof (kontrolgruppenMessages[text]) !== 'undefined')
        ? kontrolgruppenMessages[text] : text;
};
global.translate = translate;

$(function () {
    $('[data-toggle="tooltip"]').tooltip(
        {
            delay: { show: 400 }
        }
    );

    // Apply select2 to all elements with select2 class.
    $(document).ready(function () {
        $('.select2').select2();

        // Setup datetimepicker defaults.
        $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
            format: 'DD-MM-YYYY HH:mm',
            icons: {
                time: 'fas fa-clock',
                date: 'fas fa-calendar',
                up: 'fas fa-arrow-up',
                down: 'fas fa-arrow-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            } });

        // Transforms dom to match required by datetimepicker.
        let dateInputs = $('.js-datepicker');
        dateInputs.each(function (i, val) {
            let parent = val.closest('.form-group');
            let inputGroup = $('<div class="input-group date" id="datetimepicker' + i + '" data-target-input="nearest"></div>');
            let input = $(parent).find('input');

            $(input).attr('data-target', '#datetimepicker' + i);
            $(parent).find('input').remove();
            inputGroup.html(input);

            let el = $(
                '<div class="input-group-append" data-target="#datetimepicker' + i + '" data-toggle="datetimepicker">\n' +
                '<div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
                '</div>');

            $(inputGroup).append(el);

            $(parent).find('label').after(inputGroup);
        });

        // Use preventCPR script for all text and textarea elements not marked with class .no-cpr-scanning
        $('input[type=text]:not(.no-cpr-scanning), textarea:not(.no-cpr-scanning)').preventCPRinText();

        $('form[data-yes-no-message]').each(function () {
            $(this).on('submit', () => {
                if ($(this).data('submit-confirmed') !== true) {
                    $.confirm({
                        title: $(this).data('yes-no-title') || null,
                        content: $(this).data('yes-no-message'),
                        escapeKey: 'no',
                        buttons: {
                            yes: {
                                text: translate('common.boolean.Yes'),
                                btnClass: 'btn-primary',
                                action: () => {
                                    $(this).data('submit-confirmed', true);
                                    $(this).submit();
                                }
                            },
                            no: {
                                text: translate('common.boolean.No'),
                                btnClass: 'btn-default'
                            }
                        }
                    });
                    return false;
                }
            });
        });
    });

    // Show alert when user is leaving a dirty form unsubmitted
    let isSubmitting = false;

    $('form').submit(function () {
        isSubmitting = true;
    });

    $('form').data('initial-state', $('form').serialize());

    $(window).on('beforeunload', function () {
        if (!isSubmitting && $('form').serialize() !== $('form').data('initial-state')) {
            return 'You have unsaved changes which will not be saved.'; // This will not be shown, but Chrome requires a return value.
        }
    });
});
