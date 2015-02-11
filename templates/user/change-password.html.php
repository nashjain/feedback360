<section class="wrapper style special fade">
    <div class="container 50%">
        <div class="alert alert-error" style="text-align: left;display: none;">
            <span>Confirmation Password does not match the Password entered.</span>
        </div>
        <form action="/auth/reset-password" id="password-change-form" method="post">
            <h2>Update Password</h2>
            <input name="email" type="hidden" value="<?php echo $data['email'];?>">
            <input name="activation_token" type="hidden" value="<?php echo $data['activation_token'];?>">

            <div class="row uniform 50%">
                <div class="6u 12u$(medium) form-label"><label for="new_password">New Password<sup>*</sup></label></div>
                <div class="6u$ 12u$(medium)"><input type="password" name="password" id="password" placeholder="New Password" required minlength="6" autocomplete="off"></div>

                <div class="6u 12u$(medium) form-label"><label for="confirmation-password">Confirm Password<sup>*</sup></label></div>
                <div class="6u$ 12u$(medium)"><input type="password" name="confirmation-password" id="confirmation-password" placeholder="Confirm Password" required minlength="6"></div>

                <div class="6u 12u$(medium)"></div>
                <div class="6u$ 12u$(medium)"><input type="submit" value="Update" class="fit special" /></div>
            </div>
        </form>
        <?php //include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
    </div>
    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $("#password-change-form").submit(function(){
            if($('input[name="password"]').val() != $('input[name="confirmation-password"]').val()){
                var $alert = $('.alert-error');
                $alert.show();
                setTimeout(function () {
                    $alert.hide();
                }, 3000);
                return false;
            }
            return $("#password-change-form").validate();
        });
    </script>
</section>


