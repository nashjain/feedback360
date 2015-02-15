<section class="wrapper style special fade">
    <div class="container">
        <form action="/org/<?php echo $data['org_id'] ?>/team/add" id="theForm" method="post">
            <h2>Add New Team to <?php echo $data['org_name']?></h2>
            <div class="row uniform 50%">
                <div class="3u 12u$(medium) form-label"><label for="team_name">Team Name<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)"><input type="text" name="team_name" id="team_name" placeholder="Team Name" required minlength="2"></div>

                <div class="3u 12u$(medium) form-label"><label for="team_owner_name">Team Owner's Name<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)"><input type="text" name="team_owner_name" id="team_owner_name" placeholder="Team Owner's Name" required minlength="2" value="<?php echo Session::get_user_property('name') ?>"></div>

                <div class="3u 12u$(medium) form-label"><label for="team_owner_email">Team Owner's Email<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)"><input type="email" name="team_owner_email" id="team_owner_email" placeholder="Team Owner's Email" required minlength="4" value="<?php echo Session::get_user_property('email') ?>"></div>

                <?php include_once TEMPLATE_PATH . "org/inc/team_members_textarea.html.php"?>

                <div class="3u 12u$(medium)"></div>
                <div class="3u 12u$(medium)"><a href="/org/<?php echo $data['org_id'] ?>/team" class="button cancel_button">Cancel</a></div>
                <div class="3u 12u$(medium)"><input type="submit" value="Create" class="button special" /></div>
                <div class="3u$ 12u$(medium)"></div>
            </div>
        </form>
        <?php include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
    </div>
</section>


