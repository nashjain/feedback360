<?php

require __DIR__ . '/../vendor/swiftmailer/swiftmailer/lib/swift_required.php';

function mailer_setup()
{
    $transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 587)
        ->setUsername(EMAIL_USER_NAME)
        ->setPassword(EMAIL_PWD);
    return Swift_Mailer::newInstance($transport);
}

function send_mail($details, $objective, $conf_details=['email_address'=>'naresh@feedback360.co','name_in_email'=>'Feedback360'])
{
    if(is_string($details['email'] and !($objective=='registration' or $objective=='email_verification'))){
        include_once MODELS_DIR . "email.php";
        if(Email::is_not_active($details['email'])){
            $details = ['email'=>'naresh@feedback360.co', 'name'=>'Feedback360', 'subject'=>'Did not send email to un-subscribed user', 'message'=> "Can not send $objective email to un-subscribed email: ".$details['email']];
            $objective = 'unsub_notification';
        }
    }
    $mailer = mailer_setup();
    $subject = '';
    $message_body = '';
    switch ($objective) {
        case 'registration':
            $subject = 'Feedback360 Registration';
            $message_body = build_registration_message($details);
            break;
        case 'new_review':
            $subject = '[Feedback360] New Review is assigned to You';
            $message_body = build_new_review_message();
            break;
        case 'email_verification':
            $subject = 'Feedback360 Email Verification';
            $message_body = build_registration_message($details, true);
            break;
        case 'password-recovery':
            $subject = 'Password Recovery Mail for your Feedback360 Account';
            $message_body = build_password_recovery_message($details, 'reset-password');
            break;
        case 'unsub_notification':
            $subject = $details['subject'];
            $message_body = $details['message'];
            break;
    }
    $message_body = email_header($details) . $message_body . email_footer($details['email']);
    $conf_self = [$conf_details['email_address'] => $conf_details['name_in_email']];
    $message = Swift_Message::newInstance('Feedback360')
        ->setSubject($subject)
        ->setFrom(['naresh@feedback360.co' => 'Feedback360'])
        ->setReplyTo($conf_self)
        ->setBody($message_body, 'text/html');

    try {
        $bcc_address  = '';
        if(is_array($details['email'])) {
            $email_addresses = email_addresses($details['email']);
            if(array_key_exists('email_mode', $details) and $details['email_mode']=='bcc'){
                $to_address = $conf_self;
                $bcc_address = $email_addresses;
            }
            else{
                $to_address = $email_addresses;
            }
        } else {
            $to_address = [$details['email'] => $details['name']];
        }
        $message->setTo($to_address);
        if(!empty($bcc_address))
            $message->setBcc($bcc_address);
            $mailer->send($message);
    }catch (Swift_SwiftException $e) {
        return $e->getMessage();
    }
    return 'success';
}

    function email_addresses($all) {
        $result = [];
        foreach($all as $email_record){
            $result[$email_record['email']]=$email_record['name'];
        }
        return $result;
    }

function build_registration_message($user_details, $email_activation_only = false)
{
    $activation_url = "http://" . $_SERVER['HTTP_HOST'] . "/auth/email-confirmation?email=" . $user_details['email'] . "&activation_token=" . $user_details['activation_token'];
    if ($email_activation_only)
        return 'Verify your new email address by clicking on this link: <a href="' . $activation_url . '"> ' . $activation_url . ' </a>';
    return 'Welcome aboard, to the <b>Feedback360 System</b>. <br />
        <br />
        Feedback360 is a platform for you to receive and give 360&deg; feedback to your colleagues. <br />
        <br />
        To get started, click on this link to activate your account and start using Feedback360: <a href="' . $activation_url . '"> ' . $activation_url . ' </a><br />
        <br />
        You have got this email either because you signed up for this service or you were enrolled by your manager. Please do the needful.';
}

function build_new_review_message()
{
    $review_url = "http://" . $_SERVER['HTTP_HOST'] . "/review/pending";
    return 'A new review has been assigned to you in <b>Feedback360 System</b>. <br /><br />
        To get started, please click on this link: <a href="' . $review_url . '"> ' . $review_url . ' </a>';
}

function build_password_recovery_message($user_details, $action)
{
    $activation_url = "http://" . $_SERVER['HTTP_HOST'] . "/auth/" . $action . "?email=" . $user_details['email'] . "&activation_token=" . $user_details['activation_token'];
    return 'Your account is safe with us, the following is the requested password recovery link to reset your account&#39;s password on Feedback360.<br/>
            <br/>
            To reset your password, Click on this <a href="' . $activation_url . '">reset password link</a><br/><br/> OR<br/><br/> Copy and Paste this link in your browser window,<br/><br/> &nbsp;&nbsp; <b><i>' . $activation_url . '</b></i>
            <br/><br/>
            Let us know if you come across any issue, in resetting your password or while using Feedback360 System, we will be glad to help.<br/>';
}

function email_footer($email)
{
    $unsublink = "";
    if(is_string($email))
        $unsublink = "<br/><a href='*|UNSUB:http://feedback360.co/auth/unsub/$email|*'>Click here to un-subscribe from all email updates from Feedback360.</a>";
    return "<br /><br />
            Regards,<br />
            Feedback360 Team<br />
            <a href='mailto:naresh@feedback360.co'>naresh@feedback360.co</a><br/>
            <a href='http://feedback360.co'>http://feedback360.co</a><br />
            <div style='font-size:10px;margin-top:10px;'>You are receiving this email from Feedback360.
			Thanks for using Feedback360. $unsublink</div>
          </body>
        </html>";
}

function email_header($user_details)
{
    return '<html>
            <head><meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"></head>
            <body bgcolor="#FFFFFF" text="#000000">Dear ' . $user_details['name'] . ',<br /><br />';
}