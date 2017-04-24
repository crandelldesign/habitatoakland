jQuery(document).ready(function ($) {
    $('.habitat-homeowner-checkbox').on('change', function(event)
    {
        if ($(this).is(':checked')) {
            $('.referrer-name-form-group').show();
        } else {
            $('.referrer-name-form-group').hide();
        }
    });
});
