<?php
function purify($dirty_html) {
    include_once VENDOR_PATH . "ezyang/htmlpurifier/library/HTMLPurifier.autoload.php";
    $config = HTMLPurifier_Config::createDefault();
    $config->set('Cache.DefinitionImpl', null);
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($dirty_html);
}