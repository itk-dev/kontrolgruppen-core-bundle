$(document).ready(function(){
  $('#kontrolgruppenGlobalSearch').on('submit', function (event) {
    event.preventDefault();
    event.stopPropagation();

    let input = $(this).find('input').val();

    window.location.href = '/process/?process_filter[wildcard]=' + input;

    return false;
  });
});
