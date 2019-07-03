/* global fetch:readonly */
function getLatestReminders (interval) {
    $('#coming-reminders-spinner').show();
    $('#coming-reminders-content').html('');

    fetch('/reminder/latest/' + interval)
        .then(function (response) {
            return response.text();
        })
        .then(function (body) {
            $('#coming-reminders-spinner').hide();
            $('#coming-reminders-content').html(body);
        }
        );
}

let formDateInterval = $('#form_date_interval');

formDateInterval.change(function () {
    getLatestReminders($(this).val());
});

getLatestReminders(formDateInterval.val());
