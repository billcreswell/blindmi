<?php

/**
 * @brief: application form
 * @created: 2015
*/

try {
    // config
    #  $email = "info@blindmi.org"; // recipient
    $recipient_email = "info@blindmi.org"; // recipient
    $site = "BlindMI";
    $pagetitle = "Application for Membership";
    $submit_message = "Thank you for your interest  -your application has been submitted.";

    // process
    $timestamp = time();
    $date = date('Y-m-d');
    $submit_message = '';
    $form = '';
    $subject = '';
    $message = '';
    $headers = '';

    // template
    $template['date'] = $date;
    $template['smessage'] = $submit_message;
    $template['timestamp'] = $timestamp;
    $template['title']  = $pagetitle;

    // where from
    if (isset($_SERVER['SERVER_NAME'])) {
        $site = $_SERVER['SERVER_NAME'];
    }

    // check for post values, and timestamp of less than 20 minutes
    // @todo verify separately
    if (
        isset( $_POST['name'] )
        && ($_POST['name'] != '')
        && ($_POST['name'] != $_POST['email'])
        //&& ($timestamp - $_REQUEST['t'] < 1200)
    ) {

        // check for spam
        $_http = 0;
        $_href = 0;

        foreach ($_POST as $key => $value) {
            $key = $value;
            // Annoying URL spams in comments any field
            $_http += substr_count($value, 'http');
            $_href += substr_count($value, 'href');
        }

        if ($_http > 1 OR $_href > 1) {
            die("Sorry, contains too many links or banned words.");
        }

        // process form results
        foreach ($_POST as $key => $value) {
            $form.= ucwords(str_replace('_', ' ', $key));
            if($value != 'on') {
                $form.= ': ' . $value;
            }
            $form.= "\r\n";
            $breaklines=array('provide_information_to');
            if (in_array($key, $breaklines)) {
                $form.="========\r\n";
            }
        }

        // build email
        $applicant_mail = (isset($_REQUEST['email']) && filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) ?  $_REQUEST['email'] : false;
        $form_email = ($applicant_mail) ? $applicant_mail : $recipient_email;
        $subject = $pagetitle . ' for '. $_POST['name'];
        $headers .= 'From: webmaster@blindmi.org' . "\r\n";
        $message = $form;

        try {

            // mail application

            if (!mail($recipient_email, $subject, $message, $headers)) {
                $template['smessage'] = 'email not sent';
            } else {
                $template['smessage'] = 'Application sent';
            // mail acknowledgement to sender
                if ($applicant_mail) {
                    if (mail($applicant_mail, $subject, $submit_message, $headers)) {
                        $template['smessage'] = 'Application and confirmation sent';
                    }
                }
            }

        } catch (Exception $e) {
             echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    //mail('billcreswell@gmail.com', 'Error on Post', json_encode($_POST), $headers);
}
?><!doctype html>
<html lang="en">
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>

        <title>M C B V I Membership Application</title>

        <style type='text/css'>
        body {margin-top: 0px;margin-left: 0px;}
        #page {position:relative; margin: 48px 0px 117px 48px;padding: 0;border: none;width: 768px;}
        h1 {font: bold 32px 'Tahoma';line-height: 32px;}
        label, p, span {margin-left: 10px;font: 16px 'Tahoma';margin-left: 10px;line-height: 19px;}
        input {margin-left: 5px}
        </style>

    </head>

<body >


<div id='page'>


<h1>MCBVI Membership Application</h1>

<?php echo $template['smessage'];?>

<form method='post'>
<h2>Your Information</h2>

<p>
    <label>Name <input name='name' id='Name' /></label>
</p>

<p>
    <label>Address <input name='address' id='Address'/></label>
    <label>City <input name='city' id='City'/></label>
    <label>Zip Code <input name='postal_code' id='Zip'/></label>
</p>

<p>
    <label>Phone <input type='tel' name='phone' id='Phone'/></label>
    <label>Email (Optional)<input type='email' name='email' id='Email'/></label>
</p>

<p>Please check your preferences.</p>
<p>
    <label> <input type='checkbox' name='contact_me_for_more_information' id='ContactMe'/>
    I would like to be contacted for more information.</label>
</p>

<h2>Additional Contact Information</h2>

<p>
    <label><input type='checkbox' name='provide_information_to'/>
    Please contact and provide additional information to the individual, Business, Church, Hospital, nursing home, or agency I have also identified here.</label>
</p>

<p>
    <label>Contact/Organization Name<input name='additional_contact' id='AdditionalContact'/></label>
</p>

<p>
    <label>Address <input name='additional_contact_address' id='AdditionalContactAddress'/></label>
</p>

<p>
    <label>City <input name='additional_contact_city' id='AdditionalContactCity'/></label>
    <label>Zip Code <input name='additional_contact_zip' id='AdditionalContactZip'/></label>
</p>

<p>
    <label>Phone <input name='additional_contact_phone' id='AdditionalContactPhone'/></label>
    <label>Email (Optional)<input type='tel' name='additional_contact_phone' id='AdditionalContactPhone'/></label>
</p>

<h2>Membership</h2>

<p>
    <p>General "At Large" Membership is only $10</p>
    <label><input type='checkbox' name='check_payment_enclosed'/>I am sending my annual dues by check or money order</label>
    <p>Make checks payable to the MCBVI in the amount of $10</p>
</p>

<p>
    <label><input type='checkbox' name='check_online_payment'/>I will pay online</label>
</p>

<h2>Publication Format Preference</h2>

<p>I prefer and read publications in:</p>

<p>
    <label><input type='checkbox' name='publication_preference_cassette'/>Cassette</label>
    <label><input type='checkbox' name='publication_preference_large_print'/>Large Print</label>
    <label><input type='checkbox' name='publication_preferecnce_braille'/>Braille</label>
    <label><input type='checkbox' name='publication_preference_text'/>Computer Text files</label>
    <label><input type='checkbox' name='publication_preference_email'/>E-mail.</label>
</p>

<p>
    <label><input type='checkbox' name='receipt_requested'/>Please return receipt for my tax deductible contribution.</label>
</p>


<h2>Payment Information</h2>


<p>Submit Form and Pay now by Paypal </p>
<input type='submit' value='Submit Form and Pay'/>
<p>Print and Return your MCBVI dues and completed membership application to:</p>

<p>
    All Needs Accounting<br/>
    Matt Livingston, Treasurer<br/>
    7751 Lakeshore Road<br />
    Lakeport, MI 48059
</p>

<p>
    Learn more about local chapters by calling toll free to 1 888 956-2284, that’s 1 888 95M-CBVI.
</p>
<p>Check out website at <a href='http://blindmi.org/'>http://blindmi.org</a></p>
<p>Thank You.</p>

</form>
</div>
</body>
</html>
