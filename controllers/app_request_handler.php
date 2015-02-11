<?php

use phpish\app;
use phpish\template;

app\get("/", function($req) {
    $data = [];
    return template\compose("home.html", compact('data'), "layout-no-sidebar.html");
});
