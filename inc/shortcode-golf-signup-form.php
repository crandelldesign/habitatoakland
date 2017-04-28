<?php
// Shortcode for the Golf Signup Form
use Mailgun\Mailgun;
function golf_signup_form_shortcode()
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
            'company_name', 'contact_name', 'address1', 'city', 'state', 'zip', 'phone', 'email', 'military_service', 'home_locations', 'current_housing_needs', 'affordability', 'willingness_to_partner', 'g-recaptcha-response'
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
                case 'company_name':
                    $form_columns[$field] = 'Contact Name';
                    break;
                case 'contact_name':
                    $form_columns[$field] = 'Contact Name';
                    break;
                default:
                    $form_columns[$field] = $field;
            }
        }
    }
    $start_div = '<div id="golf-signup-form">';

    $info = '';

    if ($error == true) {
        $info = '<div class="alert alert-danger">Please correct the errors in red on the form.</div>';
    }
    /*if ($error == false && $result != '') {
        $info = '<div class="alert alert-success">' . $result . '</div>';
    }*/

    $email_form = '<form class="golf-signup-form" action="' . get_permalink() . '" method="post">
        <h3>Contact Information</h3>
        <div class="form-group' . ((isset($has_error['company_name']) && $has_error['company_name']) ? ' has-error' : '') . '">
            <label class="control-label">Company Name*</label>
            <input type="text" name="company_name" class="form-control" placeholder="Company Name" value="' . (isset($form_data) ? $form_data['company_name'] : '') . '">
            ' . ((isset($has_error['company_name']) && $has_error['company_name']) ? '<span class="help-block">Please fill out the company name.</span>' : '') . '
            <span class="help-block">(As you would like it to appear in printed material)</span>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['contact_name']) && $has_error['contact_name']) ? ' has-error' : '') . '">
                    <label class="control-label">Contact Name*</label>
                    <input type="text" name="contact_name" class="form-control" placeholder="Contact Name" value="' . (isset($form_data) ? $form_data['contact_name'] : '') . '">
                    ' . ((isset($has_error['contact_name']) && $has_error['contact_name']) ? '<span class="help-block">Please fill out the contact name.</span>' : '') . '
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group' . ((isset($has_error['phone']) && $has_error['phone']) ? ' has-error' : '') . '">
                    <label control-label>Phone*</label>
                    <input type="text" name="phone" class="form-control" placeholder="Phone" value="' . (isset($form_data) ? $form_data['phone'] : '') . '">
                    ' . ((isset($has_error['phone']) && $has_error['phone']) ? '<span class="help-block">Please fill out your phone number.</span>' : '') . '
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
        <div class="form-group' . ((isset($has_error['email']) && $has_error['email']) ? ' has-error' : '') . '">
            <label class="control-label">Email*</label>
            <input type="text" name="email" class="form-control" placeholder="Email" value="' . (isset($form_data) ? $form_data['email'] : '') . '">
            ' . ((isset($has_error['email']) && $has_error['email']) ? '<span class="help-block">Please fill out a valid email address.</span>' : '') . '
        </div>
        <h3>Attendees</h3>
        <div class="form-group">
            <table class="table no-border vertical-align-middle">
                <tr>
                    <td>Number of Golfers</td>
                    <td><select class="form-control" name="number_of_golfers">
                            <option value="0" '.(isset($form_data) && ($form_data['number_of_golfers'] == '0') ? 'selected' : '').'>0</option>
                            <option value="1" '.(isset($form_data) && ($form_data['number_of_golfers'] == '1') ? 'selected' : '').'>1</option>
                        </select>
                    </td>
                    <td>&times; $125 = </td>
                    <td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="golfing_total" class="form-control readonly"readonly value="' . (isset($form_data) ? $form_data['golfing_total'] : '') . '"></div></td>
                </tr>
            </table>
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-darkblue" type="submit">Submit</button>
        </div>
    </form>';
    $close_div = '</div>';

    return $start_div . $info . $email_form . $close_div;
}
add_shortcode('golf_signup_form', 'golf_signup_form_shortcode');
