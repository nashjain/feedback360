<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'team.php';

app\any("/team[/.*]", function ($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/auth/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/team/create", function ($req) {
    $data = [];
    if (array_key_exists('requested_url', $req['query']))
        $data['requested_url'] = $req['query']['requested_url'];
    return template\compose("org/create.html", compact('data'), "layout.html");
});