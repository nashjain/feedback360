<?php

include_once TEMPLATE_PATH . "inc/gravatar.php";
include_once TEMPLATE_PATH . "inc/html_purifier.php";
$img = gravatar_image_link($data['email'], 250);
?>

<section class="wrapper style fade">
    <div class="container">
        <span class="image left"><img src="<?php echo $img ?>" alt="<?php echo strip_tags($data['name']) ?>"/></span>

        <h3><?php echo strip_tags($data['name']) ?></h3>
        <h4><?php echo strip_tags($data['title']) ?>, <?php echo strip_tags($data['organization']) ?></h4>

        <div><?php echo purify($data['bio']) ?></div>
    </div>
</section>
