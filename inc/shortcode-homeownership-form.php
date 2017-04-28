<?php
// Shortcode for Homewonership Form
use Mailgun\Mailgun;
function homeownership_form_shortcode()
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
            'fname', 'lname', 'address1', 'city', 'state', 'zip', 'phone', 'email', 'military_service', 'home_locations', 'current_housing_needs', 'affordability', 'willingness_to_partner', 'g-recaptcha-response'
        );

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
                case 'fname':
                    $form_columns[$field] = 'First Name';
                    break;
                case 'lname':
                    $form_columns[$field] = 'Last Name';
                    break;
                case 'address1':
                    $form_columns[$field] = 'Address';
                    break;
                case 'address2':
                    $form_columns[$field] = 'Address 2';
                    break;
                case 'city':
                    $form_columns[$field] = 'City';
                    break;
                case 'state':
                    $form_columns[$field] = 'State';
                    break;
                case 'zip':
                    $form_columns[$field] = 'Zip Code';
                    break;
                case 'phone':
                    $form_columns[$field] = 'Phone';
                    break;
                case 'email':
                    $form_columns[$field] = 'Email';
                    break;
                case 'military_service':
                    $form_columns[$field] = 'Military Service';
                    break;
                case 'how_did_you_hear':
                    $form_columns[$field] = 'How did you hear about Habitat Oakland?';
                    break;
                case 'referrer_name':
                    $form_columns[$field] = 'Referring Homeowner Name';
                    break;
                case 'receive_updates':
                    $form_columns[$field] = 'I would like to receive updates';
                    break;
                case 'home_locations':
                    $form_columns[$field] = 'Possible Home Locations';
                    break;
                case 'current_housing_needs':
                    $form_columns[$field] = 'Current Housing Needs';
                    break;
                case 'affordability':
                    $form_columns[$field] = 'Affordability';
                    break;
                case 'willingness_to_partner':
                    $form_columns[$field] = 'Willingness to Partner';
                    break;
                default:
                    $form_columns[$field] = $field;
            }
        }
        //$result .= var_dump($form_columns);
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
            $result .= "<li>Please enter a valid e-mail address.</li>";
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
                do_action('ftd_insert_data','Homeownership Form', $form_data, $form_columns);
            }

            // Build the Email
            $htmlEmail = file_get_contents(get_template_directory_uri() . '/library/html/email.html');
            // Replace Logo
            $logo = get_template_directory_uri().'/library/images/habitatoakland-logo-email.jpg';
            if (getenv('APP_ENV') == 'local') {
                $logo = 'http://crandellhosting.com/images/habitatoakland-logo-email.jpg';
            }
            $htmlEmail = str_replace('../images/habitatoakland-logo-email.jpg', $logo, $htmlEmail);

            // Add Data from Form
            $formDataEmail = '<br><table style="color: #636466; font-family: \'Helvetica\' \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px;">
                    <tr>
                        <td valign="top">First Name: </td>
                        <td>' . $form_data['fname'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">Last Name: </td>
                        <td>' . $form_data['lname'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">Address: </td>
                        <td>' . $form_data['address1'] . '<br>' . $form_data['address2'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">City: </td>
                        <td>' . $form_data['city'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">State: </td>
                        <td>' . $form_data['state'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">Zip Code: </td>
                        <td>' . $form_data['zip'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">Phone: </td>
                        <td>' . $form_data['phone'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">Email: </td>
                        <td>' . $form_data['email'] . '</td>
                    </tr>
                    <tr>
                        <td valign="top">Military Service: </td>
                        <td>' . $form_data['military_service'] . '<br><br></td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="2">How did you hear about Habitat Oakland?</td>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['how_did_you_hear'] . '<br><br></td>
                    </tr>';
                if (strpos($form_data['how_did_you_hear'],'Habitat Homeowner') !== false) {
                    $formDataEmail .= '<tr>
                        <td valign="top" colspan="2">Referring Homeowner Name</td>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['referrer_name'] . '<br><br></td>
                    </tr>';
                }
                $formDataEmail .= '<tr>
                        <td valign="top" colspan="2">I would like to receive updates from Habitat Oakland</td>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['receive_updates'] . '<br><br></td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="2">Possible Home Locations</td>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['home_locations'] . '<br><br></td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="2">Current Housing Needs</td>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['current_housing_needs'] . '<br><br></td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="2">Affordability</td>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['affordability'] . '<br><br></td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="2">Willingness to Partner</td>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['willingness_to_partner'] . '<br><br></td>
                    </tr>
                </table>';
            $htmlEmail = str_replace('!*data*!', $formDataEmail, $htmlEmail);
            // Send the Email
            $message = $mgClient->sendMessage($domain, array(
                'from'    => 'Habitat for Humanity of Oakland County Website <postmaster@mailgun.habitatoakland.org>',
                'to'      => 'Matt Crandell <matt@crandelldesign.com>', // Use comma for 2nd email
                'subject' => 'New Form Entry: Homeownership Pre-Application Form',
                'text'    => 'Your mail do not support HTML',
                'html'    => $htmlEmail
            ));

            //Success
            header('Location: '.get_site_url().'/housingprograms-information/homeownership-program/homeownership-program-thank/');

            unset($form_data);
            unset($has_error);
        }
    }

    $start_div = '<div id="homeownership-form">';

    $info = '';

    if ($error == true) {
        $info = '<div class="alert alert-danger">Please correct the errors in red on the form.</div>';
    }
    /*if ($error == false && $result != '') {
        $info = '<div class="alert alert-success">' . $result . '</div>';
    }*/

    $email_form = '<form class="homeownership-form" action="' . get_permalink() . '" method="post">
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
        <h2>Possible Home Locations</h2>
        <p>Please select the areas below that would be a desired location for a future Habitat home. **Available home locations are subject to change at any time. Home locations are contingent on home/property donations and the available property in our community. There is never a guarantee that a home can be built in any area.</p>
        <div class="form-group' . ((isset($has_error['home_locations']) && $has_error['home_locations']) ? ' has-error' : '') . '">
            <label class="control-label">Choose Any*</label>
            ' . ((isset($has_error['home_locations']) && $has_error['home_locations']) ? '<span class="help-block">Please check a possible home location.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="home_locations[]" value="Pontiac" '.(isset($form_data) && (strpos($form_data['home_locations'],'Pontiac') !== false) ? 'checked' : '').'>
                    Pontiac
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="home_locations[]" value="Southfield" '.(isset($form_data) && (strpos($form_data['home_locations'],'Southfield') !== false) ? 'checked' : '').'>
                    Southfield
                </label>
            </div>
        </div>
        <h2>Situation Information</h2>
        <p>Please answer the questions below to provide Habitat with information about your current housing needs, your ability to pay for a future home, credit history and your willingness to partner with our organization.</p>
        <div class="form-group' . ((isset($has_error['current_housing_needs']) && $has_error['current_housing_needs']) ? ' has-error' : '') . '">
            <label class="control-label">Current Housing Needs*</label>
            ' . ((isset($has_error['current_housing_needs']) && $has_error['current_housing_needs']) ? '<span class="help-block">Please answer about your current housing situation.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="current_housing_needs[]" value="My current housing is overcrowded" '.(isset($form_data) && (strpos($form_data['current_housing_needs'],'My current housing is overcrowded') !== false) ? 'checked' : '').'>
                    My current housing is overcrowded
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="current_housing_needs[]" value="My current housing needs major repairs or is unsafe" '.(isset($form_data) && (strpos($form_data['current_housing_needs'],'My current housing needs major repairs or is unsafe') !== false) ? 'checked' : '').'>
                    My current housing needs major repairs or is unsafe
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="current_housing_needs[]" value="My current housing costs are greater than 35% of my monthly income" '.(isset($form_data) && (strpos($form_data['current_housing_needs'],'My current housing costs are greater than 35% of my monthly income') !== false) ? 'checked' : '').'>
                    My current housing costs are greater than 35% of my monthly income
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="current_housing_needs[]" value="I do not currently own another home" '.(isset($form_data) && (strpos($form_data['current_housing_needs'],'I do not currently own another home') !== false) ? 'checked' : '').'>
                    I do not currently own another home
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['affordability']) && $has_error['affordability']) ? ' has-error' : '') . '">
            <label class="control-label">Affordability*</label>
            ' . ((isset($has_error['affordability']) && $has_error['affordability']) ? '<span class="help-block">Please answer concerning affordability.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="affordability[]" value="I can pay a mortgage loan that is up to 30% of my gross household income" '.(isset($form_data) && (strpos($form_data['affordability'],'I can pay a mortgage loan that is up to 30% of my gross household income') !== false) ? 'checked' : '').'>
                    I can pay a mortgage loan that is up to 30% of my gross household income
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="affordability[]" value="I can save enough to cover the closing costs of a home purchase" '.(isset($form_data) && (strpos($form_data['affordability'],'I can save enough to cover the closing costs of a home purchase') !== false) ? 'checked' : '').'>
                    I can save enough to cover the closing costs of a home purchase
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['willingness_to_partner']) && $has_error['willingness_to_partner']) ? ' has-error' : '') . '">
            <label class="control-label">Willingness to Partner*</label>
            ' . ((isset($has_error['willingness_to_partner']) && $has_error['willingness_to_partner']) ? '<span class="help-block">Please answer about your willingness to partner.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I am willing to complete a minimum of 300 &quot;sweat equity&quot; hours during the program" '.(isset($form_data) && (strpos($form_data['willingness_to_partner'],'I am willing to complete a minimum of 300 &quot;sweat equity&quot; hours during the program') !== false) ? 'checked' : '').'>
                    I am willing to complete a minimum of 300 &quot;sweat equity&quot; hours during the program
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I am willing to participate in educational classes such as financial management, home maintenance and landscaping" '.(isset($form_data) && (strpos($form_data['willingness_to_partner'],'I am willing to participate in educational classes such as financial management, home maintenance and landscaping') !== false) ? 'checked' : '').'>
                    I am willing to participate in educational classes such as financial management, home maintenance and landscaping
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? ' has-error' : '') . '">
            <div class="g-recaptcha" data-sitekey="'.getenv('RECAPTCHA_SITEKEY').'"></div>
            ' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? '<span class="help-block">PCaptcha validation has failed.</span>' : '') . '
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-darkblue" type="submit">Next</button>
        </div>
    </form>';
    $close_div = '</div>';
    return $result . $start_div . $info . $email_form . $close_div;
}
add_shortcode('homeownership_form', 'homeownership_form_shortcode');
