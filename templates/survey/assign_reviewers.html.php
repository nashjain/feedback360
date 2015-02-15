<section class="wrapper style special fade">
    <div class="container">
        <form action="/survey/<?php echo $data['survey_id']; ?>/reviewers" id="theForm" method="post">
            <h2>Assign Reviewers for <?php echo $data['survey_name'] ; ?></h2>

            <input type="hidden" name="survey_name" value="<?php echo $data['survey_name']; ?>">
            <input type="hidden" name="org_id" value="<?php echo $data['org_id']; ?>">
            <input type="hidden" name="team_id" value="<?php echo $data['team_id']; ?>">

            <div class="row uniform 50%">
                <?php foreach($data['team_members'] as $reviewee_id=>$reviewee_name) { ?>
                <div class="3u 12u$(medium) form-label"><label><?php echo $reviewee_name; ?></label></div>
                <div class="9u$ 12u$(medium)">
                    <div class="12u$ 12u$(medium)">
                        <?php foreach($data['employees'] as $employ_id=>$employ_name) {
                            if($employ_id==$reviewee_id) continue;
                            $unique_id = $reviewee_id . '_'. $employ_id;
                        ?>
                            <input type="checkbox" id="<?php echo $unique_id ; ?>" name="<?php echo $reviewee_id; ?>[]" value="<?php echo $employ_id; ?>">
                            <label class="checkbox-label" for="<?php echo $unique_id; ?>"><?php echo $employ_name; ?></label>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <div class="3u 12u$(medium)"></div>
                <div class="3u 12u$(medium)"><a href="/" class="button cancel_button">Cancel</a></div>
                <div class="3u 12u$(medium)"><input type="submit" value="Assign" class="button special" /></div>
                <div class="3u$ 12u$(medium)"></div>
            </div>
        </form>
    </div>
</section>


