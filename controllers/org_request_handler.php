<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'team.php';
include_once MODELS_DIR . 'org.php';

app\any("/org[/.*]", function ($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/auth/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/org/create", function ($req) {
    $data = ['teams'=>Team::fetch_all()];
    if (array_key_exists('requested_url', $req['query']))
        $data['requested_url'] = $req['query']['requested_url'];
    return template\compose("org/create.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/org/create", function ($req) {
    $response = Org::create($req['form']);
    if ('success'!= $response)
        set_flash_msg('error', $response);
    return app\response_302($req['form']['requested_url']);
});