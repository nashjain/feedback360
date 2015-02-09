<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . "model.php";

app\get("/", function($req) {
    return app\response_301($_SERVER["REQUEST_URI"]."hello/world");
});

app\get("/hello", function($req) {
    return app\response_301($_SERVER["REQUEST_URI"]."/world");
});

app\get("/hello/{name}", function($req) {
    $name = $req['matches']['name'];
    $data = ModelName::fetch_something($name);
    return template\compose("main.html", compact('data'), "layout.html");
});
