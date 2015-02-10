<?php include_once TEMPLATE_PATH."inc/header.html.php"; ?>
<body>
<?php include_once TEMPLATE_PATH."inc/menu.html.php"; ?>

    <!-- Main -->
    <div id="main" class="wrapper style1">
        <div class="container">
            <header class="major">
                <h2>Title</h2>
            </header>
            <div class="row 150%">
                <div class="3u 12u$(medium)">

                    <!-- Sidebar -->
                    <section id="sidebar">
                        <section>
                            <h3>Magna Feugiat</h3>
                            <p>Sed tristique purus vitae volutpat commodo suscipit amet sed nibh. Proin a ullamcorper sed blandit. Sed tristique purus vitae volutpat commodo suscipit ullamcorper commodo suscipit amet sed nibh. Proin a ullamcorper sed blandit..</p>
                            <footer>
                                <ul class="actions">
                                    <li><a href="#" class="button">Learn More</a></li>
                                </ul>
                            </footer>
                        </section>
                        <hr />
                        <section>
                            <a href="#" class="image fit"><img src="/static/images/pic06.jpg" alt="" /></a>
                            <h3>Amet Lorem Tempus</h3>
                            <p>Sed tristique purus vitae volutpat commodo suscipit amet sed nibh. Proin a ullamcorper sed blandit. Sed tristique purus vitae volutpat commodo suscipit ullamcorper sed blandit lorem ipsum dolore.</p>
                            <footer>
                                <ul class="actions">
                                    <li><a href="#" class="button">Learn More</a></li>
                                </ul>
                            </footer>
                        </section>
                    </section>

                </div>
                <div class="9u$ 12u$(medium) important(medium)">
                    <!-- Content -->
                    <section id="content">
                        <?php echo display_flash_msg() . $content; ?>
                    </section>

                </div>
            </div>
        </div>
    </div>
<?php include_once TEMPLATE_PATH."inc/footer.html.php"; ?>