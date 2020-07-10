$(function () {
    const $cprInputElement = $('#process_clientCPR');
    const $searchCprButton = $('#process_searchCpr');

    $cprInputElement.change(function () {
        if ($('#cpr-search-results').length) {
            $('#cpr-search-results').hide();
            $searchCprButton.text($searchCprButton.data('search-text'));
            $searchCprButton.show();
        }
    });

    $searchCprButton.click(function () {
        $searchCprButton.text($searchCprButton.data('loading-text'));

        $.ajax({
            url: $searchCprButton.data('search-action'),
            type: 'post',
            data: { 'cpr': $cprInputElement.val() },
            success: function (html) {
                if ($('#cpr-search-results').length) {
                    $searchCprButton.hide();
                    $('#cpr-search-results').replaceWith(html);
                    $('#cpr-search-results').show();
                } else {
                    $searchCprButton.hide();
                    $searchCprButton.after(html);
                }
            }
        });
    });
});
