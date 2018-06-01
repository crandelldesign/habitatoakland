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
    $('.military-service').on('click', function(event)
    {
        if ($(this).find('input').val() == 'I am a military veteran' || $(this).find('input').val() == 'I am the surviving spouse of a military veteran') {
            $('.military-branch-form-group').show();
        } else {
            $('.military-branch-form-group').hide();
        }
    });
    $('.i-am-homeowner').on('click', function(event)
    {
        if ($(this).find('input').val() == 'I am the legal owner of my home (my name is listed on the property deed)' && $(this).find('input').is(':checked')) {
            $('.date-home-purchased-form-group').show();
        } else {
            $('.date-home-purchased-form-group').hide();
        }
    });
    $('.i-am-renting').on('click', function(event) {
        if ($(this).find('input').val() == 'I am NOT the homeowner; I am currently renting the home' && $(this).find('input').is(':checked')) {
            $('.landlord-info-form-group').show();
        } else {
            $('.landlord-info-form-group').hide();
        }
    })
    $('.energy-audit').on('click', function(event)
    {
        if ($(this).find('input').val() == 'Energy Audit to identify savings' && $(this).find('input').is(':checked')) {
            $('.consumers-energy-account-number-form-group').show();
        } else {
            $('.consumers-energy-account-number-form-group').hide();
        }
    });
    $('.service-requested-other-click').on('click', function(event)
    {
        if ($(this).find('input').val() == 'Other' && $(this).find('input').is(':checked')) {
            $('.service-requested-other').show();
        } else {
            $('.service-requested-other').hide();
        }
    });
    $('.race-national-origin-other').on('click', function (event) {
        if ($(this).val() == 'Other' && $(this).is(':checked')) {
            $('.race-national-origin-explaination-form-group').show();
        } else {
            $('.race-national-origin-explaination-form-group').hide();
        }
    });
    $('select[name="race_national_origin"]').on('change', function (event) {
        if ($(this).val() == 'Other') {
            $('.race-national-origin-explaination-form-group').show();
        } else {
            $('.race-national-origin-explaination-form-group').hide();
        }
    });
    $('select[name="gender_identity"]').on('change', function (event) {
        if ($(this).val() == 'Other') {
            $('.gender-identity-explaination-form-group').show();
        } else {
            $('.gender-identity-explaination-form-group').hide();
        }
    });
    $('.gender-identity-other').on('click', function (event) {
        if ($(this).val() == 'Other' && $(this).is(':checked')) {
            $('.gender-identity-explaination-form-group').show();
        } else {
            $('.gender-identity-explaination-form-group').hide();
        }
    });
});
