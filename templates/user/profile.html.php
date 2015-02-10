<?php include_once TEMPLATE_PATH."inc/gravatar.php";

$img = gravatar_image_link($data['email'],250);
?>
<h2><?php echo $data['name'] ?></h2>
<img src="<?php echo $img ?>">
