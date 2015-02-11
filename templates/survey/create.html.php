<?php
include_once TEMPLATE_PATH. "inc/html_helper.php";

$org_ids = build_options_from($data['org_ids']);
$teams = build_options_from($data['teams']);
$competencies = $data['competencies'];
?>

<section class="wrapper style special fade">
    <div class="container">
        <form action="/survey/create" id="theForm" method="post">
            <h2>Create a Survey</h2>

            <div class="row uniform 50%">
                <div class="3u 12u$(medium) form-label"><label for="name">Survey Name<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)"><input type="text" name="name" id="name" placeholder="Survey Name (Ex: 1st Quarter Review)" required minlength="2"></div>

                <div class="3u 12u$(medium) form-label"><label for="org_id">Org. Name<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)">
                    <div class="select-wrapper">
                        <select name="org_id" id="org_id">
                            <?php echo $org_ids; ?>
                        </select>
                    </div>
                </div>

                <div class="3u 12u$(medium) form-label"><label for="team_id">Team Name<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)">
                    <div class="select-wrapper">
                        <select name="team_id" id="team_id">
                            <?php echo $teams; ?>
                        </select>
                    </div>
                </div>

                <div class="3u 12u$(medium) form-label"><label>Competencies<sup>*</sup></label></div>
                <div class="9u$ 12u$(medium)">
                    <?php foreach($competencies as $competency) {
                        $unique_id = "competency_".$competency['id'];
                        ?>
                        <div class="12u$ 12u$(medium)">
                            <input type="checkbox" id="<?php echo $unique_id; ?>" name="competencies[]" value="<?php echo $competency['id']; ?>" checked="">
                            <label class="checkbox-label" for="<?php echo $unique_id; ?>"><b><?php echo $competency['name']; ?></b><br/><?php echo $competency['description']; ?></label>
                        </div>
                    <?php } ?>
                </div>

                <div class="3u 12u$(medium)"><a href="/">Cancel</a></div>
                <div class="3u 12u$(medium)"></div>
                <div class="3u 12u$(medium)"><input type="submit" value="Create" class="fit special" /></div>
                <div class="3u$ 12u$(medium)"></div>
            </div>
        </form>
        <?php include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
    </div>
</section>


