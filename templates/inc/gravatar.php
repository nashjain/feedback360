<?php
function gravatar_image_link($email, $size=150, $random=true, $speaker_name='')
{
    $default_image = "mm";
    if(!empty($speaker_name)){
        $file_location = 'assets/img/speakers/'.str_replace(' ', '', $speaker_name).'.png';
        if(file_exists(BASE_PATH.$file_location))
            $default_image = urlencode('http://confengine.com/'.$file_location);
    }
    else if($random) {
        $default_images = array("mm", "identicon", "monsterid", "wavatar", "retro");
        $rand_index = array_rand($default_images);
        $default_image = $default_images[$rand_index];
    }
    return "http://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $size . "&r=pg&d=" . $default_image;
}