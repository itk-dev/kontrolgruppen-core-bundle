const $ = require('jquery');
global.$ = global.jQuery = $;

require('bootstrap');
require('select2');

// Add Moment.js.
const moment = require('moment');
moment.locale('da');
global.moment = moment;


require('./monthpicker/monthpicker');
require('./monthpicker/monthpicker.css');

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

library.add(
    faTachometerAlt, faTasks, faIdCard, faUsersCog, faCog, faClock,
    faUserPlus, faArchive, faEye, faPencilAlt, faHouseDamage, faFileDownload,
    faPrint, faCheck, faLayerGroup, faSort, faSortUp, faSortDown
);
dom.watch();

require('../css/core.scss');

import 'whatwg-fetch';

$(function () {
    $('[data-toggle="tooltip"]').tooltip(
        {
            delay: {show: 400},
        }
    );

    // Apply select2 to all elements with select2 class.
    $(document).ready(function() {
        $('.select2').select2();
    });
});
