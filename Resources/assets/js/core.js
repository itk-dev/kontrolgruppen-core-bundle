/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// Add fontawesome
import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { faTachometerAlt } from "@fortawesome/free-solid-svg-icons/faTachometerAlt";
import { faTasks } from "@fortawesome/free-solid-svg-icons/faTasks";
import { faIdCard } from "@fortawesome/free-solid-svg-icons/faIdCard";
import { faUsersCog } from "@fortawesome/free-solid-svg-icons/faUsersCog";
import { faCog } from "@fortawesome/free-solid-svg-icons/faCog";
import { faClock } from "@fortawesome/free-solid-svg-icons/faClock";
import { faUserPlus } from "@fortawesome/free-solid-svg-icons/faUserPlus";
import { faArchive } from "@fortawesome/free-solid-svg-icons/faArchive";
import { faEye } from "@fortawesome/free-solid-svg-icons/faEye";
import { faPencilAlt } from "@fortawesome/free-solid-svg-icons/faPencilAlt";
import { faHouseDamage } from "@fortawesome/free-solid-svg-icons/faHouseDamage";

library.add(faTachometerAlt, faTasks, faIdCard, faUsersCog, faCog, faClock, faUserPlus, faArchive, faEye, faPencilAlt, faHouseDamage);
dom.watch();

// any CSS you require will output into a single css file (app.css in this case)
require('../css/core.scss');

$(function () {
  $('[data-toggle="tooltip"]').tooltip(
    {
      delay: {show: 400},
    }
  )
})
