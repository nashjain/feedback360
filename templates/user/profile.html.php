<?php include_once TEMPLATE_PATH . "inc/gravatar.php";

$img = gravatar_image_link($data['email'], 250);
?>

<section class="wrapper style fade">
    <div class="container">
        <span class="image left"><img src="<?php echo $img ?>" alt="<?php echo $data['name'] ?>"/></span>

        <h3><?php echo $data['name'] ?></h3>
        <h4><?php echo $data['title'] ?>, <?php echo $data['organization'] ?></h4>

        <div><?php echo $data['bio'] ?></div>
    </div>
</section>
