<?php
include_once TEMPLATE_PATH. "inc/html_helper.php";
$ratings = build_options_from_map($data['ratings']);
?>

<section class="wrapper style special fade">
    <div class="container">
        <h2>Provide Feedback for NAME</h2>
        <?php if(!array_key_exists('competencies', $data) or empty($data['competencies'])) {?>
            <h4>Sorry! There are no competencies identified for giving feedback. Please contact your manager.</h4>
        <?php } else { ?>
            <form action="/review/give/<?php echo $data['id']?>" id="theForm" method="post">
                <input name="id" type="hidden" value="<?php echo $data['id'];?>">

                <div class="row uniform 50%">
                    <?php foreach($data['competencies'] as $competency) {
                        $id = $competency['id'];
                        $rating_id = $id.'_rating';
                        $confidence_id = $id.'_sc';
                        $effectiveness_id = $id.'_ie';
                    ?>
                        <div class="12u$ 12u$(medium)"><b><?php echo $competency['name'] . "</b>: ". $competency['description']?></div>

                        <div class="3u 12u$(medium) form-label"><label for="<?php echo $rating_id?>"><?php echo $competency['name']?><sup>*</sup></label></div>
                        <div class="9u$ 12u$(medium)">
                            <select name="<?php echo $rating_id?>" id="<?php echo $rating_id?>">
                                <?php echo $ratings; ?>
                            </select>
                        </div>

                        <div class="3u 12u$(medium) form-label"><label for="<?php echo $confidence_id?>">Strengthen Confidence</label></div>
                        <div class="9u$ 12u$(medium)">
                            <textarea name="<?php echo $confidence_id?>" id="<?php echo $confidence_id?>"></textarea>
                        </div>

                        <div class="3u 12u$(medium) form-label"><label for="<?php echo $effectiveness_id?>">Increase Effectiveness</label></div>
                        <div class="9u$ 12u$(medium)">
                            <textarea name="<?php echo $effectiveness_id?>" id="<?php echo $effectiveness_id?>"></textarea>
                        </div>

                        <div class="12u$ 12u$(medium)"><hr></div>

                    <?php } ?>

                    <div class="3u 12u$(medium)"></div>
                    <div class="3u 12u$(medium)"><a href="/review/pending" class="button cancel_button">Cancel</a></div>
                    <div class="3u 12u$(medium)"><input type="submit" value="Submit" class="fit special" /></div>
                    <div class="3u$ 12u$(medium)"></div>

                </div>
            </form>
            <?php include_once TEMPLATE_PATH. "inc/jquery_validator.php"; ?>
        <?php } ?>
    </div>
</section>


