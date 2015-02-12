<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'review.php';

app\any("/review[/.*]", function ($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/auth/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/review", function ($req) {
    $data = [];
    return template\compose("review/dashboard.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/review/pending", function ($req) {
    $data = Review::pending();
    return template\compose("review/pending.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/review/given", function ($req) {
    $data = Review::given();
    return template\compose("review/given.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/review/received", function ($req) {
    $data = Review::received();
    return template\compose("review/received.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/review/give/{id}", function ($req) {
    $id = $req['matches']['id'];
    if(!Review::is_the_reviewer_for($id)) {
        set_flash_msg('error', 'You are not authorised to provide feedback on this review.');
        return app\response_302('/review/pending');
    }
    $ratings = Review::$ratings;
    $competencies = Review::fetch_competencies_for($id);
    $data = ['id'=>$id, 'competencies'=> $competencies, 'ratings'=>$ratings];
    return template\compose("review/give_feedback.html", compact('data'), "layout-no-sidebar.html");
});