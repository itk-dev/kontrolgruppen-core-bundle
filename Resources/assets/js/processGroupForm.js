$(document).ready(function () {
    let $primaryProcess = $('#process_group_primaryProcess');
    let $secondaryProcesses = $('#process_group_processes');
    let $spinner = $('#process-group-form-spinner');
    let $oldPrimaryProcessValue;

    $primaryProcess.focusin(function () {
        $oldPrimaryProcessValue = $(this).children('option:selected').val();
    });

    // When primary process gets selected ...
    $primaryProcess.change(function () {
        $secondaryProcesses.attr('disabled', true);
        $spinner.show();

        // ... retrieve the corresponding form.
        let $form = $(this).closest('form');
        // Simulate form data, but only include the selected primary process value.
        let data = {};
        data[$primaryProcess.attr('name')] = $primaryProcess.val();

        data[$secondaryProcesses.attr('name')] = $secondaryProcesses.val();

        // Submit data via AJAX to the form's action path.
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                // Replace current processes field ...
                $('#process_group_processes').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#process_group_processes')
                );
                // Position field now displays the appropriate positions.

                $('#process_group_processes').find('option[value=' + $oldPrimaryProcessValue + ']').attr('selected', 'selected');

                $('#process_group_processes').attr('disabled', false);
                $spinner.hide();

                $('#process_group_processes').select2();
            }
        });
    });
});
