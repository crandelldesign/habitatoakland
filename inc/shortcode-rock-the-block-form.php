<?php
// Shortcode for the Rock the Block Form
use Mailgun\Mailgun;
function rock_the_block_form_shortcode($atts = array())
{
    if ( ! empty( $atts['event'] ) ) {
        $event = $atts['event'];
    } elseif (isset($_GET['event'])) {
        $event = $_GET['event'];
    } elseif (isset($_POST['event'])) {
        $event = $_POST['event'];
    } else {
        $event = '2017-06-20';
    }

    /* Notes: Set up day before cut off dates */

    switch ($event) {
        case '2018-09-11':
            // Bonnie Acres 2018
            $event_name = 'Bonnie Acres Neighborhood';
            $start_date = '2018-09-11';
            $end_date = '2018-09-12';
            $street_names = array('W 12 Mile Rd', 'Greenfield Rd', 'Brentwood St', 'Marshal St', 'Fairfax St', 'Everett St', 'Selkirk St', 'Aberdeen St', 'Sutherland St', 'Pierce St', 'Glasglow St', 'Stuart Ave', 'Red Leaf Ln', 'Wiltshire Blvd', 'San Rosa Ave', 'Roseland Blvd', 'Spring Arbor Dr', 'Avilla Blvd', 'Redwood Ave', 'Glenwood Blvd', 'E Goldengate Dr', 'Catalpa Dr', 'Lathrup Blvd', 'Lexington Pkwy', 'Shaqbark Dr', 'San Quentin Dr');
            $city = 'Southfield';
            $zip_code = '48076';
            break;
        case '2018-08-01':
            // Southwest Evergreen 2018
            $event_name = 'Southwest Evergreen Neighborhood';
            $start_date = '2018-08-01';
            $end_date = '2018-08-02';
            $street_names = array('Prescott St', 'Mahon Dr', 'Wallace Rd', 'Frazer Ave', 'Lahser Rd', 'Mada Ave', 'Bentler St', '8 1/2 Mile Rd', 'Rose Hollow Dr', 'Tapert Dr', 'Secluded Ln', 'Melrose St', 'Westland Dr', 'Westover Ave', 'Westhampton Ave', 'Tapert Ave', 'Macauley St', 'Westhave Ave', '8 Mile Rd', 'Evergreen Rd', 'Midway Ave');
            $city = 'Southfield';
            $zip_code = '48075';
            break;
        case '2018-06-13':
            // Pontiac 2018
            $event_name = 'Unity Park Neighborhood';
            $start_date = '2018-06-13';
            $end_date = '2018-06-14';
            $street_names = array('Anderson','Collingwood','Cottage','Edith','Going','Marshall','Osmun','Shirley','Wall','Willard');
            $city = 'Pontiac';
            $zip_code = '48342';
            break;
        case '2018-05-16':
            // Pontiac 2018
            $event_name = 'Ferry Farms Neighborhood';
            $start_date = '2018-05-16';
            $end_date = '2018-05-17';
            $street_names = array('Baxter','Central','Edith','Elm','Ferry','Going','Irwin','Jesse','Midway','Paddock','Prospect','Raeburn','Sanford','Wilson');
            $city = 'Pontiac';
            $zip_code = '48341';
            break;
        case '2017-07-25':
            // Pontiac
            $event_name = 'Unity Park Neighborhood';
            $start_date = '2017-07-25';
            $end_date = '2017-07-26';
            $street_names = array('Going St', 'S Edith St', 'South Anderson St', 'South Marshall St');
            $city = 'Pontiac';
            $zip_code = '48342';
            break;
        case '2017-09-11':
            // Southfield
            $event_name = 'Bonnie Acres Neighborhood';
            $start_date = '2017-09-11';
            $end_date = '2017-09-12';
            $street_names = array('Brentwood St', 'Marshall St', 'Fairfax St', 'Everett St', 'Selkirk St', 'Santa Rosa Ave', 'Wiltshire Blvd', 'Stuart', 'Glasgow', 'Pierce', 'Sutherland', 'Aberdeen', 'Catalpa');
            $city = 'Southfield';
            $zip_code = '48076';
            break;
        default:
            // Orginal event = 2017-06-20
            // Pontiac
            $event_name = 'Ferry Farms Neighborhood';
            $start_date = '2017-06-20';
            $end_date = '2017-06-21';
            $street_names = array('Ferry', 'Wilson', 'Midway', 'Central', 'Prospect', 'Edith Ave', 'Going St');
            $city = 'Pontiac';
            $zip_code = '48341';
            break;
    }

    //wp_enqueue_script('googlerecaptcha', 'https://www.google.com/recaptcha/api.js');
    wp_enqueue_script('homeownership-form-js', get_stylesheet_directory_uri() . '/library/js/homeownership-form.js');
    // Mailgun Credentials Instantiate the client.
    $mgClient = new Mailgun(getenv('MAILGUN_KEY'));
    $domain = 'mailgun.habitatoakland.org';
    $error   = false;
    $result  = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required_fields = array(
            'fname', 'lname', 'street_number', 'street', 'city', 'state', 'zip', 'phone', 'military_service', 'homeownership', 'ability_to_pay', 'willingness_to_partner', 'authorization_and_release', 'g-recaptcha-response', 'signature', 'dob', 'years_in_home', 'how_did_you_hear', 'income_source_1', 'income_frequency_1', 'amount_per_check_1', 'additional_agreements', 'privacy_policy'
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
                case 'services_requested':
                    $form_columns[$field] = 'Home Repair Services Requested';
                    break;
                default:
                    $form_columns[$field] = $field;
            }
        }
        //error_log( print_r( $form_data['military_service'], true ) );
        // Check for required fields
        foreach ($required_fields as $required_field) {
            $value = !empty($form_data[$required_field])?trim($form_data[$required_field]):'';
            if (empty($value)) {
                $error = true;
                $has_error[$required_field] = true;
            }
        }
        //error_log( print_r( count($_POST['additional_agreements']), true ) );

        // Check if all 3 checkboxes were checked
        if (count($_POST['additional_agreements']) < 3) {
            $error = true;
            $has_error['additional_agreements'] = true;
        }

        // and if the e-mail is not valid, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'
        if (!is_email($form_data['email']) && !empty($form_data['email'])) {
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
                unset($form_data['event']);
                unset($form_columns['event']);
                do_action('ftd_insert_data','Rock the Block '.$event, $form_data, $form_columns);
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
            $formDataEmail = '<br><table style="color: #636466; font-family: \'Helvetica\' \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px;">';
            $formDataEmail .= '
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
                        <td>' . $form_data['street_number'] . ' ' . $form_data['street'] . '</td>
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
                    </tr>
                    <tr>
                        <th valign="top" align="left">Date of Birth: </th>
                        <td>' . $form_data['dob'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Information for Government Monitoring Purposes - Opt Out: </th>
                        <td>' . $form_data['government_opt_out'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Race/National Origin: </th>
                        <td>' . $form_data['race_national_origin'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Race/National Origin Explanation: </th>
                        <td>' . $form_data['race_national_origin_explaination'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Ethnicity: </th>
                        <td>' . $form_data['ethnicity'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Gender Identity: </th>
                        <td>' . $form_data['gender_identity'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" align="left">Gender Identity Explanation: </th>
                        <td>' . $form_data['gender_identity_explaination'] . '</td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">How Long Have You Lived in Your Home?</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['years_in_home'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top">Adults in household:</th>
                        <td>' . $form_data['adults_in_household'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top">Children in household:</th>
                        <td>' . $form_data['children_in_household'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top">Military Service: </th>
                        <td>' . $form_data['military_service'] . '<br><br></td>
                    </tr>';
            if ($form_data['military_branch'] != '') {
                $formDataEmail .= '<tr>
                    <th valign="top" colspan="2">Which Branch of the Military?</th>
                </tr>
                <tr>
                    <td colspan="2">' . $form_data['military_branch'] . '<br><br></td>
                </tr>';
            }
                /*$dependent_count = count($form_data['dependent_name']);
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
                }*/
            $formDataEmail .= '<tr>
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
                        <th valign="top" colspan="2">My home is in NEED of one or more of the following services</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['services_requested'] . '<br><br></td>
                    </tr>';
            if ($form_data['consumers_energy_account_number'] != '') {
                $formDataEmail .= '<tr>
                        <th valign="top colspan="2">What is Your Consumer Energy Account Number?</th>
                    </tr><tr>
                        <td colspan="2">' . $form_data['consumers_energy_account_number'] . '<br><br></td>
                    </tr>';
            }
            $formDataEmail .= '<tr>
                        <th valign="top" colspan="2">Homeownership</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['homeownership'] . '<br><br></td>
                    </tr>';
            if ($form_data['date_home_purchased'] != '') {
                $formDataEmail .= '<tr>
                        <th valign="top" colspan="2">What Date Did You Purchased Your Home?</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['date_home_purchased'] . '<br><br></td>
                    </tr>';
            }
            if ($form_data['landlord_name'] != '') {
                $formDataEmail .= '<tr>
                        <th valign="top" colspan="2">Landlord Name</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['landlord_name'] . '<br><br></td>
                    </tr>
                    <tr>
                        <th valign="top" colspan="2">Landlord Phone or Email</th>
                    </tr>
                    <tr>
                        <td colspan="2">' . $form_data['landlord_phone_email'] . '<br><br></td>
                    </tr>';
            }
            $formDataEmail .= '<tr>
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
                'to'      => 'Stephanie Osterland <stephanieo@habitatoakland.org>, Micah Jordan <micahj@habitatoakland.org>, Victoria Cantarella <victoriac@habitatoakland.org>', // Use comma for 2nd email
                'bcc' => 'Matt Crandell <matt@crandelldesign.com>',
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


    $email_form = '<div id="rock-the-block-form">';
    $email_form .= '
        <p>Please complete the form below if you are interested in participating in our Rock the Block program and having repairs completed on your home. Some services are still available for renters.</p>
        <p>Applications are currently being accepted for the '.date('F jS',strtotime($start_date)).' and '.date('jS',strtotime($end_date)).' Rock the Block event in the '.$event_name.', '.$city.'. If you live outside of this area and the identified street boundaries of '.join(' or ', array_filter(array_merge(array(join(', ', array_slice($street_names, 0, -1))), array_slice($street_names, -1)), 'strlen')).' you will need to wait to apply at a later time if your neighborhood has been selected for a Rock the Block event.</p>';
    $email_form .= '<form class="rock-the-block-form" action="' . get_permalink() . '" method="post">';
/* 
    if ($event_name == 'Southwest Evergreen Neighborhood') {
        $email_form .= '
        <hr>
        <p>Join Habitat Oakland for a Community Meet & Eat event! Join us for lunch and to meet Habitat Oakland staff, learn about Habitat programs and opportunities in your neighborhood.<br>
        <small>*lunch provided for those that RSVP</small></p>
        <p><strong>Date:</strong> Saturday June 23rd 12pm – 2pm<br>
            <strong>Location:</strong> St. Mary the Protectress Ukrainian Church<br>
            &nbsp;&nbsp;&nbsp;21931 Evergreen Rd, Southfield, MI 48075</p>
        <div class="form-group' . ((isset($has_error['event_rsvp']) && $has_error['event_rsvp']) ? ' has-error' : '') . '">
            <label class="control-label">RSVP</label>&nbsp;&nbsp;&nbsp;
            <label class="radio-inline">
                <input type="radio" name="event_rsvp" value="Yes" '.(isset($form_data) && isset($form_data['event_rsvp']) && ($form_data['event_rsvp'] == 'Yes') ? 'checked' : '').'> Yes
            </label>
            <label class="radio-inline">
                <input type="radio" name="event_rsvp" value="No" '.(isset($form_data) && isset($form_data['event_rsvp']) && ($form_data['event_rsvp'] == 'No') ? 'checked' : '').'> No
            </label>
            ' . ((isset($has_error['event_rsvp']) && $has_error['event_rsvp']) ? '<span class="help-block">Please RSVP yes or no.</span>' : '') . '
        </div>
        
        ';
        
    } */

    $email_form .= '
        <h2>'.$event_name.'</h2>
        <h3>'.date('l, F jS, Y',strtotime($start_date)).' - '.date('l, F jS, Y',strtotime($end_date)).'</h3>
        ';

    if ($error == true) {
        $email_form .= '<div class="alert alert-danger">Please correct the errors in red on the form.</div>';
    }
    
    $email_form .= '
        <h2>Contact Information</h2>
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
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['street_number']) && $has_error['street_number']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">Street Number*</label>
                        <input type="text" name="street_number" class="form-control" placeholder="Street Number" value="' . (isset($form_data) ? $form_data['street_number'] : '') . '">
                        ' . ((isset($has_error['street_number']) && $has_error['street_number']) ? '<span class="help-block">Please fill out your street number.</span>' : '') . '
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['street']) && $has_error['street']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">Street Name*</label>
                        <select type="text" name="street" class="form-control">
                        <option value="">Choose One</option>';
    for($i = 0; $i < count($street_names); $i++) {
         $email_form .= '<option value="'.$street_names[$i].'" '.(isset($form_data) && ($form_data['street'] == $street_names[$i]) ? 'selected' : '').'>'.$street_names[$i].'</option>';
    }
    $email_form .= '
                        </select>
                        ' . ((isset($has_error['street']) && $has_error['street']) ? '<span class="help-block">Please select out your street name.</span>' : '') . '
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['city']) && $has_error['city']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">City*</label>
                        <input type="text" name="city" class="form-control" placeholder="City" value="'.$city.'" readonly>
                        ' . ((isset($has_error['city']) && $has_error['city']) ? '<span class="help-block">Please fill out your city.</span>' : '') . '
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['state']) && $has_error['state']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">State*</label>
                        <input type="text" name="state" class="form-control" placeholder="State" value="MI" readonly>
                        ' . ((isset($has_error['state']) && $has_error['state']) ? '<span class="help-block">Please fill out your state.</span>' : '') . '
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group' . ((isset($has_error['zip']) && $has_error['zip']) ? ' has-error' : '') . '">
                        <label class="sub-label control-label">Zip Code*</label>
                        <input type="text" name="zip" class="form-control" placeholder="Zip Code" value="'.$zip_code.'" readonly>
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
                    <label class="control-label">Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Email" value="' . (isset($form_data) ? $form_data['email'] : '') . '">
                    ' . ((isset($has_error['email']) && $has_error['email']) ? '<span class="help-block">Please fill out a valid email address.</span>' : '') . '
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['dob']) && $has_error['dob']) ? ' has-error' : '') . '">
                    <label class="control-label">Date of Birth*</label>
                    <input type="text" name="dob" class="form-control" placeholder="mm/dd/yyyy" value="' . (isset($form_data) ? $form_data['dob'] : '') . '">
                    ' . ((isset($has_error['dob']) && $has_error['dob']) ? '<span class="help-block">Please fill out date of birth.</span>' : '') . '
                </div>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['military_service']) && $has_error['military_service']) ? ' has-error' : '') . '">
            <label class="control-label">Armed Forces Service*</label>
            ' . ((isset($has_error['military_service']) && $has_error['military_service']) ? '<span class="help-block">Please check if you or your spouse served in the armed forces service.</span>' : '') . '
            <div class="radio military-service">
                <label>
                    <input type="radio" name="military_service" value="I am currently serving in the Armed Forces" '.(isset($form_data) && isset($form_data['military_service']) && ($form_data['military_service'] == 'I am currently serving in the Armed Forces') ? 'checked' : '').'>
                    I am currently serving in the Armed Forces
                </label>
            </div>
            <div class="radio military-service">
                <label>
                    <input type="radio" name="military_service" value="I previously served in the Armed Forces" '.(isset($form_data) && isset($form_data['military_service']) && ($form_data['military_service'] == 'I previously served in the Armed Forces') ? 'checked' : '').'>
                    I previously served in the Armed Forces
                </label>
            </div>
            <div class="radio military-service">
                <label>
                    <input type="radio" name="military_service" value="I am the surviving spouse of an Armed Forces Servicemember" '.(isset($form_data) && isset($form_data['military_service']) && ($form_data['military_service'] == 'I am the surviving spouse of an Armed Forces Servicemember') ? 'checked' : '').'>
                    I am the surviving spouse of an Armed Forces Servicemember
                </label>
            </div>
            <div class="radio military-service">
                <label>
                    <input type="radio" name="military_service" value="I have never served in the Armed Forces" '.(isset($form_data) && isset($form_data['military_service']) && ($form_data['military_service'] == 'I have never served in the Armed Forces') ? 'checked' : '').'>
                    I have never served in the Armed Forces
                </label>
            </div>
        </div>
        <div class="form-group military-branch-form-group" style="display:none">
            <label class="control-label">Which Branch of Military?</label>
            <input type="text" name="military_branch" class="form-control" placeholder="Which Branch of Military?" value="' . (isset($form_data) ? $form_data['military_branch'] : '') . '">
        </div>
        
        <h2>Household Information</h2>
        <div class="form-group' . ((isset($has_error['years_in_home']) && $has_error['years_in_home']) ? ' has-error' : '') . '">
            <label class="control-label">How Long Have You Lived in Your Home?*</label>
            <input type="text" name="years_in_home" class="form-control" placeholder="How Long Have Your Lived in Your Home?" value="' . (isset($form_data) ? $form_data['years_in_home'] : '') . '">
            ' . ((isset($has_error['years_in_home']) && $has_error['years_in_home']) ? '<span class="help-block">Please fill out a how long you lived in your home.</span>' : '') . '
        </div>
        <div class="row hidden">
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['rent_or_own']) && $has_error['rent_or_own']) ? ' has-error' : '') . '">
                    <label class="control-label">Do you rent or own your home?</label>
                    ' . ((isset($has_error['rent_or_own']) && $has_error['rent_or_own']) ? '<span class="help-block">Please select if you rent or own your home.</span>' : '') . '
                    <div class="radio">
                        <label>
                            <input type="radio" name="rent_or_own" value="Rent" '.(isset($form_data) && isset($form_data['rent_or_own']) && ($form_data['rent_or_own'] == 'Rent') ? 'checked' : '').'>
                            Rent
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="rent_or_own" value="Own" '.(isset($form_data) && isset($form_data['rent_or_own']) && ($form_data['rent_or_own'] == 'Own') ? 'checked' : '').'>
                            Own
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
        <div class="form-section form-group' . (((isset($has_error['adults_in_household']) && $has_error['adults_in_household']) || (isset($has_error['children_in_household']) && $has_error['children_in_household'])) ? ' has-error' : '') . '">
            <div class="row">
                <div class="col-sm-8 col-lg-6">
                    <label>How many <strong>adults, 18 years or older</strong>, live in your household?*</label>
                </div>
                <div class="col-sm-4 margin-bottom-5">
                    <input type="text" name="adults_in_household" class="form-control form-control-inline" value="' . (isset($form_data) ? $form_data['adults_in_household'] : '') . '"> adults
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 col-lg-6">
                    <label>How many <strong>children, 18 years or younger</strong>, live in your household?*</label>
                </div>
                <div class="col-sm-4 margin-bottom-5">
                    <input type="text" name="children_in_household" class="form-control form-control-inline" value="' . (isset($form_data) ? $form_data['children_in_household'] : '') . '"> children
                </div>
            </div>
            ' . (((isset($has_error['adults_in_household']) && $has_error['adults_in_household']) || (isset($has_error['children_in_household']) && $has_error['children_in_household'])) ? '<p class="help-block">Please enter the amount of occupants in your home.</p>' : '') . '
        </div>
        <!--<div class="form-section form-group margin-top-20">
            <label>Please list the name and date of birth for any additional household members/dependents<br><small>Click the plus button to add more.</small></label>
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
                    <tr class="dependent-row">
                        <td><input type="text" name="dependent_name[]" class="form-control" value=""></td>
                        <td><input type="text" name="dependent_dob[]" class="form-control"></td>
                        <td><div class="btn-group" role="group"><button type="button" class="btn btn-darkblue btn-sm btn-dependent-add"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-darkblue btn-sm btn-dependent-remove"><i class="fa fa-minus" aria-hidden="true"></i></button></div></td>
                    </tr>
                    <tr class="dependent-row">
                        <td><input type="text" name="dependent_name[]" class="form-control" value=""></td>
                        <td><input type="text" name="dependent_dob[]" class="form-control"></td>
                        <td><div class="btn-group" role="group"><button type="button" class="btn btn-darkblue btn-sm btn-dependent-add"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-darkblue btn-sm btn-dependent-remove"><i class="fa fa-minus" aria-hidden="true"></i></button></div></td>
                    </tr>
                    <tr class="dependent-row">
                        <td><input type="text" name="dependent_name[]" class="form-control" value=""></td>
                        <td><input type="text" name="dependent_dob[]" class="form-control"></td>
                        <td><div class="btn-group" role="group"><button type="button" class="btn btn-darkblue btn-sm btn-dependent-add"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-darkblue btn-sm btn-dependent-remove"><i class="fa fa-minus" aria-hidden="true"></i></button></div></td>
                    </tr>
                </tbody>
            </table>
        </div>-->

        <div class="form-group' . ((isset($has_error['household_disablility_service']) && $has_error['household_disablility_service']) ? ' has-error' : '') . '">
            <label class="control-label">Household Disability Status <small>(optional)</small></label>
            <div class="radio">
                <label>
                    <input type="radio" name="household_disablility_service" value="One or more household members has a disability" '.(isset($form_data) && isset($form_data['household_disablility_service']) && ($form_data['household_disablility_service'] == 'One or more household members has a disability') ? 'checked' : '').'>
                    One or more household members has a disability
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="household_disablility_service" value="N/A" '.(isset($form_data) && isset($form_data['household_disablility_service']) && ($form_data['household_disablility_service'] == 'N/A') ? 'checked' : '').'>
                    N/A
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['how_did_you_hear']) && $has_error['how_did_you_hear']) ? ' has-error' : '') . '">
            <label class="control-label">How did you hear about Habitat Oakland?*</label>
            ' . ((isset($has_error['how_did_you_hear']) && $has_error['how_did_you_hear']) ? '<span class="help-block">Please answer how you heard about Habitat Oakland.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" class="habitat-homeowner-checkbox" value="Habitat Homeowner" '.(isset($form_data) && isset($form_data['how_did_you_hear']) && (strpos($form_data['how_did_you_hear'],'Habitat Homeowner') !== false) ? 'checked' : '').'>
                    Habitat Homeowner
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="Neighbor" '.(isset($form_data) && isset($form_data['how_did_you_hear']) && (strpos($form_data['how_did_you_hear'],'Neighbor') !== false) ? 'checked' : '').'>
                    Neighbor
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="Friend or family" '.(isset($form_data) && isset($form_data['how_did_you_hear']) && (strpos($form_data['how_did_you_hear'],'Friend or family') !== false) ? 'checked' : '').'>
                    Friend or family
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="Lawn sign in the Neighborhood" '.(isset($form_data) && isset($form_data['how_did_you_hear']) && (strpos($form_data['how_did_you_hear'],'Lawn sign in the Neighborhood') !== false) ? 'checked' : '').'>
                    Lawn sign in the Neighborhood
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="Online" '.(isset($form_data) && isset($form_data['how_did_you_hear']) && (strpos($form_data['how_did_you_hear'],'Online') !== false) ? 'checked' : '').'>
                    Online
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="Another organization" '.(isset($form_data) && isset($form_data['how_did_you_hear']) && (strpos($form_data['how_did_you_hear'],'Another organization') !== false) ? 'checked' : '').'>
                    Another organization
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="how_did_you_hear[]" value="None of the above" '.(isset($form_data) && isset($form_data['how_did_you_hear']) && (strpos($form_data['how_did_you_hear'],'None of the above') !== false) ? 'checked' : '').'>
                    None of the above
                </label>
            </div>
        </div>
        <div class="form-group referrer-name-form-group" style="display:none">
            <label class="control-label">Referring Homeowner Name</label>
            <input type="text" name="referrer_name" class="form-control" placeholder="Referring Homeowner Name" value="' . (isset($form_data) ? $form_data['referrer_name'] : '') . '">
        </div>
        <div class="form-group hidden' . ((isset($has_error['receive_updates']) && $has_error['receive_updates']) ? ' has-error' : '') . '">
            <label class="control-label">I would like to receive updates from Habitat Oakland</label>
            <div class="radio">
                <label>
                    <input type="radio" name="receive_updates" value="Yes" '.(isset($form_data) && isset($form_data['receive_updates']) && ($form_data['receive_updates'] == 'Yes') ? 'checked' : '').'>
                    Yes
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="receive_updates" value="No" '.(isset($form_data) && isset($form_data['receive_updates']) && ($form_data['receive_updates'] == 'No') ? 'checked' : '').'>
                    No
                </label>
            </div>
        </div>
        <h2>Program Eligibility</h2>
        <p>Please answer the questions below to provide Habitat with information about your current housing status, ability to pay for repair services and your willingness to partner with our organization.</p>
        <div class="form-group' . ((isset($has_error['homeownership']) && $has_error['homeownership']) ? ' has-error' : '') . '">
            <label class="control-label">Homeownership*</label>
            ' . ((isset($has_error['homeownership']) && $has_error['homeownership']) ? '<span class="help-block">Please answer about your homeownership.</span>' : '') . '
            <div class="checkbox i-am-homeowner">
                <label>
                    <input type="checkbox" name="homeownership[]" value="I am the legal owner of my home (my name is listed on the property deed)" '.(isset($form_data) && isset($form_data['homeownership']) && (strpos($form_data['homeownership'],'I am the legal owner of my home (my name is listed on the property deed)') !== false) ? 'checked' : '').'>
                    I am the legal owner of my home (my name is listed on the property deed)
                </label>
            </div>
            <div class="checkbox i-am-renting">
                <label>
                    <input type="checkbox" name="homeownership[]" value="I am NOT the homeowner; I am currently renting the home" '.(isset($form_data) && isset($form_data['homeownership']) && (strpos($form_data['homeownership'],'I am NOT the homeowner; I am currently renting the home') !== false) ? 'checked' : '').'>
                    I am NOT the homeowner; I am currently renting the home
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="homeownership[]" value="This home is my primary residence" '.(isset($form_data) && isset($form_data['homeownership']) && (strpos($form_data['homeownership'],'This home is my primary residence') !== false) ? 'checked' : '').'>
                    This home is my primary residence
                </label>
            </div>
        </div>
        <div class="form-group date-home-purchased-form-group" style="'.(isset($form_data) && isset($form_data['homeownership']) && (strpos($form_data['homeownership'],'I am the legal owner of my home (my name is listed on the property deed)') !== false) ? '' : 'display:none').'">
            <label class="control-label">What Date Did You Purchased Your Home?</label>
            <input type="text" name="date_home_purchased" class="form-control" placeholder="What Date Did You Purchased Your Home?" value="' . (isset($form_data) ? $form_data['date_home_purchased'] : '') . '">
        </div>
        <div class="form-group landlord-info-form-group" style="'.(isset($form_data) && isset($form_data['homeownership']) && (strpos($form_data['homeownership'],'I am NOT the homeowner; I am currently renting the home') !== false) ? '' : 'display:none').'">
            <p>If you are renting, you may still be eligible for some services however you would need consent from your landlord. Please provide their information below and Habitat will contact them if necessary.</p>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Landlord Name</label>
                         <input type="text" name="landlord_name" class="form-control" placeholder="Landlord Name" value="' . (isset($form_data) ? $form_data['landlord_name'] : '') . '">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Landlord Phone or Email</label>
                         <input type="text" name="landlord_phone_email" class="form-control" placeholder="Landlord Phone or Email" value="' . (isset($form_data) ? $form_data['landlord_phone_email'] : '') . '">
                    </div>
                </div>
            </div>


        </div>
        <div class="form-group' . ((isset($has_error['ability_to_pay']) && $has_error['ability_to_pay']) ? ' has-error' : '') . '">
            <label class="control-label">Ability to Pay*</label>
            ' . ((isset($has_error['ability_to_pay']) && $has_error['ability_to_pay']) ? '<span class="help-block">Please answer concerning your ability to pay.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="ability_to_pay[]" value="I understand that Habitat will charge a reasonable fee for the services I am approved for" '.(isset($form_data) && isset($form_data['ability_to_pay']) && (strpos($form_data['ability_to_pay'],'I understand that Habitat will charge a reasonable fee for the services I am approved for') !== false) ? 'checked' : '').'>
                    I understand that Habitat will charge a reasonable fee for the services I am approved for
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="ability_to_pay[]" value="My fee will be reviewed with me and agreed upon prior to project approval" '.(isset($form_data) && isset($form_data['ability_to_pay']) && (strpos($form_data['ability_to_pay'],'My fee will be reviewed with me and agreed upon prior to project approval') !== false) ? 'checked' : '').'>
                    My fee will be reviewed with me and agreed upon prior to project approval
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="ability_to_pay[]" value="My fee is due and shall be paid in full prior to the work day on my home" '.(isset($form_data) && isset($form_data['ability_to_pay']) && (strpos($form_data['ability_to_pay'],'My fee is due and shall be paid in full prior to the work day on my home') !== false) ? 'checked' : '').'>
                    My fee is due and shall be paid in full prior to the work day on my home
                </label>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['willingness_to_partner']) && $has_error['willingness_to_partner']) ? ' has-error' : '') . '">
            <label class="control-label">Willingness to Partner*</label>
            ' . ((isset($has_error['willingness_to_partner']) && $has_error['willingness_to_partner']) ? '<span class="help-block">Please answer about your willingness to partner.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I will be available and participate in work done at my home alongside Habitat volunteers when scheduled" '.(isset($form_data) && isset($form_data['willingness_to_partner']) && (strpos($form_data['willingness_to_partner'],'I will be available and participate in work done at my home alongside Habitat volunteers when scheduled') !== false) ? 'checked' : '').'>
                    I will be available and participate in work done at my home alongside Habitat volunteers when scheduled
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I will put in the required number of sweat equity hours for my project (4-8 hours)" '.(isset($form_data) && isset($form_data['willingness_to_partner']) && (strpos($form_data['willingness_to_partner'],'I will put in the required number of sweat equity hours for my project (4-8 hours)') !== false) ? 'checked' : '').'>
                    I will put in the required number of sweat equity hours for my project (4-8 hours)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="willingness_to_partner[]" value="I am willing to display partnership materials (lawn sign, stencil)" '.(isset($form_data) && isset($form_data['willingness_to_partner']) && (strpos($form_data['willingness_to_partner'],'I am willing to display partnership materials (lawn sign, stencil)') !== false) ? 'checked' : '').'>
                    I am willing to display partnership materials (lawn sign, stencil)
                </label>
            </div>
        </div>
        <h2>Rock the Block - Home Repair Service Available</h2>
        <p>This program focuses on simple exterior home repairs that will be completed by our team of volunteers. Included in this program is a FREE energy audit from the Consumers Energy Helping Neighbors Program. A technician will come to your home to perform energy tests to help identify ways to reduce your energy costs. Some people are eligible for free energy upgrades. For more information about this FREE service, <a href="https://www.consumershelpingneighbors.com/" target="_blank">click here</a>.</p>
        <p>Below is a list of services available. Price ranges are based on typical services and are subject to change based on an assessment.</p>
        <p></p>
        <div class="form-group' . ((isset($has_error['services_requested']) && $has_error['services_requested']) ? ' has-error' : '') . '">
            <label class="control-label">My home is in NEED of one or more of the following services</label>
            <div class="checkbox energy-audit hidden">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Energy Audit to identify savings" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Energy Audit to identify savings') !== false) ? 'checked' : '').'>
                    Energy Audit to identify savings<br>
                    Free
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Landscape Clean Up: rake, edge, trim hedges/trees, brush clearing" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Landscape Clean Up: rake, edge, trim hedges/trees, brush clearing') !== false) ? 'checked' : '').'>
                    Landscape Clean Up: rake, edge, trim hedges/trees, brush clearing<br>
                    $10 - $50
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Lawn Mowing" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Lawn Mowing') !== false) ? 'checked' : '').'>
                    Lawn Mowing<br>
                    $10 - $25
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Gutter Cleaning and Repair" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Gutter Cleaning and Repair') !== false) ? 'checked' : '').'>
                    Gutter Cleaning and Repair<br>
                    $10 - $40 (subject to assessment)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Exterior of Home Painting" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Exterior of Home Painting') !== false) ? 'checked' : '').'>
                    Exterior Home Painting, Foundation Painting, Garage Painting (Weather Permitting)<br>
                    $100 - $500 (subject to testing & assessment)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Exterior Home Cleaning & Power Washing" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Exterior Home Cleaning & Power Washing') !== false) ? 'checked' : '').'>
                    Exterior Home Cleaning & Power Washing<br>
                    $25 - $100
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Wood porch, steps or fence repair (does not include paint/stain)" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Wood porch, steps or fence repair (does not include paint/stain)') !== false) ? 'checked' : '').'>
                    Wood porch, steps or fence repair (does not include paint/stain)<br>
                    $75 - $125 (subject to testing & assessment)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Wood porch or fence painting/stain" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Wood porch or fence painting/stain') !== false) ? 'checked' : '').'>
                    Wood porch or fence painting/stain<br>
                    $25 - $100 (subject to survey & testing)
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Leaf Raking" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Leaf Raking') !== false) ? 'checked' : '').'>
                    Leaf Raking (Fall Only)<br>
                    $10 - $25
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="services_requested[]" value="Dumpster Use" '.(isset($form_data) && isset($form_data['services_requested']) && (strpos($form_data['services_requested'],'Dumpster Use') !== false) ? 'checked' : '').'>
                    Dumpster Use (Trash and Yard Debris)<br>
                    Free
                </label>
            </div>
        </div>
        <div class="form-group consumers-energy-account-number-form-group" style="display:none">
            <label class="control-label">What is Your Consumers Energy Account Number?</label>
            <input type="text" name="consumers_energy_account_number" class="form-control" placeholder="What is Your Consumers Energy Account Number?" value="' . (isset($form_data) ? $form_data['consumers_energy_account_number'] : '') . '">
        </div>
        <h2>Financial Information</h2>
        <p>Please select the sources of income for your household. Ex: Job, Social Security, Child Support, etc. Please list the amounts BEFORE taxes.</p>
        ' . (((isset($has_error['income_source_1']) && $has_error['income_source_1']) || (isset($has_error['income_frequency_1']) && $has_error['income_frequency_1']) || (isset($has_error['amount_per_check_1']) && $has_error['amount_per_check_1'])) ? '<p class="text-warning">Please provide information for at least one income source.</p>' : '') . '
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group ' . ((isset($has_error['income_source_1']) && $has_error['income_source_1']) ? ' has-error' : '') . '">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_1" class="form-control">
                        <option value="">Choose One</option>
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
                <div class="form-group ' . ((isset($has_error['income_frequency_1']) && $has_error['income_frequency_1']) ? ' has-error' : '') . '">
                    <label class="control-label">How Often Are You Paid?</label>
                    <select name="income_frequency_1" class="form-control">
                        <option value="">Choose One</option>
                        <option value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_1'] == 'Weekly') ? 'selected' : '').'>Weekly</option>
                        <option value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_1'] == 'Bi-weekly') ? 'selected' : '').'>Bi-weekly</option>
                        <option value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_1'] == 'Monthly') ? 'selected' : '').'>Monthly</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group ' . ((isset($has_error['amount_per_check_1']) && $has_error['amount_per_check_1']) ? ' has-error' : '') . '">
                    <label class="control-label">Amount Per Check</label>
                    <input type="text" name="amount_per_check_1" class="form-control" placeholder="$" value="' . (isset($form_data) ? $form_data['amount_per_check_1'] : '') . '">
                </div>
            </div>
        </div>
        <hr class="margin-top-5 margin-bottom-5">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_2" class="form-control">
                        <option value="">Choose One</option>
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
                    <label class="control-label">How Often Are You Paid?</label>
                    <select name="income_frequency_2" class="form-control">
                        <option value="">Choose One</option>
                        <option value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_2'] == 'Weekly') ? 'selected' : '').'>Weekly</option>
                        <option value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_2'] == 'Bi-weekly') ? 'selected' : '').'>Bi-weekly</option>
                        <option value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_2'] == 'Monthly') ? 'selected' : '').'>Monthly</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Amount Per Check</label>
                    <input type="text" name="amount_per_check_2" class="form-control" placeholder="$" value="' . (isset($form_data) ? $form_data['amount_per_check_2'] : '') . '">
                </div>
            </div>
        </div>
        <hr class="margin-top-5 margin-bottom-5">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_3" class="form-control">
                        <option value="">Choose One</option>
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
                    <label class="control-label">How Often Are You Paid?</label>
                    <select name="income_frequency_3" class="form-control">
                        <option value="">Choose One</option>
                        <option value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_3'] == 'Weekly') ? 'selected' : '').'>Weekly</option>
                        <option value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_3'] == 'Bi-weekly') ? 'selected' : '').'>Bi-weekly</option>
                        <option value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_3'] == 'Monthly') ? 'selected' : '').'>Monthly</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Amount Per Check</label>
                    <input type="text" name="amount_per_check_3" class="form-control" placeholder="$" value="' . (isset($form_data) ? $form_data['amount_per_check_3'] : '') . '">
                </div>
            </div>
        </div>
        <hr class="margin-top-5 margin-bottom-5">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">Income Source</label>
                    <select name="income_source_4" class="form-control">
                        <option value="">Choose One</option>
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
                    <label class="control-label">How Often Are You Paid?</label>
                    <select name="income_frequency_4" class="form-control">
                        <option value="">Choose One</option>
                        <option value="Weekly" '.(isset($form_data) && ($form_data['income_frequency_4'] == 'Weekly') ? 'selected' : '').'>Weekly</option>
                        <option value="Bi-weekly" '.(isset($form_data) && ($form_data['income_frequency_4'] == 'Bi-weekly') ? 'selected' : '').'>Bi-weekly</option>
                        <option value="Monthly" '.(isset($form_data) && ($form_data['income_frequency_4'] == 'Monthly') ? 'selected' : '').'>Monthly</option>
                    </select>
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
            <span class="help-block">I understand that by submitting this application form, I am authorizing Habitat for Humanity of Oakland County (HFHOC) to evaluate my eligibility for home preservation services which includes a criminal background and sexual offender check. I have answered all the questions on this form truthfully. The original or a copy of this form will be retained by HFHOC even if the application is not approved. HFHOC will not share the information on this application form with any outside agency except as needed to provide preservation services I select and are part of the program such as Consumers Energy.</span>
            ' . ((isset($has_error['authorization_and_release']) && $has_error['authorization_and_release']) ? '<span class="help-block">Please accept the agreement.</span>' : '') . '
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="authorization_and_release" value="Agree" '.(isset($form_data) && isset($form_data['authorization_and_release']) && (strpos($form_data['authorization_and_release'],'Agree') !== false) ? 'checked' : '').'>
                    I Agree
                </label>
            </div>
            <div class="form-group' . ((isset($has_error['additional_agreements']) && $has_error['additional_agreements']) ? ' has-error' : '') . '">
                <p>I understand that:</p>
                ' . ((isset($has_error['additional_agreements']) && $has_error['additional_agreements']) ? '<span class="help-block">Please check all 3 checkboxes below.</span>' : '') . '
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="additional_agreements[]" value="Only projects that can be completed in 2 days will be approved" '.(isset($form_data) && isset($form_data['additional_agreements']) && (strpos($form_data['additional_agreements'],'Only projects that can be completed in 2 days will be approved') !== false) ? 'checked' : '').'>
                        Only projects that can be completed in 2 days will be approved.
                    </label>
                    <label>
                        <input type="checkbox" name="additional_agreements[]" value="The requested services and completion of my project is dependent on the number of volunteers that register for the event" '.(isset($form_data) && isset($form_data['additional_agreements']) && (strpos($form_data['additional_agreements'],'The requested services and completion of my project is dependent on the number of volunteers that register for the event') !== false) ? 'checked' : '').'>
                        The requested services and completion of my project is dependent on the number of volunteers that register for the event.
                    </label>
                    <label>
                        <input type="checkbox" name="additional_agreements[]" value="Services must meet volunteer safety requirements and those that do not will not be completed" '.(isset($form_data) && isset($form_data['additional_agreements']) && (strpos($form_data['additional_agreements'],'Services must meet volunteer safety requirements and those that do not will not be completed') !== false) ? 'checked' : '').'>
                        Services must meet volunteer safety requirements and those that do not will not be completed.
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group' . ((isset($has_error['signature']) && $has_error['signature']) ? ' has-error' : '') . '">
            <label class="control-label">Please Fill Out Your Name as a Signature*</label>
            <input type="text" name="signature" class="form-control" placeholder="Your Full Name" value="' . (isset($form_data) ? $form_data['signature'] : '') . '">
            ' . ((isset($has_error['signature']) && $has_error['signature']) ? '<span class="help-block">Please fill out your name.</span>' : '') . '
        </div>
        <h2>Additional Demographic Information<br><small>For government monitoring purposes</small></h2>
        <p>The following information is requested by the federal government for loans related to the purchase of homes, in order to monitor the lender’s compliance with equal credit opportunity and fair housing laws. You are not required to furnish this information, but you are encouraged to do so.</p>
        <p>The law provides that a lender may neither discriminate on the basis of this information, nor on whether you choose to furnish it or not. However, if you choose not to furnish it, under federal regulation, this lender is required to note race and sex on the basis of visual observation or surname. If you do not wish to furnish the information below, please check the box below.</p>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="government_opt_out" value="I do NOT wish to furnish this information" '.(isset($form_data) && isset($form_data['government_opt_out']) && ($form_data['government_opt_out'] == 'I do NOT wish to furnish this information') ? 'checked' : '').'>
                    I do NOT wish to furnish this information
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>What race do you identify with? <small>(check all that apply)</small></label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="American Indian or Alaskan Native" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'American Indian or Alaskan Native') !== false) ? 'checked' : '').'>
                    American Indian or Alaskan Native
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="Native Hawaiian or Other Pacific Islander" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'Native Hawaiian or Other Pacific Islander') !== false) ? 'checked' : '').'>
                    Native Hawaiian or Other Pacific Islander
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="Black / African American" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'Black / African American') !== false) ? 'checked' : '').'>
                    Black / African American
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="White / Caucasian" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'White / Caucasian') !== false) ? 'checked' : '').'>
                    White / Caucasian
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="Asian" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'Asian') !== false) ? 'checked' : '').'>
                    Asian
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="American Indian or Alaskan Native AND Caucasian" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'American Indian or Alaskan Native AND Caucasian') !== false) ? 'checked' : '').'>
                    American Indian or Alaskan Native AND Caucasian
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="Asian AND Caucasian" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'Asian AND Caucasian') !== false) ? 'checked' : '').'>
                    Asian AND Caucasian
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="Black/African American AND Caucasian" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'Black/African American AND Caucasian') !== false) ? 'checked' : '').'>
                    Black/African American AND Caucasian
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="American Indian or Alaskan Native AND Black/African American" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'American Indian or Alaskan Native AND Black/African American') !== false) ? 'checked' : '').'>
                    American Indian or Alaskan Native AND Black/African American
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="race_national_origin[]" value="Other" class="race-national-origin-other" '.(isset($form_data) && isset($form_data['race_national_origin']) && (strpos($form_data['race_national_origin'],'Other') !== false) ? 'checked' : '').'>
                    Other (please specify)
                </label>
            </div>
            <!--<select name="race_national_origin" id="race_national_origin" class="form-control">
                <option value="">Choose One</option>
                <option value="American Indian or Alaskan Native" '.(isset($form_data) && ($form_data['race_national_origin'] == 'American Indian or Alaskan Native') ? 'selected' : '').'>American Indian or Alaskan Native</option>
                <option value="Native Hawaiian or Other Pacific Islander" '.(isset($form_data) && ($form_data['race_national_origin'] == 'Native Hawaiian or Other Pacific Islander') ? 'selected' : '').'>Native Hawaiian or Other Pacific Islander</option>
                <option value="Black/African American" '.(isset($form_data) && ($form_data['race_national_origin'] == 'Black/African American') ? 'selected' : '').'>Black/African American</option><option value="Caucasian">Caucasian</option>
                <option value="Asian" '.(isset($form_data) && ($form_data['race_national_origin'] == 'Asian') ? 'selected' : '').'>Asian</option>
                <option value="American Indian or Alaskan Native AND Caucasian" '.(isset($form_data) && ($form_data['race_national_origin'] == 'American Indian or Alaskan Native AND Caucasian') ? 'selected' : '').'>American Indian or Alaskan Native AND Caucasian</option>
                <option value="Asian AND Caucasian" '.(isset($form_data) && ($form_data['race_national_origin'] == 'Asian AND Caucasian') ? 'selected' : '').'>Asian AND Caucasian</option>
                <option value="Black/African American AND Caucasian" '.(isset($form_data) && ($form_data['race_national_origin'] == 'Black/African American AND Caucasian') ? 'selected' : '').'>Black/African American AND Caucasian</option>
                <option value="American Indian or Alaskan Native AND Black/African American" '.(isset($form_data) && ($form_data['race_national_origin'] == 'American Indian or Alaskan Native AND Black/African American') ? 'selected' : '').'>American Indian or Alaskan Native AND Black/African American</option>
                <option value="Other" '.(isset($form_data) && ($form_data['race_national_origin'] == 'Other') ? 'selected' : '').'>Other (specify)</option>
            </select>-->
        </div>
        <div class="form-group race-national-origin-explaination-form-group" style="display:none">
            <label class="control-label">Race/National Origin Explanation</label>
            <input type="text" name="race_national_origin_explaination" class="form-control" placeholder="Please explain" value="' . (isset($form_data) ? $form_data['race_national_origin_explaination'] : '') . '">
        </div>
        <div class="form-group">
            <label>Do you consider yourself Hispanic, Latino, Latina, or of Spanish Origin?</label>
            <div class="radio">
                <label>
                    <input type="radio" name="ethnicity" value="Yes, I consider myself as Hispanic, Latino, Latina, or of Spanish Origin" '.(isset($form_data) && ($form_data['ethnicity'] == 'Yes, I consider myself as Hispanic, Latino, Latina, or of Spanish Origin') ? 'checked' : '').'>
                    Yes, I consider myself as Hispanic, Latino, Latina, or of Spanish Origin
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="ethnicity" value="No, I do not consider myself as Hispanic, Latino, Latina, or of Spanish Origin" '.(isset($form_data) && ($form_data['ethnicity'] == 'No, I do not consider myself as Hispanic, Latino, Latina, or of Spanish Origin') ? 'checked' : '').'>
                    No, I do not consider myself as Hispanic, Latino, Latina, or of Spanish Origin
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>What gender do you identify with?</label>
            <div class="radio">
                <label>
                    <input type="radio" name="gender_identity" value="Female" '.(isset($form_data) && ($form_data['gender_identity'] == 'Female') ? 'checked' : '').'>
                    Female
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="gender_identity" value="Male" '.(isset($form_data) && ($form_data['gender_identity'] == 'Male') ? 'checked' : '').'>
                    Male
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="gender_identity" value="Non-Binary" '.(isset($form_data) && ($form_data['gender_identity'] == 'Non-Binary') ? 'checked' : '').'>
                    Non-Binary
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="gender_identity" value="Other" '.(isset($form_data) && ($form_data['gender_identity'] == 'Other') ? 'checked' : '').' class="gender-identity-other">
                    Other
                </label>
            </div>
            <!--<select name="gender_identity" id="gender_identity" class="form-control">
                <option value="">Choose One</option>
                <option value="Female" '.(isset($form_data) && ($form_data['gender_identity'] == 'Female') ? 'selected' : '').'>Female</option>
                <option value="Male" '.(isset($form_data) && ($form_data['gender_identity'] == 'Male') ? 'selected' : '').'>Male</option>
                <option value="Non-Binary" '.(isset($form_data) && ($form_data['gender_identity'] == 'Non-Binary') ? 'selected' : '').'>Non-Binary</option>
                <option value="Other" '.(isset($form_data) && ($form_data['gender_identity'] == 'Other') ? 'selected' : '').'>Other (Specify)</option></select>
            </select>-->
        </div>
        <div class="form-group gender-identity-explaination-form-group" style="display:none">
            <label class="control-label">Gender Identity Explanation</label>
            <input type="text" name="gender_identity_explaination" class="form-control" placeholder="Please explain" value="' . (isset($form_data) ? $form_data['gender_identity_explaination'] : '') . '">
        </div>
        <div class="form-group margin-top-25' . ((isset($has_error['privacy_policy']) && $has_error['privacy_policy']) ? ' has-error' : '') . '">
            <label class="control-label">I have read Habitat for Humanity of Oakland County\'s <a href="/privacy-policy/" target="_blank">Privacy Policy</a></label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="privacy_policy" value="I agree" '.(isset($form_data) && ($form_data['privacy_policy'] == 'I agree') ? 'checked' : '').'>
                    I agree
                </label>
            </div>
            ' . ((isset($has_error['privacy_policy']) && $has_error['privacy_policy']) ? '<span class="help-block">Please agree if you understand and agree with the privacy policy.</span>' : '') . '
        </div>
        <div class="form-group' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? ' has-error' : '') . '">
            <div class="g-recaptcha" data-sitekey="'.getenv('RECAPTCHA_SITEKEY').'"></div>
            ' . ((isset($has_error['recaptcha']) && $has_error['recaptcha']) ? '<span class="help-block">ReCaptcha validation has failed.</span>' : '') . '
        </div>
        <div class="form-group">
            <input type="hidden" value="'.$event.'" name="event">
            <button class="btn btn-lg btn-darkblue" type="submit">Submit</button>
        </div>
    </form>';
    $email_form .= '</div>';
    return $email_form;
}
add_shortcode('rock_the_block_form_old', 'rock_the_block_form_shortcode');
