<?php
// Load the Contact-Us Eloqua Form.
?>
<div id="contact-us-external-form" style="display: none;">
<?php
    $failed_recaptcha = false;
    if(! empty($_GET) ){
        if(isset($_GET['failed_recaptcha']) && $_GET['failed_recaptcha'])
        {
            $failed_recaptcha = true;
        }
    }
    $template_dir = "templates/{$this->template}/";
    $eloqua_form_config = array(
        'close_form' => false,
        'action' => "/{$template_dir}/external/eloqua_validate.php",
        'use_recaptcha' => true
    );
?>
<?php
if( $failed_recaptcha ){
?>
    <dl id="system-message">
        <dt class="error">Validation Error</dt>
        <dd class="error"><ul><li>The CAPTCHA was not entered correctly.  Please try again.</li></ul></dd>
    </dl>
    <script type="text/javascript">
        var $form = jQuery('#contact-us-external-form');
        if( $form ){
            $form.show();
        }
    </script>
    <?php
}
// Contact form.
require('eloqua_contact_us_form.php');

if( $failed_recaptcha ){
    // Repopulate the contact form when the user fails the ReCAPTCHA.
    require_once('repopulate_contact_us.php');
}
?>
</div>
