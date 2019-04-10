require('jquery');
require('bootstrap');
require('../css/core.scss');

import 'whatwg-fetch';

$(function () {
  $('[data-toggle="tooltip"]').tooltip(
    {
      delay: {show: 400},
    }
  )
});
