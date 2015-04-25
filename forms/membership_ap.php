<?php

/**
 * @brief: application form
 * @created: 2015
*/

try {
    // config
    #  $email = "info@blindmi.org"; // recipient
    $recipient_email = "billcreswell@gmail.com"; // recipient
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
        html, body, p, label {font:16px Tahoma, Helvetica, san-serif}
        #page {width:768px;}
        h1 {font: bold 32px 'Tahoma';line-height: 32px;}
        label, p, span {margin-left: 10px;margin-left: 10px;}
        input {margin-left: 5px}
        .iwrap{float:left;padding:2px}.iwrap label{display:block}
        .cb {clear:both}
        @media print {
            input {border:none;border-bottom:1px gainsboro solid;}
            input[type=button], input[type=submit], printHide {display:none}
        }
        #Name, #Address, #City, #Email {width:22em}
        #State {width:2em}
        #Zip {width:9em}
        </style>

    </head>

<body >

<div id='page'>

<?php 
if($form!='') { ?>

<form class='printHide' action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="WJTLY5KB37DX2">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<?php }?>
<h1>MCBVI Membership Application</h1>
<p>
    If there is a local chapter in your area, we recommend joining through the chapter.
    Learn more about <a href='/?page=chapters'>local chapters on our website</a>,
    or by calling toll free to 1 888 956-2284, that’s 1 888 95M-CBVI.
</p>
</p>
    You may also join as a member at large by completing this form.
    Fields with an asterisk are required.
</p>
<?php echo $template['smessage'];?>

<form method='post'>

    <fieldset>
        <legend>Your Contact Information</legend>
        <div class='iwrap'>
            <label for='Name'>First and Last Name *</label>
            <input name='name' id='Name' required='true'/>
        </div>
    
        <br class='cb'/>
                
        <div class='iwrap'>
            <label for='Address'>Address</label>
            <input name='address' id='Address'/>
        </div>

        <br class='cb'/>
           
        <div class='iwrap'>
            <label for='City'>City </label>
            <input name='city' id='City'/>
        </div>

        <div class='iwrap'>
            <label for='State'>State</label>
            <input name='state' id='State'/>
        </div>
        
        <div class='iwrap'>
            <label for='Zip'>Zip Code</label>
            <input name='postal_code' id='Zip'/>
        </div>

        <br class='cb'/>
        
        <div class='iwrap'>
            <label for='Phone'>Phone</label>
            <input type='tel' name='phone' id='Phone'/>
        </div>
        
        <div class='iwrap'>
            <label for='Email'>Email</label>
            <input type='email' name='email' id='Email'/>
        </div>
        
    </fieldset>
    
     <fieldset>
        <legend>Communication Preferences</legend>
        <p>I prefer and MCBVI publications in:</p>
        <p>
            <label for='MC'>
                <input type='checkbox' id='MC' name='mcbvi_pub_preference_cassette'/>Get MCBVI Pubs on Cassette
            </label>
            <label for='MP'>
                <input type='checkbox' id='MP' name='mcbvi_pub_preference_large_print'/>Get MCBVI Pubs in Large Print
            </label>
            <label>
                <input type='checkbox' name='mcbvi_pub_preferecnce_braille'/>Get MCBVI Pubs in Braille
            </label>
            <label>
                <input type='checkbox' name='mcbvi_pub_preference_text'/>Get MCBVI Pubs on Computer Text files
            </label>
            <label>
                <input type='checkbox' name='mcbvi_pub_preference_email'/>Get MCBVI Pubs on E-mail
            </label>
        </p>

        <p>I would like to receive the ACB Braille Forum in:</p>
        <p>
            <label>
                <input type='checkbox' name='acb_forum_pub_preference_cassette'/>Get ACB Forum on Cassette
            </label>
            <label>
                <input type='checkbox' name='acb_forum_pub_preference_large_print'/>Get ACB Forum in Large Print
            </label>
            <label>
                <input type='checkbox' name='acb_forum_pub_preferecnce_braille'/>Get ACB Forum in Braille</label>
            <label>
                <input type='checkbox' name='acb_forum_pub_preference_text'/>Get ACB Forum in Computer Text files
            </label>
            <label>
                <input type='checkbox' name='acb_forum_pub_preference_email'/>Get ACB Forum by E-mail
            </label>
        </p>
      
     </fieldset>

    <fieldset>
        <legend>Payment Information</legend>
        <p>General "At Large" Membership is only $10</p>
        <label><input type='checkbox' name='check_payment_enclosed'/>I am sending my annual dues by check or money order</label>
        <p>Make checks payable to the MCBVI in the amount of $10</p>
        <label><input type='checkbox' name='check_online_payment'/>I will pay online</label>
        <label><input type='checkbox' name='receipt_requested'/>Please return receipt for my tax deductible contribution.</label>
     
    
        <p class='printHide'>Submit Form and Pay now by Paypal </p>
        <input type='submit' value='Submit Form and Pay'/>
        <p class='printHide'>The 'subscribe' button will be presented at the top of the page after submission</p>
        <p>Print and Return your MCBVI dues and completed membership application to:</p>
   
        <p>
            All Needs Accounting<br/>
             Matt Livingston, Treasurer<br/>
            7751 Lakeshore Road<br />
            Lakeport, MI 48059
        </p>
        
        <input type='button' value='print' onclick='window.print();return false;'/>
    </fieldset>
</form>

<h2>Pay Dues Online with Paypal</h2>
<form class='printHide' action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="WJTLY5KB37DX2">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</div>
</body>
</html>
