<?php
// Shortcode for Newsletter Signup
function newsletter_signup_shortcode($newsletter_list)
{
    $error   = false;
    $result  = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required_fields = array(
            "email"
        );
        foreach ($_POST as $field => $value) {
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            $form_data[$field] = strip_tags($value);
        }
        foreach ($required_fields as $required_field) {
            $value = trim($form_data[$required_field]);
            if (empty($value)) {
                $error = true;
                if ($required_field == 'email') {
                    $result .= "<li>Please fill out an email address.</li>";
                    $has_error['email'] = true;
                }
            }
        }
        // and if the e-mail is not valid, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'
        if (!is_email($form_data['email']) && $error == false) {
            $error = true;
            $result .= "<li>Please enter a valid e-mail address.</li>";
            $has_error['email'] = true;
        }
        if ($error == false) {

            // Add Data to Database
            insertuser('Newsletter', $form_data);

            //Success
            $result = '<p>Thanks for signing up for our newsletter. You\'re now subscribed to receive regular updates.</p>
                <p>WHAT NEXT? Click <a href="'.get_site_url().'/volunteer/">here</a> to see all of the ways you can get involved now! You can also follow us on <a href="https://www.facebook.com/HabitatforHumanityOC" target="_blank">Facebook</a> to see all of our work in action.</p>';

            unset($form_data);
            unset($has_error);
        }
    }

    $start_div = '<div id="newsletter">';

    $info = '';

    if ($error == true) {
        $info = '<div class="alert alert-danger"><ul>' . $result . '</ul></div>';
    }
    if ($error == false && $result != '') {
        $info = '<div class="alert alert-success">' . $result . '</div>';
    }

    $email_form = '<form class="newsletter-form" action="' . get_permalink() . '#newsletter" method="post">
        <div class="form-group' . ((isset($has_error['email']) && $has_error['email']) ? ' has-error' : '') . '">
            <div class="input-group">
                <input type="email" name="email" class="form-control" placeholder="Email Address" value="' . (isset($form_data) ? $form_data['email'] : '') . '">
                <span class="input-group-btn">
                    <button class="btn btn-darkblue" type="submit">Sign Up</button>
                </span>
            </div>
        </div>
    </form>';
    $close_div = '</div>';
    return $start_div . $info . $email_form . $close_div;
}
add_shortcode('newsletter_signup', 'newsletter_signup_shortcode');
