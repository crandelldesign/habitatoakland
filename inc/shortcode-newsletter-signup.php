<?php
// Shortcode for Newsletter Signup
use Ctct\Components\Contacts\Contact;
use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;
function newsletter_signup_shortcode($atts = array())
{

    extract( shortcode_atts( array(
        // Master List IS
        'list_id' => '1547299734'
    ), $atts ));
    $error   = false;
    $result  = '';

    switch ($list_id) {
        case '1207250353':
            // Women Build List - 1207250353
            $newsletter_slug = 'women-build';
            break;
        case '1943471176':
            // ReStore List - 1943471176
            $newsletter_slug = 'restore';
            break;
        default:
            // Default - Master List - 1547299734
            $newsletter_slug = 'master-list';
            break;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $required_fields = array(
            'email'
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
        // Check if the hidden field was filled out (honeypot)
        if (strlen($form_data['honeyName']) > 0) {
            $error = true;
            $result .= "<li>No spam, please.</li>";
            $has_error['spam'] = true;
        }
        if ($error == false) {

            // Enter your Constant Contact APIKEY and ACCESS_TOKEN
            define("APIKEY", getenv('CONSTANT_CONTACT_API'));
            define("ACCESS_TOKEN", getenv('CONSTANT_CONTACT_TOKEN'));
            $cc = new ConstantContact(APIKEY);
            $action = "Getting Contact By Email Address";
            try {
                // check to see if a contact with the email address already exists in the account
                $response = $cc->contactService->getContacts(ACCESS_TOKEN, array('email' => $form_data['email']));

                // create a new contact if one does not exist
                if (empty($response->results)) {
                    $action = "Creating Contact";
                    $contact = new Contact();
                    $contact->addEmail($form_data['email']);
                    $contact->addList($list_id);
                    $returnContact = $cc->contactService->addContact(ACCESS_TOKEN, $contact, ['action_by' => 'ACTION_BY_VISITOR']);
                // update the existing contact if address already existed
                } else {
                    $action = "Updating Contact";
                    $contact = $response->results[0];
                    if ($contact instanceof Contact) {
                        $contact->addList($list_id);
                        $returnContact = $cc->contactService->updateContact(ACCESS_TOKEN, $contact, ['action_by' => 'ACTION_BY_VISITOR']);
                    } else {
                        $e = new CtctException();
                        $e->setErrors(array("type", "Contact type not returned"));
                        throw $e;
                    }
                }
                // catch any exceptions thrown during the process and print the errors to screen
            } catch (CtctException $ex) {
                wp_die('<strong>Error ' . $action . '</strong>: <pre class="failure-pre">' . json_encode($ex->getErrors()) . '</pre>', 'Constant Contact Error', array('response' => 200, 'back_link' => true));
            }

            // Add Data to Database
            if(has_action('ftd_insert_data')) {
                do_action('ftd_insert_data','Newsletter', $form_data);
            }

            // Default - Master List - 1547299734
            $result = '<p>Thanks for signing up for our newsletter. You\'re now subscribed to receive regular updates.</p>
                <p>WHAT NEXT? Click <a href="'.get_site_url().'/volunteer/">here</a> to see all of the ways you can get involved now! You can also follow us on <a href="https://www.facebook.com/HabitatforHumanityOC" target="_blank">Facebook</a> to see all of our work in action.</p>';
            // Women Build List - 1207250353
            if ($list_id == '1207250353') {
                $result = 'Thank you for signing up for the Habitat for Humanity of Oakland County Women Build Newsletter!';
            }
            // ReStore List - 1943471176
            if ($list_id == '1943471176') {
                $result = 'Thank you for signing up for the Habitat for Humanity of Oakland County ReStore Newsletter!';
            }

            unset($form_data);
            unset($has_error);
        }
    }

    $start_div = '<div id="newsletter">';

    $info = '';

    if ($error == true) {
        $info = '<div class="alert alert-danger"><ul>' . $returnContact . ' ' .$result . '</ul></div>';
    }
    if ($error == false && $result != '') {
        $info = '<div class="alert alert-success">' . $result . '</div>';
    }

    ob_start();
        do_action('ftd_test');
        $test = ob_get_contents();
    ob_end_clean();

    $email_form = '<form class="newsletter-form '.$newsletter_slug.'" action="' . get_permalink() . '#newsletter" method="post">
        <input type="text" name="honeyName" style="display:none">
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
// Example: [newsletter_signup list_id='1943471176'] or [newsletter_signup]
add_shortcode('newsletter_signup', 'newsletter_signup_shortcode');
