<?php
// Shortcode for Company Team Building
use Mailgun\Mailgun;
function company_team_building_form_shortcode()
{
    return $start_div . $info . $email_form . $close_div;
}
add_shortcode('company_team_building_form', 'company_team_building_form_shortcode');
