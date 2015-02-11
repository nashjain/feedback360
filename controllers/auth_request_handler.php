<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'user.php';

app\get("/auth/login", function ($req) {
    $data = [];
    if (array_key_exists('requested_url', $req['query']))
        $data['requested_url'] = $req['query']['requested_url'];
    if (array_key_exists('msg', $req['query']))
        set_flash_msg('error', $req['query']['msg']);
    return template\compose("auth/signin.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/auth/login", function ($req) {
    $response = User::authenticate_user($req['form']);
    if ('Success'!= $response)
        set_flash_msg('error', $response);
    return app\response_302($req['form']['requested_url']);
});

app\get("/auth/logout", function ($req) {
    User::logout();
    return app\response_302('/');
});

app\post("/auth/registration", function ($req) {
    $data = ['email'=> $req['form']['email']];
    $errors = User::register($req['form']);
    if (empty($errors))
        return template\compose("auth/email-authentication.html", compact('data'), "layout-no-sidebar.html");
    $error_msg = '';
    foreach($errors as $input=>$error) {
        $error_msg .= "<b>Error: $input</b>&nbsp;&nbsp;$error<br/>";
    }
    set_flash_msg('error', $error_msg);
    return app\response_302('/auth/login');
});

app\get("/auth/email-confirmation", function ($req) {
    $query = $req['query'];
    $message = User::verify_email_address($query);
    set_flash_msg($message['state'], $message['text']);
    if($message['state']=='reset_pwd') {
        $data = ['user_details'=>User::fetch_user_details('email', $query['email'])];
        return template\compose("auth/reset-password.html", compact('data'), "layout-no-sidebar.html");
    }
    return app\response_302('/auth/login');
});

app\get("/auth/resend-verification-email", function ($req) {
    $results = User::resend_verification_email($req['query']);
    set_flash_msg($results[0], $results[1]);
    return app\response_302('/login');
});

app\get("/auth/forgot-password", function ($req) {
    $data = [];
    return template\compose("auth/forgot-password.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/auth/forgot-password", function ($req) {
    $data = [];
    $results = User::process_forgot_password_request($req['form']['email']);
    set_flash_msg($results[0], $results[1]);
    return template\compose("auth/forgot-password.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/auth/reset-password", function ($req) {
    $data = [];
    $results = User::process_password_reset_request($req['query']);
    if (is_array($results) && 'error' == current($results)) {
        set_flash_msg('error', $results[1]);
        return app\response_302('/auth/forgot-password');
    }
    $data['user_details'] = $results;
    return template\compose("auth/reset-password.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/auth/reset-password", function ($req) {
    $form = $req['form'];
    $results = User::reset_password($form);
    set_flash_msg($results[0], $results[1]);
    if ('error' != $results[0])
        return app\response_302('/auth/login');
    $data = ['user_details'=>$form];
    return template\compose("auth/reset-password.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/auth/unsub/{email_id}", function($req) {
    $email_id = $req['matches']['email_id'];
    Email::unsub($email_id);
    set_flash_msg('success', "Thank you! Your email: $email_id will no longer receive updates from us.");
    return app\response_302("/");
});

app\post("/auth/subscribe", function($req) {
    $email_id = $req['form']['email'];
    if(!empty($email_id)){
        Email::enroll($email_id);
        set_flash_msg('success', "Thank you! We'll keep you posted via your email: $email_id.");
    }
    return app\response_302("/");
});