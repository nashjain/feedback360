<?php

use phpish\app;
use phpish\template;

app\any("/user/*", function($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/user/change-password", function($req) {
    $data = [];
    return template\compose("user/change-password.html", compact('data'), "layout.html");
});