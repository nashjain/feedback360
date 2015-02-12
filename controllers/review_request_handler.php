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