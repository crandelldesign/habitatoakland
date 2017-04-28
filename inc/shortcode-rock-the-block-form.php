<?php
// Shortcode for the Rock the Block Form
use Mailgun\Mailgun;
function rock_the_block_form_shortcode()
{
    wp_enqueue_script('googlerecaptcha', 'https://www.google.com/recaptcha/api.js');
    wp_enqueue_script('homeownership-form-js', get_stylesheet_directory_uri() . '/library/js/homeownership-form.js');
    // Mailgun Credentials Instantiate the client.
    $mgClient = new Mailgun(getenv('MAILGUN_KEY'));
    $domain = 'mailgun.habitatoakland.org';
    $error   = false;
    $result  = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required_fields = array(
            'fname', 'lname', 'address1', 'city', 'state', 'zip', 'phone', 'email', 'military_service', 'homeownership', 'ability_to_pay', 'willingness_to_partner', 'authorization_and_release', 'g-recaptcha-response'
        );

        //error_log( print_r( $_POST, true ) );

        // Fetches everything that has been POSTed, sanitizes them and lets us use them as $form_data['field']
        foreach ($_POST as $field => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            $form_data[$field] = strip_tags($value);
            // Create Columns
            switch ($field) {
                case 'services_requested':
                    $form_columns[$field] = 'Home Repair Services Requested';
                    break;
                default:
                    $form_columns[$field] = $field;
            }
        }
        // Check for required fields
        foreach ($required_fields as $required_field) {
            $value = trim($form_data[$required_field]);
            if (empty($value)) {
                $error = true;
                $has_error[$required_field] = true;
            }
        }
        // and if the e-mail is not valid, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'
        if (!is_email($form_data['email']) && $error == false) {
            $error = true;
            $has_error['email'] = true;
        }
        // Verify Captcha
        $response = verifyCaptcha($_POST['g-recaptcha-response']);
        if (!$response->success) {
            $error = true;
            $has_error['recaptcha'] = true;
        }
        if ($error == false) {

            // Add Data to Database
            if(has_action('ftd_insert_data')) {
                do_action('ftd_insert_data','Rock the Block', $form_data, $form_columns);
            }

            // Build the Email
            $htmlEmail = file_get_contents(get_template_directory_uri() . '/library/html/email.html');
            // Replace Logo
            $logo = get_template_directory_uri().'/library/images/habitatoakland-logo-email.jpg';
            if (getenv('APP_ENV') == 'local') {
                $logo = 'http://crandellhosting.com/images/habitatoakland-logo-email.jpg';
            }
            $htmlEmail = str_replace('../images/habitatoakland-logo-email.jpg', $logo, $htmlEmail);

            $form_data['dependent_name'] = explode(', ', $form_data['dependent_name']);
            $form_data['dependent_dob'] = explode(', ', $form_data['dependent_dob']);

            // Add Data from Form
            $formDataEmail = '<br><table style="color: #636466; font-family: \'Helvetica\' \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px;">
                    <tr>
                        <th valign="top" align="left">First Name: </th>
                        <td>' . $form_data['fname'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Last Name: </th>
                        <td>' . $form_data['lname'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Address: </th>
                        <td>' . $form_data['address1'] . '<br>' . $form_data['address2'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">City: </th>
                        <td>' . $form_data['city'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">State: </th>
                        <td>' . $form_data['state'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Zip Code: </th>
                        <td>' . $form_data['zip'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Phone: </th>
                        <td>' . $form_data['phone'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Email: </th>
                        <td>' . $form_data['email'] . '</td>
                    </tr>';
                    $dependent_count = count($form_data['dependent_name']);
                    if ($dependent_count > 0) {
                        $formDataEmail .= '<tr>
                            <th valign="top" colspan="2">Dependents</th>
                        </tr>';
                    }
                    for ($i=0; $i < $dependent_count; $i++) {
                        $formDataEmail .= '<tr>
                            <td>'.$form_data['dependent_name'][$i].'</td>
                            <td>'.$form_data['dependent_dob'][$i].'</td>
                        </tr>';
                    }
                $formDataEmail .= '<tr>
                        <th valign="top">Military Service: </th>
                        <td>' . $form_data['military_service'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Household Disability Status: </th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['household_disablility_service'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">How did you hear about Habitat Oakland?</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['how_did_you_hear'] . '<br><br></td>
                    </tr>';
                if (strpos($form_data['how_did_you_hear'],'Habitat Homeowner') !== false) {
                    $formDataEmail .= '<tr>
                        <th valign="top" colspan="2">Referring Homeowner Name</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['referrer_name'] . '<br><br></td>
                    </tr>';
                }
                $formDataEmail .= '<tr>
                        <th valign="top" colspan="2">I would like to receive updates from Habitat Oakland</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['receive_updates'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">My home is in NEED of one or more of the following services</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['services_requested'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Homeownership</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['homeownership'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Ability to Pay</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['ability_to_pay'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Willingness to Partner</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['willingness_to_partner'] . '<br><br></td>
                    </tr>
                </table>';
            $formDataEmail .= '<br><table style="color: #636466; font-family: \'Helvetica\' \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px;">
                    <tr>
                        <th valign="top">Income Source</th>
                        <th valign="top">Pay Frequency</th>
                        <th valign="top">Amount per Check</th>
                    </tr>
                    <tr>
                        <td>' . $form_data['income_source_1'] . '</td>
                        <td>' . $form_data['income_frequency_1'] . '</td>
                        <td>' . $form_data['amount_per_check_1'] . '</td>
                    </tr>
                    <tr>
                        <td>' . $form_data['income_source_2'] . '</td>
                        <td>' . $form_data['income_frequency_2'] . '</td>
                        <td>' . $form_data['amount_per_check_2'] . '</td>
                    </tr>
                    <tr>
                        <td>' . $form_data['income_source_3'] . '</td>
                        <td>' . $form_data['income_frequency_3'] . '</td>
                        <td>' . $form_data['amount_per_check_3'] . '</td>
                    </tr>
                    <tr>
                        <td>' . $form_data['income_source_4'] . '</td>
                        <td>' . $form_data['income_frequency_4'] . '</td>
                        <td>' . $form_data['amount_per_check_4'] . '</td>
                    </tr>
                </table>';
            $htmlEmail = str_replace('!*data*!', $formDataEmail, $htmlEmail);
            // Send the Email
            $message = $mgClient->sendMessage($domain, array(
                'from'    => 'Habitat for Humanity of Oakland County Website <postmaster@mailgun.habitatoakland.org>',
                'to'      => 'Matt Crandell <matt@crandelldesign.com>', 'Stephanie Osterland <stephanieo@habitatoakland.org>', 'Micah Jordan <micahj@habitatoakland.org>', // Use comma for 2nd email
                'subject' => 'New Form Entry: Rock the Block Form',
                'text'    => 'Your mail does not support HTML',
                'html'    => $htmlEmail
            ));

            // Email to User
            // Build the Email
            $htmlEmail = file_get_contents(get_template_directory_uri() . '/library/html/email.html');
            // Replace Logo
            $logo = get_template_directory_uri().'/library/images/habitatoakland-logo-email.jpg';
            if (getenv('APP_ENV') == 'local') {
                $logo = 'http://crandellhosting.com/images/habitatoakland-logo-email.jpg';
            }
            $htmlEmail = str_replace('../images/habitatoakland-logo-email.jpg', $logo, $htmlEmail);
            $formDataEmail = '<br><table style="color: #636466; font-family: \'Helvetica\' \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px;">
                    <tr>
                        <td><strong>Thank you, ' . $form_data['fname'] . '! We have received your Rock the Block application for home repairs.</strong><br><br>
                            We have a high demand for this program so please allow 7-14 business days for processing. You will be contacted by Habitat Oakland about the status for your application and any next steps in the process if you are eligible.<br><br>
                            If you would like any further information about our Rock the Block, neighborhood revitalization program feel free to contact me.<br><br>
                            Call: 248-338-1843 x. 231<br><br>
                            All the best,<br>
                            Micah Jordan</td>
                    </tr>
                </table>';
            $htmlEmail = str_replace('!*data*!', $formDataEmail, $htmlEmail);
            $message = $mgClient->sendMessage($domain, array(
                'from'    => 'Micah Jordan <micahj@habitatoakland.org>',
                'to'      => $form_data['fname'].' <'.$form_data['email'].'>', // Use comma for 2nd email
                'subject' => 'Thank You for Submitting Your Application for Rock the Block!',
                'text'    => 'Your mail does not support HTML',
                'html'    => $htmlEmail
            ));

            //Success
            header('Location: '.get_site_url().'/housingprograms-information/rock-the-block/rock-block-application-thank/');

            unset($form_data);
            unset($has_error);
        }
    }
    $start_div = '<div id="golf-signup-form">';
    $info = '';

    if ($error == true) {
        $info = '<div class="alert alert-danger">Please correct the errors in red on the form.</div>';
    }

    $email_form = '<form class="golf-signup-form" action="' . get_permalink() . '" method="post">
        <h2>Contact Info</h2>
        <div class="form-section">
            <label>Name</label>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['fname']) && $has_error['fname']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">First*</label>
                        <input type="text" name="fname" class="form-control" placeholder="First Name" value="' . (isset($form_data) ? $form_data['fname'] : '') . '">
                        ' . ((isset($has_error['fname']) && $has_error['fname']) ? '<span class="help-block">Please fill out your first name.</span>' : '') . '
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['lname']) && $has_error['lname']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">Last*</label>
                        <input type="text" name="lname" class="form-control" placeholder="Last Name" value="' . (isset($form_data) ? $form_data['lname'] : '') . '">
                        ' . ((isset($has_error['lname']) && $has_error['lname']) ? '<span class="help-block">Please fill out your last name.</span>' : '') . '
                    </div>
                </div>
            </div>
        </div>
        <div class="form-section">
            <label>Address</label>
            <div class="form-group' . ((isset($has_error['address1']) && $has_error['address1']) ? ' has-error' : '') . '">
                <label class="sub-label control-label">Street Address*</label>
                <input type="text" name="address1" class="form-control" placeholder="Street Address" value="' . (isset($form_data) ? $form_data['address1'] : '') . '">
                ' . ((isset($has_error['address1']) && $has_error['address1']) ? '<span class="help-block">Please fill out your street address.</span>' : '') . '
                <label class="sub-label control-label">Address Line 2</label>
                <input type="text" name="address2" class="form-control" placeholder="Address Line 2" value="' . (isset($form_data) ? $form_data['address2'] : '') . '">
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['city']) && $has_error['city']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">City*</label>
                        <input type="text" name="city" class="form-control" placeholder="City" value="' . (isset($form_data) ? $form_data['city'] : '') . '">
                        ' . ((isset($has_error['city']) && $has_error['city']) ? '<span class="help-block">Please fill out your city.</span>' : '') . '
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['state']) && $has_error['state']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">State*</label>
                        <input type="text" name="state" class="form-control" placeholder="State" value="' . (isset($form_data) ? $form_data['state'] : '') . '">
                        ' . ((isset($has_error['state']) && $has_error['state']) ? '<span class="help-block">Please fill out your state.</span>' : '') . '
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['zip']) && $has_error['zip']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">Zip Code*</label>
                        <input type="text" name="zip" class="form-control" placeholder="Zip Code" value="' . (isset($form_data) ? $form_data['zip'] : '') . '">
                        ' . ((isset($has_error['zip']) && $has_error['zip']) ? '<span class="help-block">Please fill out your zip code.</span>' : '') . '
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['phone']) && $has_error['phone']) ? ' has-error' : '') . '">
                    <label control-label>Phone*</label>
                    <input type="text" name="phone" class="form-control" placeholder="Phone" value="' . (isset($form_data) ? $form_data['phone'] : '') . '">
                    ' . ((isset($has_error['phone']) && $has_error['phone']) ? '<span class="help-block">Please fill out your phone number.</span>' : '') . '
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['email']) && $has_error['email']) ? ' has-error' : '') . '">
                    <label class="control-label">Email*</label>
                    <input type="text" name="email" class="form-control" placeholder="Email" value="' . (isset($form_data) ? $form_data['email'] : '') . '">
                    ' . ((isset($has_error['email']) && $has_error['email']) ? '<span class="help-block">Please fill out a valid email address.</span>' : '') . '
                </div>
            </div>
        </div>
        <div class="form-section form-group margin-top-20">
            <label>Please list the name and date of birth for any additional household members/dependents</label>
            <table class="table no-border dependent-table">
                <thead>
                    <tr>
                        <th>Name(s)</th>
                        <th>Date of Birth(s)</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="dependent-row">
                        <td><input type="text" name="dependent_name[]" class="form-control" value=""></td>
                        <td><input type="text" name="dependent_dob[]" class="form-control"></td>
                        <td><div class="btn-group" role="group"><button type="button" class="btn btn-darkblue btn-sm btn-dependent-add"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-darkblue btn-sm btn-dependent-remove"><i class="fa fa-minus" aria-hidden="true"></i></button></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group' . ((isset($has_error['military_service']) && $has_error['military_service']) ? ' has-error' : '') . '">
            <label class="control-label">Military Service*</label>
            ' . ((isset($has_error['military_service']) && $has_error['military_service']) ? '<span class="help-block">Please check if you or your spouse served in the military.</span>' : '') . '
            <div class="radio">
                <label>
                    <input type="radio" name="military_service" value="I am a military veteran" '.(isset($form_data) && ($form_data['military_service'] == 'I am a military veteran') ? 'checked' : '').'>
                    I am a military veteran
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="military_service" value="I am the surviving spouse of a military veteran" '.(isset($form_data) && ($form_data['military_service'] == 'I am the surviving spouse of a military veteran') ? 'checked' : '').'>
                    I am the surviving spouse of a military veteran
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="military_service" value="I am NOT a military veteran" '.(isset($form_data) && ($form_data['military_service'] == 'I am NOT a military veteran') ? 'checked' : '').'>
                    I am NOT a military veteran
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['household_disablility_service']) && $has_error['household_disablility_service']) ? ' has-error' : '') . '">
            <label class="control-label">Household Disability Status</label>
            <div class="radio">
                <label>
                    <input type="radio" name="household_disablility_service" value="One or more household members has a disability" '.(isset($form_data) && ($form_data['household_disablility_service'] == 'One or more household members has a disability') ? 'checked' : '').'>
                    One or more household members has a disability
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="household_disablility_service" value="N/A" '.(isset($form_data) && ($form_data['household_disablility_service'] == 'N/A') ? 'checked' : '').'>
                    N/A
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['how_did_you_hear']) && $has_error['how_did_you_hear']) ? ' has-error' : '') . '">
            <label class="control-label">How did you hear about Habitat Oakland?</label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="A sign in front of a home to be built or renovated" '.(isset($form_data) && (strpos($form_data['how_did_you_hear'],'A sign in front of a home to be built or renovated') !== false) ? 'checked' : '').'>
                    A sign in front of a home to be built or renovated
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="A real estate website or agent" '.(isset($form_data) && (strpos($form_data['how_did_you_hear'],'A real estate website or agent') !== false) ? 'checked' : '').'>
                    A real estate website or agent
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" class="habitat-homeowner-checkbox" value="Habitat Homeowner" '.(isset($form_data) && (strpos($form_data['how_did_you_hear'],'Habitat Homeowner') !== false) ? 'checked' : '').'>
                    Habitat Homeowner
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="Friend or family" '.(isset($form_data) && (strpos($form_data['how_did_you_hear'],'Friend or family') !== false) ? 'checked' : '').'>
                    Friend or family
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="Another organization" '.(isset($form_data) && (strpos($form_data['how_did_you_hear'],'Another organization') !== false) ? 'checked' : '').'>
                    Another organization
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="None of the above" '.(isset($form_data) && (strpos($form_data['how_did_you_hear'],'None of the above') !== false) ? 'checked' : '').'>
                    None of the above
                </label>
            </div>
        </div>
        <div class="form-group referrer-name-form-group" style="display:none">
            <label class="control-label">Referring Homeowner Name</label>
            <input type="text" name="referrer_name" class="form-control" placeholder="Referring Homeowner Name" value="' . (isset($form_data) ? $form_data['referrer_name'] : '') . '">
        </div>
        <div class="form-group' . ((isset($has_error['receive_updates']) && $has_error['receive_updates']) ? ' has-error' : '') . '">
            <label class="control-label">I would like to receive updates from Habitat Oakland</label>
            <div class="radio">
                <label>
                    <input type="radio" name="receive_updates" value="Yes" '.(isset($form_data) && ($form_data['receive_updates'] == 'Yes') ? 'checked' : '').'>
                    Yes
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="receive_updates" value="No" '.(isset($form_data) && ($form_data['receive_updates'] == 'No') ? 'checked' : '').'>
                    No
                </label>
            </div>
        </div>
        <h2>Rock the Block - Home Repair Service Available</h2>
        <div class="form-group' . ((isset($has_error['services_requested']) && $has_error['services_requested']) ? ' has-error' : '') . '">
            <label class="control-label">My home is in NEED of one or more of the following services*<br><small>* Services also available to renters</br></label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Energy Audit to identify savings" '.(isset($form_data) && (strpos($form_data['services_requested'],'Energy Audit to identify savings') !== false) ? 'checked' : '').'>
                    Energy Audit to identify savings
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Landscape: Rake, edge, trim hedges, level landscape blocks" '.(isset($form_data) && (strpos($form_data['services_requested'],'Landscape: Rake, edge, trim hedges, level landscape blocks') !== false) ? 'checked' : '').'>
                    Landscape: Rake, edge, trim hedges, level landscape blocks
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Gutter Cleaning, install Gutter Guards" '.(isset($form_data) && (strpos($form_data['services_requested'],'Gutter Cleaning, install Gutter Guards') !== false) ? 'checked' : '').'>
                    Gutter Cleaning, install Gutter Guards
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Exterior of Home Painting" '.(isset($form_data) && (strpos($form_data['services_requested'],'Exterior of Home Painting') !== false) ? 'checked' : '').'>
                    Exterior of Home Painting
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Wood porch, steps or fence repair (does not include paint/stain)" '.(isset($form_data) && (strpos($form_data['services_requested'],'Wood porch, steps or fence repair (does not include paint/stain)') !== false) ? 'checked' : '').'>
                    Wood porch, steps or fence repair (does not include paint/stain)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Wood porch or fence painting/stain" '.(isset($form_data) && (strpos($form_data['services_requested'],'Wood porch or fence painting/stain') !== false) ? 'checked' : '').'>
                    Wood porch or fence painting/stain
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Dumpster Ditch Day" '.(isset($form_data) && (strpos($form_data['services_requested'],'Dumpster Ditch Day') !== false) ? 'checked' : '').'>
                    Dumpster Ditch Day
                </label>
            </div>
        </div>
        <h2>Please Check All That Apply</h2>
        <p>Please answer the questions below to provide Habitat with information about your current housing needs, your ability to pay for a future home, credit history and your willingness to partner with our organization.</p>
        <div class="form-group' . ((isset($has_error['homeownership']) && $has_error['homeownership']) ? ' has-error' : '') . '">
            <label class="control-label">Homeownership*</label>
            ' . ((isset($has_error['homeownership']) && $has_error['homeownership']) ? '<span class="help-block">Please answer about your homeownership.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="homeownership[]" value="I am the legal owner of my home (my name is listed on the property deed)" '.(isset($form_data) && (strpos($form_data['homeownership'],'I am the legal owner of my home (my name is listed on the property deed)') !== false) ? 'checked' : '').'>
                    I am the legal owner of my home (my name is listed on the property deed)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="homeownership[]" value="I am NOT the homeowner; I am currently renting the home" '.(isset($form_data) && (strpos($form_data['homeownership'],'I am NOT the homeowner; I am currently renting the home') !== false) ? 'checked' : '').'>
                    I am NOT the homeowner; I am currently renting the home
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="homeownership[]" value="This home is my primary residence" '.(isset($form_data) && (strpos($form_data['homeownership'],'This home is my primary residence') !== false) ? 'checked' : '').'>
                    This home is my primary residence
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['ability_to_pay']) && $has_error['ability_to_pay']) ? ' has-error' : '') . '">
            <label class="control-label">Ability to Pay*</label>
            ' . ((isset($has_error['ability_to_pay']) && $has_error['ability_to_pay']) ? '<span class="help-block">Please answer concerning your ability to pay.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="ability_to_pay[]" value="I understand that Habitat will charge a reasonable fee for the services I am approved for" '.(isset($form_data) && (strpos($form_data['ability_to_pay'],'I understand that Habitat will charge a reasonable fee for the services I am approved for') !== false) ? 'checked' : '').'>
                    I understand that Habitat will charge a reasonable fee for the services I am approved for
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="ability_to_pay[]" value="My fee will be reviewed with me and agreed upon prior to project approval" '.(isset($form_data) && (strpos($form_data['ability_to_pay'],'My fee will be reviewed with me and agreed upon prior to project approval') !== false) ? 'checked' : '').'>
                    My fee will be reviewed with me and agreed upon prior to project approval
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="ability_to_pay[]" value="My fee is due and shall be paid in full prior to the work day on my home" '.(isset($form_data) && (strpos($form_data['ability_to_pay'],'My fee is due and shall be paid in full prior to the work day on my home') !== false) ? 'checked' : '').'>
                    My fee is due and shall be paid in full prior to the work day on my home
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['willingness_to_partner']) && $has_error['willingness_to_partner']) ? ' has-error' : '') . '">
            <label class="control-label">Willingness to Partner*</label>
            ' . ((isset($has_error['willingness_to_partner']) && $has_error['willingness_to_partner']) ? '<span class="help-block">Please answer about your willingness to partner.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I will be available and participate in work done at my home alongside Habitat volunteers when scheduled" '.(isset($form_data) && (strpos($form_data['willingness_to_partner'],'I will be available and participate in work done at my home alongside Habitat volunteers when scheduled') !== false) ? 'checked' : '').'>
                    I will be available and participate in work done at my home alongside Habitat volunteers when scheduled
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I will put in the required number of sweat equity hours for my project (4-8 hours)" '.(isset($form_data) && (strpos($form_data['willingness_to_partner'],'I will put in the required number of sweat equity hours for my project (4-8 hours)') !== false) ? 'checked' : '').'>
                    I will put in the required number of sweat equity hours for my project (4-8 hours)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I am willing to display partnership materials (lawn sign, stencil)" '.(isset($form_data) && (strpos($form_data['willingness_to_partner'],'I am willing to display partnership materials (lawn sign, stencil)') !== false) ? 'checked' : '').'>
                    I am willing to display partnership materials (lawn sign, stencil)
                </label>
            </div>
        </div>
        <h2>Financial Information</h2>
        <p>Please select the sources of income for your household. Ex: Job, Social Security, Child Support, etc. Please list the amounts BEFORE taxes.</p>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_1" class="form-control">
                        <option value="Job" '.(isset($form_data) && ($form_data['income_source_1'] == 'Job') ? 'selected' : '').'>Job</option>
                        <option value="Social Security" '.(isset($form_data) && ($form_data['income_source_1'] == 'Social Security') ? 'selected' : '').'>Social Security</option>
                        <option value="Child Support" '.(isset($form_data) && ($form_data['income_source_1'] == 'Child Support') ? 'selected' : '').'>Child Support</option>
                        <option value="Disability" '.(isset($form_data) && ($form_data['income_source_1'] == 'Disability') ? 'selected' : '').'>Disability</option>
                        <option value="Other" '.(isset($form_data) && ($form_data['income_source_1'] == 'Other') ? 'selected' : '').'>Other</option>
                        <option value="N/A" '.(isset($form_data) && ($form_data['income_source_1'] == 'N/A') ? 'selected' : '').'>N/A</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">How Often Are You Paid?</label><br>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_1" value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_1'] == 'Weekly') ? 'checked' : '').'>
                        Weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_1" value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_1'] == 'Bi-weekly') ? 'checked' : '').'>
                        Bi-weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_1" value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_1'] == 'Monthly') ? 'checked' : '').'>
                        Monthly
                    </label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Amount Per Check</label>
                    <input type="text" name="amount_per_check_1" class="form-control" placeholder="$" value="' . (isset($form_data) ? $form_data['amount_per_check_1'] : '') . '">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_2" class="form-control">
                        <option value="Job" '.(isset($form_data) && ($form_data['income_source_2'] == 'Job') ? 'selected' : '').'>Job</option>
                        <option value="Social Security" '.(isset($form_data) && ($form_data['income_source_2'] == 'Social Security') ? 'selected' : '').'>Social Security</option>
                        <option value="Child Support" '.(isset($form_data) && ($form_data['income_source_2'] == 'Child Support') ? 'selected' : '').'>Child Support</option>
                        <option value="Disability" '.(isset($form_data) && ($form_data['income_source_2'] == 'Disability') ? 'selected' : '').'>Disability</option>
                        <option value="Other" '.(isset($form_data) && ($form_data['income_source_2'] == 'Other') ? 'selected' : '').'>Other</option>
                        <option value="N/A" '.(isset($form_data) && ($form_data['income_source_2'] == 'N/A') ? 'selected' : '').'>N/A</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">How Often Are You Paid?</label><br>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_2" value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_2'] == 'Weekly') ? 'checked' : '').'>
                        Weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_2" value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_2'] == 'Bi-weekly') ? 'checked' : '').'>
                        Bi-weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_2" value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_2'] == 'Monthly') ? 'checked' : '').'>
                        Monthly
                    </label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Amount Per Check</label>
                    <input type="text" name="amount_per_check_2" class="form-control" placeholder="$" value="' . (isset($form_data) ? $form_data['amount_per_check_2'] : '') . '">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_3" class="form-control">
                        <option value="Job" '.(isset($form_data) && ($form_data['income_source_3'] == 'Job') ? 'selected' : '').'>Job</option>
                        <option value="Social Security" '.(isset($form_data) && ($form_data['income_source_3'] == 'Social Security') ? 'selected' : '').'>Social Security</option>
                        <option value="Child Support" '.(isset($form_data) && ($form_data['income_source_3'] == 'Child Support') ? 'selected' : '').'>Child Support</option>
                        <option value="Disability" '.(isset($form_data) && ($form_data['income_source_3'] == 'Disability') ? 'selected' : '').'>Disability</option>
                        <option value="Other" '.(isset($form_data) && ($form_data['income_source_3'] == 'Other') ? 'selected' : '').'>Other</option>
                        <option value="N/A" '.(isset($form_data) && ($form_data['income_source_3'] == 'N/A') ? 'selected' : '').'>N/A</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">How Often Are You Paid?</label><br>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_3" value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_3'] == 'Weekly') ? 'checked' : '').'>
                        Weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_3" value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_3'] == 'Bi-weekly') ? 'checked' : '').'>
                        Bi-weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_3" value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_3'] == 'Monthly') ? 'checked' : '').'>
                        Monthly
                    </label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Amount Per Check</label>
                    <input type="text" name="amount_per_check_3" class="form-control" placeholder="$" value="' . (isset($form_data) ? $form_data['amount_per_check_3'] : '') . '">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_4" class="form-control">
                        <option value="Job" '.(isset($form_data) && ($form_data['income_source_4'] == 'Job') ? 'selected' : '').'>Job</option>
                        <option value="Social Security" '.(isset($form_data) && ($form_data['income_source_4'] == 'Social Security') ? 'selected' : '').'>Social Security</option>
                        <option value="Child Support" '.(isset($form_data) && ($form_data['income_source_4'] == 'Child Support') ? 'selected' : '').'>Child Support</option>
                        <option value="Disability" '.(isset($form_data) && ($form_data['income_source_4'] == 'Disability') ? 'selected' : '').'>Disability</option>
                        <option value="Other" '.(isset($form_data) && ($form_data['income_source_4'] == 'Other') ? 'selected' : '').'>Other</option>
                        <option value="N/A" '.(isset($form_data) && ($form_data['income_source_4'] == 'N/A') ? 'selected' : '').'>N/A</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">How Often Are You Paid?</label><br>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_4" value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_4'] == 'Weekly') ? 'checked' : '').'>
                        Weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_4" value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_4'] == 'Bi-weekly') ? 'checked' : '').'>
                        Bi-weekly
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="income_frequency_4" value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_4'] == 'Monthly') ? 'checked' : '').'>
                        Monthly
                    </label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Amount Per Check</label>
                    <input type="text" name="amount_per_check_4" class="form-control" placeholder="$" value="' . (isset($form_data) ? $form_data['amount_per_check_4'] : '') . '">
                </div>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['authorization_and_release']) && $has_error['authorization_and_release']) ? ' has-error' : '') . '">
            <label class="control-label">Authorization & Release*</label>
            <span class="help-block">I understand that by submitting this pre-screening form, I am authorizing Habitat for Humanity of Oakland County (HFHOC) to evaluate my eligibility for home preservation services. I also understand that an evaluation will include a criminal background and sexual offender check. I have answered all the questions on this form truthfully. The original or a copy of this form will be retained by HFHOC even if the application is not approved. HFHOC will not share the information on this application form with any outside agency and your name(s) will not be put on any mailing lists outside this agency.</span>
            ' . ((isset($has_error['authorization_and_release']) && $has_error['authorization_and_release']) ? '<span class="help-block">Please accept the agreement.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="authorization_and_release" value="Agree" '.(isset($form_data) && (strpos($form_data['authorization_and_release'],'Agree') !== false) ? 'checked' : '').'>
                    Agree
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? ' has-error' : '') . '">
            <div class="g-recaptcha" data-sitekey="'.getenv('RECAPTCHA_SITEKEY').'"></div>
            ' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? '<span class="help-block">ReCaptcha validation has failed.</span>' : '') . '
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-darkblue" type="submit">Next</button>
        </div>
    </form>';
    $close_div = '</div>';
    return $result . $start_div . $info . $email_form . $close_div;
}
add_shortcode('rock_the_block_form', 'rock_the_block_form_shortcode');
