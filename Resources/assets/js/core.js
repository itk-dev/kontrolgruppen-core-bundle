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
import { faSearch } from '@fortawesome/free-solid-svg-icons/faSearch';
import { Danish as flatpickrDanish } from 'flatpickr/dist/l10n/da.js';
import 'flatpickr/dist/flatpickr.css';

const $ = require('jquery');
global.$ = global.jQuery = $;

require('bootstrap');
require('select2');

library.add(
    faTachometerAlt, faTasks, faIdCard, faUsersCog, faCog, faClock,
    faUserPlus, faArchive, faEye, faPencilAlt, faHouseDamage, faFileDownload,
    faPrint, faCheck, faLayerGroup, faSort, faSortUp, faSortDown, faCalendar,
    faArrowUp, faArrowDown, faChevronRight, faChevronLeft, faCalendarCheck,
    faTrash, faTimes, faEyeSlash, faDoorClosed, faDoorOpen, faUserCircle, faSave,
    faChartPie, faSearch
);
dom.watch();

require('jquery-confirm');

require('../css/core.scss');

require('./preventCPR/preventCPR');

require('flatpickr');

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

        $('.js-datepicker').flatpickr({
            dateFormat: 'd-m-Y',
            locale: flatpickrDanish
        });

        $('.js-datetimepicker').flatpickr({
            enableTime: true,
            dateFormat: 'd-m-Y H:i',
            locale: flatpickrDanish
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

    // Prevent double submission on forms with the prevent-double-submission class
    let preventDoubleSubmissionForm = $('.prevent-double-submission');

    preventDoubleSubmissionForm.on('submit', function (e) {
        if (preventDoubleSubmissionForm.data('submitted')) {
            e.preventDefault();
        } else {
            preventDoubleSubmissionForm.data('submitted', true);
        }
    });
});
