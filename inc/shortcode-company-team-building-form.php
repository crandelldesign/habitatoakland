<?php
// Shortcode for Company Team Building
use Mailgun\Mailgun;
function company_team_building_form_shortcode()
{
    // Mailgun Credentials Instantiate the client.
    $mgClient = new Mailgun(getenv('MAILGUN_KEY'));
    $domain = 'mailgun.habitatoakland.org';

    // Javascript
    wp_enqueue_script('company-team-building-form-js', get_stylesheet_directory_uri() . '/library/js/company-team-building-form.js');

    $error = false;
    $result = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required_fields = array(
            'company_name', 'contact_name', 'address', 'email', 'phone', 'number_employees', 'corporate_social_responsibility_program', 'number_employees_involved', 'preferred_time_frame', 'preferred_days'
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
                case 'contact_name':
                    $form_columns[$field] = 'Contact Name and Title';
                    break;
                case 'number_employees':
                    $form_columns[$field] = 'How many employees does your company have in Oakland County?';
                    break;
                case 'corporate_social_responsibility_program':
                    $form_columns[$field] = 'Does your company have a corporate social responsibility program?';
                    break;
                case 'social_responsibility_contact':
                    $form_columns[$field] = 'If yes, name of contact for social responsibility program';
                    break;
                case 'preferred_time_frame':
                    $form_columns[$field] = 'Preferred time-frame to participate';
                    break;
                case 'preferred_days':
                    $form_columns[$field] = 'Preferred days to participate';
                    break;
                default:
                    $form_columns[$field] = $field;
            }
        }
        // Check for required fields
        foreach ($required_fields as $required_field) {
            $value = !empty($form_data[$required_field])?trim($form_data[$required_field]):'';
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
                // Remove Unneeded Data
                unset($form_data['g-recaptcha-response']);
                unset($form_columns['g-recaptcha-response']);
                do_action('ftd_insert_data','Company Team Building', $form_data, $form_columns);
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
                        <th valign="top" align="left">Company Name: </th>
                        <td>' . $form_data['company_name'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Contact Name &amp; Title: </th>
                        <td>' . $form_data['contact_name'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Address: </th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['address'] . '<br></td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Email: </th>
                        <td>' . $form_data['email'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Phone: </th>
                        <td>' . $form_data['phone'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">How many employees does your company have in Oakland County?</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['number_employees'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Does your company have a corporate social responsibility program?</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['corporate_social_responsibility_program'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">If yes, name of contact for social responsibility program</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['social_responsibility_contact'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Preferred time-frame to participate?</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['preferred_time_frame'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Preferred days to participate?</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['preferred_days'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Comments/Questions</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['comments'] . '<br><br></td>
                    </tr>
                </table>';
            $htmlEmail = str_replace('!*data*!', $formDataEmail, $htmlEmail);
            // Send the Email
            $message = $mgClient->sendMessage($domain, array(
                'from'    => 'Habitat for Humanity of Oakland County Website <postmaster@mailgun.habitatoakland.org>',
                //'to'      => 'Matt Crandell <matt@crandelldesign.com>, Stephanie Osterland <stephanieo@habitatoakland.org>, Annabelle Wilkinson <annabellew@habitatoakland.org>', // Use comma for 2nd email
                'to'      => 'Matt Crandell <matt@crandelldesign.com>',
                'subject' => 'New Form Entry: Company Team Building Form',
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
                        <td>
                            Thank you, ' . $form_data['contact_name'] . '! We have received your Company Team Building application.<br>
                            We will get in touch with you shortly.
                        </td>
                    </tr>
                </table>';
            $htmlEmail = str_replace('!*data*!', $formDataEmail, $htmlEmail);
            $message = $mgClient->sendMessage($domain, array(
                'from'    => 'Annabelle Wilkinson <annabellew@habitatoakland.org>',
                'to'      => $form_data['contact_name'].' <'.$form_data['email'].'>', // Use comma for 2nd email
                'subject' => 'Thank You for Submitting Your Application for Company Team Building!',
                'text'    => 'Your mail does not support HTML',
                'html'    => $htmlEmail
            ));

            //Success
            header('Location: '.get_site_url().'/volunteer/company-team-building/company-team-building-thank/');

            unset($form_data);
            unset($has_error);
        }
    }
    $start_div = '<div id="company-team-building-form">';
    $info = '';

    if ($error == true) {
        $info = '<div class="alert alert-danger">Please correct the errors in red on the form.</div>';
    }
    $email_form = '<form class="company-team-building-form" action="' . get_permalink() . '#company-team-building-form" method="post">
        <h2>Team Building Inquiry Form</h2>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['company_name']) && $has_error['company_name']) ? ' has-error' : '') . '">
                    <label class="control-label">Company Name*</label>
                    <input type="text" name="company_name" class="form-control" placeholder="Company Name" value="' . (isset($form_data) ? $form_data['company_name'] : '') . '">
                    ' . ((isset($has_error['company_name']) && $has_error['company_name']) ? '<span class="help-block">Please fill out your company name.</span>' : '') . '
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['contact_name']) && $has_error['contact_name']) ? ' has-error' : '') . '">
                    <label class="control-label">Contact Name &amp; Title*</label>
                    <input type="text" name="contact_name" class="form-control" placeholder="Contact Name &amp; Title" value="' . (isset($form_data) ? $form_data['contact_name'] : '') . '">
                    ' . ((isset($has_error['contact_name']) && $has_error['contact_name']) ? '<span class="help-block">Please fill out your contact name and title.</span>' : '') . '
                </div>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['address']) && $has_error['address']) ? ' has-error' : '') . '">
            <label class="control-label">Address*</label>
            <textarea name="address" class="form-control" placeholder="Address" rows="4">' . (isset($form_data) ? $form_data['address'] : '') . '</textarea>
            ' . ((isset($has_error['address']) && $has_error['address']) ? '<span class="help-block">Please fill out your address.</span>' : '') . '
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['email']) && $has_error['email']) ? ' has-error' : '') . '">
                    <label class="control-label">Email Address*</label>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" value="' . (isset($form_data) ? $form_data['email'] : '') . '">
                    ' . ((isset($has_error['email']) && $has_error['email']) ? '<span class="help-block">Please fill out a valid email address.</span>' : '') . '
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['phone']) && $has_error['phone']) ? ' has-error' : '') . '">
                    <label class="control-label">Phone Number*</label>
                    <input type="text" name="phone" class="form-control" placeholder="Phone Number" value="' . (isset($form_data) ? $form_data['phone'] : '') . '">
                    ' . ((isset($has_error['phone']) && $has_error['phone']) ? '<span class="help-block">Please fill out your phone number.</span>' : '') . '
                </div>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['number_employees']) && $has_error['number_employees']) ? ' has-error' : '') . '">
            <label class="control-label">How many employees does your company have in Oakland County?*</label>
            ' . ((isset($has_error['number_employees']) && $has_error['number_employees']) ? '<span class="help-block">Please answer how many employees does your company have in Oakland County.</span>' : '') . '
            <div class="radio">
                <label>
                    <input type="radio" name="number_employees" value="0-50" '.(isset($form_data) && isset($form_data['number_employees']) && ($form_data['number_employees'] == '0-50') ? 'checked' : '').'>
                    0-50
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="number_employees" value="51-100" '.(isset($form_data) && isset($form_data['number_employees']) && ($form_data['number_employees'] == '51-100') ? 'checked' : '').'>
                    51-100
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="number_employees" value="101-500" '.(isset($form_data) && isset($form_data['number_employees']) && ($form_data['number_employees'] == '101-500') ? 'checked' : '').'>
                    101-500
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="number_employees" value="500+" '.(isset($form_data) && isset($form_data['number_employees']) && ($form_data['number_employees'] == '500+') ? 'checked' : '').'>
                    500+
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['corporate_social_responsibility_program']) && $has_error['corporate_social_responsibility_program']) ? ' has-error' : '') . '">
            <label class="control-label">Does your company have a corporate social responsibility program?*</label>
            ' . ((isset($has_error['corporate_social_responsibility_program']) && $has_error['corporate_social_responsibility_program']) ? '<span class="help-block">Please answer if your company has a corporate social responsibility program.</span>' : '') . '
            <div class="radio">
                <label>
                    <input type="radio" name="corporate_social_responsibility_program" value="Yes" '.(isset($form_data) && isset($form_data['corporate_social_responsibility_program']) && ($form_data['corporate_social_responsibility_program'] == 'Yes') ? 'checked' : '').'>
                    Yes
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="corporate_social_responsibility_program" value="No" '.(isset($form_data) && isset($form_data['corporate_social_responsibility_program']) && ($form_data['corporate_social_responsibility_program'] == 'No') ? 'checked' : '').'>
                    No
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">If yes, name of contact for social responsibility program</label>
            <input type="text" name="social_responsibility_contact" class="form-control" placeholder="If yes, name of contact for social responsibility program" value="' . (isset($form_data) ? $form_data['social_responsibility_contact'] : '') . '">
        </div>
        <div class="form-group' . ((isset($has_error['number_employees_involved']) && $has_error['number_employees_involved']) ? ' has-error' : '') . '">
            <label class="control-label">How many employees is your company hoping to involve in this Team Build Program?*</label>
            <input type="text" name="number_employees_involved" class="form-control" placeholder="Example: 25" value="' . (isset($form_data) ? $form_data['number_employees_involved'] : '') . '">
            ' . ((isset($has_error['number_employees_involved']) && $has_error['number_employees_involved']) ? '<span class="help-block">Please fill out how many employees is your company hoping to involve in this Team Build Program.</span>' : '') . '
        </div>
        <div class="form-group' . ((isset($has_error['preferred_time_frame']) && $has_error['preferred_time_frame']) ? ' has-error' : '') . '">
            <label class="control-label">Preferred time-frame to participate?*</label>
            ' . ((isset($has_error['preferred_time_frame']) && $has_error['preferred_time_frame']) ? '<span class="help-block">Please answer what time-frame you would like to participate.</span>' : '') . '
            <div class="radio">
                <label>
                    <input type="radio" name="preferred_time_frame" value="January-March" '.(isset($form_data) && isset($form_data['preferred_time_frame']) && ($form_data['preferred_time_frame'] == 'January-March') ? 'checked' : '').'>
                    January-March
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="preferred_time_frame" value="April-June" '.(isset($form_data) && isset($form_data['preferred_time_frame']) && ($form_data['preferred_time_frame'] == 'April-June') ? 'checked' : '').'>
                    April-June
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="preferred_time_frame" value="July-September" '.(isset($form_data) && isset($form_data['preferred_time_frame']) && ($form_data['preferred_time_frame'] == 'July-September') ? 'checked' : '').'>
                    July-September
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="preferred_time_frame" value="October-December" '.(isset($form_data) && isset($form_data['preferred_time_frame']) && ($form_data['preferred_time_frame'] == 'October-December') ? 'checked' : '').'>
                    October-December
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['preferred_days']) && $has_error['preferred_days']) ? ' has-error' : '') . '">
            <label class="control-label">Preferred days to participate?*</label>
            ' . ((isset($has_error['preferred_days']) && $has_error['preferred_days']) ? '<span class="help-block">Please answer what your preferred days to participate.</span>' : '') . '
            <div class="radio">
                <label>
                    <input type="radio" name="preferred_days" value="Weekdays" '.(isset($form_data) && isset($form_data['preferred_days']) && ($form_data['preferred_days'] == 'Weekdays') ? 'checked' : '').'>
                    Weekdays
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="preferred_days" value="Saturday" '.(isset($form_data) && isset($form_data['preferred_days']) && ($form_data['preferred_days'] == 'Saturday') ? 'checked' : '').'>
                    Saturday
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['comments']) && $has_error['comments']) ? ' has-error' : '') . '">
            <label class="control-label">Comments/Questions</label>
            <textarea name="comments" class="form-control" rows="5">' . (isset($form_data) ? $form_data['comments'] : '') . '</textarea>
        </div>
        <div class="form-group' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? ' has-error' : '') . '">
            <div class="g-recaptcha" data-sitekey="'.getenv('RECAPTCHA_SITEKEY').'"></div>
            ' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? '<span class="help-block">ReCaptcha validation has failed.</span>' : '') . '
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-darkblue" type="submit">Submit</button>
        </div>
    </form>';
    if ($error) {
        $email_form .= '
            <script>
                var form_error = true;
            </script>
        ';
    }
    $close_div = '</div>';
    return $start_div . $info . $email_form . $close_div;
}
add_shortcode('company_team_building_form', 'company_team_building_form_shortcode');
