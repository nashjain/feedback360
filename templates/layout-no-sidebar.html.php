<?php include_once TEMPLATE_PATH."inc/header.html.php"; ?>
<body class="landing">
<?php include_once TEMPLATE_PATH."inc/menu.html.php"; ?>
<div id='session_alert_msgs'><?php echo display_flash_msg()?></div>
<?php echo $content; ?>
<?php include_once TEMPLATE_PATH."inc/footer.html.php"; ?>