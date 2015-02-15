<section class="wrapper style special fade">
    <div class="container 50%">
        <h2>Forgot Password?</h2>
        <h3>Don't worry, we can send a recovery email right away!</h3>
        <br/>
        <form method="post" action="/auth/forgot-password" id="theForm" class="container 50%">
            <div class="row uniform 50%">
                <div class="7u 12u$(medium)"><input type="email" name="email" id="email" placeholder="Your Email Address" required/></div>
                <div class="5u$ 12u$(medium)"><input type="submit" value="Send Recovery Mail" class="button special" /></div>
            </div>
        </form>
        <br/>
    </div>
</section>
<?php include_once TEMPLATE_PATH . "inc/jquery_validator.php"; ?>