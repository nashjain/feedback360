<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'user.php';

app\any("/user/[change-password|update-profile.*]", function($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/auth/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/user/change-password", function($req) {
    $data = User::fetch_email_and_activation_token();
    return template\compose("user/change-password.html", compact('data'), "layout.html");
});

app\get("/user/update-profile", function($req) {
    $username = Session::username();
    $data = User::fetch_profile_data($username);
    if($data['active']==0)
        set_flash_msg('error', 'Your account is not active, please check your email for account activation email.');
    return template\compose("user/update-profile.html", compact('data'), "layout.html");
});

app\post("/user/update-profile", function($req) {
    $username = Session::username();
    $response = User::update_profile($username, $req['form']);
    if($response=='success') {
        set_flash_msg('success', 'Successfully updated your profile.');
        return app\response_302('/user/'.$username);
    }
    if ($response=='ResetEmail') {
        set_flash_msg('success', 'Successfully updated your profile.<br>Since you have updated your email address, an verification email has been sent.<br>Currently you are logged out of the system. Verify your email address to proceed.');
        return app\response_302('/');
    }
    set_flash_msg('error', $response);
    return app\response_302('/user/update-profile');
});

app\get("/user/{username}", function($req) {
    $user_key = $req['matches']['username'];
    if(empty($user_key)) return app\response_302('/');
    $data = User::display_profile($user_key);
    if(empty($data)){
        return app\response_404(template\compose("common/404.html", compact('data'), "layout.html"));
    }
    return template\compose("user/profile.html", compact('data'), "layout.html");
});