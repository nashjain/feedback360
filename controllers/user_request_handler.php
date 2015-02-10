<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'user.php';

app\any("/user/[change-password|update-profile.*]", function($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/user/change-password", function($req) {
    $data = User::fetch_email_and_activation_token();
    return template\compose("user/change-password.html", compact('data'), "layout_without_sidebar.html");
});

app\get("/user/update-profile[/{username}]", function($req) {
    $username = '';
    if(array_key_exists('username', $req['matches']))
        $username = $req['matches']['username'];
    if(empty($username) or !Session::is_admin())
        $username = Session::get_user_property('username');
    $data = User::fetch_profile_data($username);
    return template\compose("user/update-profile.html", compact('data'), "layout.html");
});

app\post("/user/update-profile", function($req) {
    $response = User::create_profile($req['form']);
    $username = $req['form']['username'];
    if(empty($username) or !Session::is_admin())
        $username = Session::get_user_property('username');
    if($response=='Success') {
        set_flash_msg('success', 'Successfully updated your profile.');
        return app\response_302('/user/'.$username);
    } elseif ($response=='ResetEmail') {
        set_flash_msg('success', 'Successfully updated your profile.<br>Since you have updated your email address, an verification email has been sent.<br>Currently you are logged out of the system. Verify your email address to proceed.');
        return app\response_302('/');
    }
    set_flash_msg('error', $response);
    $data = User::fetch_profile_data($username);
    $data['submitted_form'] = $req['form'];
    return template\compose("user/update-profile.html", compact('data'), "layout.html");
});

app\get("/user/{username}", function($req) {
    $user_key = $req['matches']['username'];
    if(empty($user_key)) return app\response_302('/');
    $user_info = User::display_profile($user_key);
    if(!$user_info){
        $data = array();
        return app\response_404(template\compose("common/404.html", compact('data'), "layout_without_sidebar.html"));
    }
    include_once MODELS_DIR . 'story.php';
    include_once MODELS_DIR . 'label.php';
    $topic = $user_info['name'];
    $labels = Label::fetch_top_label_associated_with_user($user_key);
    $upvoted_stories = Story::fetch_stories_upvoted_by(array($user_key));
    $nominated_stories = Story::fetch_stories_nominated_by($user_key);
    $commented_stories = Story::fetch_stories_commented_by($user_key);
    $stories = array('proposed' => $nominated_stories, 'liked' => $upvoted_stories, 'commented' => $commented_stories);
    $conf_details = Conference::extract_conf_details($nominated_stories);
    $data = array(
        'user_info' => $user_info,
        'topic' => $topic,
        'labels' => $labels,
        'stories' => $stories,
        'conf_details' => $conf_details,
        'quick_help_buttons'=>array('edit_profile'));

    return template\compose("user/profile.html", compact('data'), "layout.html");
});