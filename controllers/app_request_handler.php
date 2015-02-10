<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . "model.php";

app\get("/", function($req) {
    $data = [];
    return template\compose("home.html", compact('data'), "layout-no-sidebar.html");
});
