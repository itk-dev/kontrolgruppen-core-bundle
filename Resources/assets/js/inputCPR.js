$('.js-input-cpr').on('change', function (event) {
    if (event.target.value.match(/^\d{10}$/)) {
        event.target.value = event.target.value.substring(0, 6) + '-' + event.target.value.substring(6, 10);
    }
});
