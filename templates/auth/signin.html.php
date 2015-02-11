<section class="wrapper style special fade">
    <div class="container">
        <div class="row">
            <div class="6u 12u$(medium)">
                <form action="/auth/login" method="post" class="form-signin login-form" align="left">
                    <h2 class="form-signin-heading" style="margin-bottom: 15px;">Login</h2>
                    <?php
                        $requested_url = '/';
                        if(array_key_exists('requested_url', $data)) $requested_url =$data['requested_url'];
                        echo "<input type='hidden' name='requested_url' value='$requested_url'>";
                    ?>
                    <div class="control-group">
                        <label for="email">Email<sup>*</sup></label>

                        <div class="controls">
                            <input type="email" name="email" id="email" placeholder="Email Address" required>
                        </div>
                    </div>
                    <br>

                    <div class="control-group">
                        <label for="password">Password<sup>*</sup></label>

                        <div class="controls">
                            <input type="password" name="password" id="password" placeholder="Password" required minlength="6">
                        </div>
                    </div>
                    <br>

                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" value="Login" class="fit special" />
                        </div>
                    </div>
                    <hr>
                    <div style="text-align: center"><a href="/auth/forgot-password" class="fit special" style="color: #ffffff">Forgot Password?</a></div>
                </form>
            </div>
            <div class="6u 12u$(medium)">
                <form id="theForm" action="/auth/registration" method="post" class="form-signin registration-form" align="left">
                    <h2 class="form-signin-heading" style="margin-bottom: 15px;">Register</h2>
                    <div class="control-group">
                        <label for="email">Name<sup>*</sup></label>

                        <div class="controls">
                            <input name="name" type="text" placeholder="Name" required minlength="3" autocomplete="off">
                        </div>
                    </div>
                    <br>

                    <div class="control-group">
                        <label for="new_email">Email<sup>*</sup></label>

                        <div class="controls">
                            <input type="email" name="email" id="new_email" placeholder="Email Address" autocomplete="off" required>
                        </div>
                    </div>
                    <br>

                    <div class="control-group">
                        <label for="new_password">Password<sup>*</sup></label>

                        <div class="controls">
                            <input type="password" name="password" id="new_password" placeholder="Password" required minlength="6" autocomplete="off">
                        </div>
                    </div>
                    <br>

                    <div class="control-group">
                        <label for="confirmation-password">Confirm Password<sup>*</sup></label>

                        <div class="controls">
                            <input type="password" name="confirmation-password" id="confirmation-password" placeholder="Confirm Password" required minlength="6">
                        </div>
                    </div>
                    <br>

                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" value="Create Account" class="fit special" />
                        </div>
                    </div>
                </form>
                <?php include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
            </div>
        </div>
    </div>
</section>