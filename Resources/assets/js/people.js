// From: https://symfony.com/doc/current/form/form_collections.html

/* global translate */

let $collectionHolder;

let $addPersonButton = $('<button type="button" class="btn btn-sm btn-primary person-add-button">' + translate('client.form.person.add') + '</button>');
let $newLinkLi = $('<li class="list-group-item"></li>').append($addPersonButton);

$(document).ready(function () {
    $collectionHolder = $('ul.people');

    // add a delete link to all of the existing person form li elements
    $collectionHolder.find('li').each(function () {
        addPersonFormDeleteLink($(this));
    });

    // add the "add a person" anchor and li to the people ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addPersonButton.on('click', function (e) {
        // add a new person form (see next code block)
        addPersonForm($collectionHolder, $newLinkLi);
    });
});

function addPersonForm ($collectionHolder, $newLinkLi) {
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
    addPersonFormDeleteLink($newFormLi);
}

function addPersonFormDeleteLink ($personFormLi) {
    let $removeFormButton = $('<button type="button" class="btn btn-sm btn-danger cars-remove-button float-right">' + translate('client.form.person.remove') + '</button>');
    $personFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {
        // remove the li for the car form
        $personFormLi.remove();
    });
}
