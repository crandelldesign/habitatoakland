jQuery(document).ready(function ($) {
    $('.habitat-homeowner-checkbox').on('change', function(event)
    {
        if ($(this).is(':checked')) {
            $('.referrer-name-form-group').show();
        } else {
            $('.referrer-name-form-group').hide();
        }
    });
    $('.dependent-table').on('click', '.btn-dependent-add', function(event)
    {
        console.log($(this).closest('.dependent-row'));
        var new_row = $(this).closest('.dependent-row').clone();
        new_row.find('input').val('');
        $('.dependent-table').append(new_row);
    });
    $('.dependent-table').on('click', '.btn-dependent-remove', function(event)
    {
        if ($('.dependent-table .dependent-row').length > 1) {
            $(this).closest('.dependent-row').remove();
        }
    });
});
