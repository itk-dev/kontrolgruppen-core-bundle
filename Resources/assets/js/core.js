const $ = require('jquery');
global.$ = global.jQuery = $;

import 'whatwg-fetch';

require('bootstrap');
require('select2');

// Add Moment.js.
const moment = require('moment');
// Set moment locale to danish.
moment.locale('da');
global.moment = moment;

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
import { faPencilAlt } from '@fortawesome/free-solid-svg-icons/faPencilAlt';
import { faHouseDamage } from '@fortawesome/free-solid-svg-icons/faHouseDamage';
import { faFileDownload } from '@fortawesome/free-solid-svg-icons/faFileDownload';
import { faPrint } from '@fortawesome/free-solid-svg-icons/faPrint';
import { faCheck } from '@fortawesome/free-solid-svg-icons/faCheck';
import { faLayerGroup } from '@fortawesome/free-solid-svg-icons/faLayerGroup';
import { faSort } from "@fortawesome/free-solid-svg-icons/faSort";
import { faSortUp } from "@fortawesome/free-solid-svg-icons/faSortUp";
import { faSortDown } from "@fortawesome/free-solid-svg-icons/faSortDown";
import { faCalendar } from "@fortawesome/free-solid-svg-icons/faCalendar";
import { faArrowUp } from "@fortawesome/free-solid-svg-icons/faArrowUp";
import { faArrowDown } from "@fortawesome/free-solid-svg-icons/faArrowDown";
import { faChevronRight } from "@fortawesome/free-solid-svg-icons/faChevronRight";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons/faChevronLeft";
import { faCalendarCheck } from "@fortawesome/free-solid-svg-icons/faCalendarCheck";
import { faTrash } from "@fortawesome/free-solid-svg-icons/faTrash";
import { faTimes } from "@fortawesome/free-solid-svg-icons/faTimes";

library.add(
    faTachometerAlt, faTasks, faIdCard, faUsersCog, faCog, faClock,
    faUserPlus, faArchive, faEye, faPencilAlt, faHouseDamage, faFileDownload,
    faPrint, faCheck, faLayerGroup, faSort, faSortUp, faSortDown, faCalendar,
    faArrowUp, faArrowDown, faChevronRight, faChevronLeft, faCalendarCheck,
    faTrash, faTimes
);
dom.watch();

require('./monthpicker/monthpicker');
require('./monthpicker/monthpicker.css');

// https://tempusdominus.github.io/bootstrap-4
require('tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4');
require('tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css');

require('../css/core.scss');

$(function () {
    $('[data-toggle="tooltip"]').tooltip(
        {
            delay: {show: 400},
        }
    );

    // Apply select2 to all elements with select2 class.
    $(document).ready(function() {
        $('.select2').select2();

        $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
            format: 'DD/MM YYYY',
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
            let inputGroup = $('<div class="input-group date" id="datetimepicker' + i +'" data-target-input="nearest"></div>');
            let input = $(parent).find('input');

            $(input).attr('data-target', '#datetimepicker' + i);
            $(parent).find('input').remove();
            inputGroup.html(input);

            let el = $(
                '<div class="input-group-append" data-target="#datetimepicker' + i + '" data-toggle="datetimepicker">\n' +
                '<div class="input-group-text"><i class="fa fa-calendar"></i></div>\n' +
                '</div>')
            ;

            $(inputGroup).append(el);

            $(parent).find('label').after(inputGroup);
        });
    });
});
