<section class="wrapper style special fade">
    <div class="container">
        <form action="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>/member/add" id="theForm" method="post">
            <h2>Add Team Members to <?php echo $data['team_name']?></h2>

            <div class="row uniform 50%">

                <?php include_once TEMPLATE_PATH . "org/inc/team_members_textarea.html.php"?>

                <div class="3u 12u$(medium)"></div>
                <div class="3u 12u$(medium)"><a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>" class="button cancel_button">Cancel</a></div>
                <div class="3u 12u$(medium)"><input type="submit" value="Add" class="button special" /></div>
                <div class="3u$ 12u$(medium)"></div>
            </div>
        </form>
    </div>
</section>


