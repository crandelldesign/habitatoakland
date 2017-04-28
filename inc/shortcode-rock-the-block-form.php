<?php
// Shortcode for the Rock the Block Form
use Mailgun\Mailgun;
function rock_the_block_form_shortcode()
{
    wp_enqueue_script('googlerecaptcha', 'https://www.google.com/recaptcha/api.js');
    // wp_enqueue_script('homeownership-form-js', get_stylesheet_directory_uri() . '/library/js/homeownership-form.js');
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

    $email_form = '<form class="golf-signup-form" action="' . get_permalink() . '" method="post">
    </form>';
    $close_div = '</div>';
    return $start_div . $info . $email_form . $close_div;
}
add_shortcode('rock_the_block_form', 'rock_the_block_form_shortcode');
