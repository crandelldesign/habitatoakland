<?php
// Recaptcha Verification
function verifyCaptcha($googleResp){
    $fields = array(
        'secret' => urlencode(getenv('RECAPTCHA_SECRETKEY')),
        'response' => urlencode($googleResp),
        'remoteip' => urlencode($_SERVER['REMOTE_ADDR'])
    );

    $fields_string = '';

    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');


    //Initialize curl
    $ch = curl_init();

    //Set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    //Grab URL and pass it to the browser
    $response = curl_exec($ch);

    //Close cURL resource, and free up system resources
    curl_close($ch);

    $response = json_decode($response);

    return $response;
}
