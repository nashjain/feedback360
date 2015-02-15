<section class="wrapper style special fade">
    <div class="container">
        <div class="row uniform 50%">
            <div class="6u 12u$(medium)">
                <form action="/auth/login" method="post">
                    <h2>Login</h2>
                    <?php
                        $requested_url = '/';
                        if(array_key_exists('requested_url', $data)) $requested_url =$data['requested_url'];
                        echo "<input type='hidden' name='requested_url' value='$requested_url'>";
                    ?>
                    <div class="row uniform 50%">
                        <div class="3u 12u$(medium)"><label for="email" class="form-label">Email<sup>*</sup></label></div>
                        <div class="7u 12u$(medium)"><input type="email" name="email" id="email" placeholder="Email Address" required></div>
                        <div class="2u$ 12u$(medium)"></div>

                        <div class="3u 12u$(medium)"><label for="password" class="form-label">Password<sup>*</sup></label></div>
                        <div class="7u 12u$(medium)"><input type="password" name="password" id="password" placeholder="Password" required minlength="6"></div>
                        <div class="2u$ 12u$(medium)"></div>

                        <div class="4u 12u$(medium)"></div>
                        <div class="4u 12u$(medium)"><input type="submit" value="Login" class="fit special" /></div>
                        <div class="4u$ 12u$(medium)"></div>

                        <div class="4u 12u$(medium)"></div>
                        <div class="4u 12u$(medium)" style="text-align: center"><a href="/auth/forgot-password" class="fit" style="color: #ffffff">Forgot Password?</a></div>
                        <div class="4u$ 12u$(medium)"></div>
                    </div>
                </form>
            </div>
            <div class="6u$ 12u$(medium)">
                <form id="theForm" action="/auth/registration" method="post">
                    <h2>Register</h2>
                    <div class="row uniform 50%">
                        <div class="4u 12u$(medium)"><label for="name" class="form-label">Name<sup>*</sup></label></div>
                        <div class="8u$ 12u$(medium)"><input type="text" name="name" id="name" placeholder="Name" required minlength="3"></div>

                        <div class="4u 12u$(medium)"><label for="new_email" class="form-label">Email<sup>*</sup></label></div>
                        <div class="8u$ 12u$(medium)"><input type="email" name="email" id="new_email" placeholder="Email Address" required></div>

                        <div class="4u 12u$(medium)"><label for="new_password" class="form-label">Password<sup>*</sup></label></div>
                        <div class="8u$ 12u$(medium)"><input type="password" name="password" id="new_password" placeholder="Password" required minlength="6" autocomplete="off"></div>

                        <div class="4u 12u$(medium)"><label for="confirmation-password" class="form-label">Confirm Password<sup>*</sup></label></div>
                        <div class="8u$ 12u$(medium)"><input type="password" name="confirmation-password" id="confirmation-password" placeholder="Confirm Password" required minlength="6"></div>

                        <div class="4u 12u$(medium)"></div>
                        <div class="8u$ 12u$(medium)"><input type="submit" value="Create Account" class="fit special" /></div>
                    </div>
                </form>
                <?php include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
            </div>
        </div>
    </div>
</section>