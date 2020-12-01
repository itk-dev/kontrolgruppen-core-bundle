// From: https://symfony.com/doc/current/form/form_collections.html

/* global translate */

let $collectionHolder;

let $addCompanyButton = $('<button type="button" class="btn btn-sm btn-primary company-add-button">' + translate('client.form.company.add') + '</button>');
let $newLinkLi = $('<li class="list-group-item"></li>').append($addCompanyButton);

$(document).ready(function () {
    $collectionHolder = $('ul.companies');

    // add a delete link to all of the existing company form li elements
    $collectionHolder.find('li').each(function () {
        addCompanyFormDeleteLink($(this));
    });

    // add the "add a company" anchor and li to the companies ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addCompanyButton.on('click', function (e) {
        // add a new company form (see next code block)
        addCompanyForm($collectionHolder, $newLinkLi);
    });
});

function addCompanyForm ($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    let prototype = $collectionHolder.data('prototype');

    // get the new index
    let index = $collectionHolder.data('index');

    let newForm = prototype;
    // You need this only if you didn't set 'label' => false in your cars field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a car" link li
    let $newFormLi = $('<li class="list-group-item"></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    // add a delete link to the new form
    addCompanyFormDeleteLink($newFormLi);
}

function addCompanyFormDeleteLink ($companyFormLi) {
    let $removeFormButton = $('<button type="button" class="btn btn-sm btn-danger cars-remove-button float-right">' + translate('client.form.company.remove') + '</button>');
    $companyFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {
        // remove the li for the car form
        $companyFormLi.remove();
    });
}
