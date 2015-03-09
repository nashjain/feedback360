<?php

use phpish\app;
use phpish\template;

app\get("/", function($req) {
    $data = [];
    return template\compose("index.html", compact('data'), "layout.html");
});
