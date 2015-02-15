<?php
include_once TEMPLATE_PATH. "inc/html_helper.php";
$roles = build_options_from($data['roles'], $data['current_role']);
?>

<section class="wrapper style special fade">
    <div class="container">
        <form action="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>/member/<?php echo $data['username'] ?>/update" id="theForm" method="post">
            <h2>Update Membership details of <?php echo $data['member_name']?></h2>
            <input type="hidden" name="current_role" value="<?php echo $data['current_role']?>">
            <div class="row uniform 50%">
                <div class="6u 12u$(medium) form-label"><label for="role">Role<sup>*</sup></label></div>
                <div class="3u 12u$(medium)">
                    <div class="select-wrapper">
                        <select name="role" id="role">
                            <?php echo $roles; ?>
                        </select>
                    </div>
                </div>
                <div class="3u$ 12u$(medium)"></div>

                <div class="3u 12u$(medium)"></div>
                <div class="3u 12u$(medium)"><a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>" class="button cancel_button">Cancel</a></div>
                <div class="3u 12u$(medium)"><input type="submit" value="Update" class="button special" /></div>
                <div class="3u$ 12u$(medium)"></div>
            </div>
        </form>
    </div>
</section>