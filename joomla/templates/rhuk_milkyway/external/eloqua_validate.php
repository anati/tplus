<?php

require_once('lib/recaptchalib.php');
require_once('lib/basic_post_request.php');
require_once('config/recaptcha.php');
$response = recaptcha_check_answer ($recaptcha['privatekey'],
    $_SERVER["REMOTE_ADDR"],
    $_POST["recaptcha_challenge_field"],
    $_POST["recaptcha_response_field"]
);

$eloqua_fields = $_POST;
// Remove the Eloqua form meta fields and the reCaptcha fields.
unset($eloqua_fields['eloquaForm']);
unset($eloqua_fields['recaptcha_response_field']);
unset($eloqua_fields['recaptcha_challenge_field']);
if ($response->is_valid) {
    $action = $_POST['eloquaForm']['action'];
    // POST the data to Eloqua.
    $response = basic_post_request($action, $eloqua_fields);
} else {
    if(! isset($_SERVER['HTTP_REFERER'])){
        header( 'Location: /' );
        exit;
    }
    $from_url = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : "/";
    // Remove any prevous query string data.
    $from_url = preg_replace('/\?.*/','', $from_url);
    $eloqua_fields['failed_recaptcha'] = '1';
    $from_url .= "?".http_build_query($eloqua_fields);

    header( 'Location: '.$from_url );
    exit;
}
