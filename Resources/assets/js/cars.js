// From: https://symfony.com/doc/current/form/form_collections.html

/* global translate */

let $collectionHolder;

let $addCarButton = $('<button type="button" class="btn btn-sm btn-primary cars-add-button">' + translate('client.form.car.add') + '</button>');
let $newLinkLi = $('<li class="list-group-item"></li>').append($addCarButton);

$(document).ready(function () {
    $collectionHolder = $('ul.cars');

    // add a delete link to all of the existing car form li elements
    $collectionHolder.find('li').each(function () {
        addCarFormDeleteLink($(this));
    });

    // add the "add a car" anchor and li to the cars ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addCarButton.on('click', function (e) {
    // add a new car form (see next code block)
        addCarForm($collectionHolder, $newLinkLi);
    });
});

function addCarForm ($collectionHolder, $newLinkLi) {
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
    addCarFormDeleteLink($newFormLi);
}

function addCarFormDeleteLink ($carFormLi) {
    let $removeFormButton = $('<button type="button" class="btn btn-sm btn-danger cars-remove-button float-right">' + translate('client.form.car.remove') + '</button>');
    $carFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {
    // remove the li for the car form
        $carFormLi.remove();
    });
}
