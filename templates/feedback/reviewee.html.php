<section class="wrapper style special fade">
    <div class="container">
        <h2><?php echo $data['title'] ?></h2>
        <?php $all_reviews = $data['reviews'];
        if (empty($all_reviews)) { ?>
            <h4>Looks like you've not received any feedback so far. Check <a href="/review/received">your received feedback list</a>.</h4>
        <?php
        } else {
                echo '<div id="graph" style="margin-bottom: 2em"></div>';
            $self_rating = [];
            $avg_rating = [];
            foreach ($all_reviews as $competency_name => $reviewer_feedback) {
                $self = intval($reviewer_feedback['self']);
                $avg = $reviewer_feedback['avg'];
                $manager_view = $reviewer_feedback['manager_view'];
                $self_rating[] = $self;
                $avg_rating[] = $avg;
                ?>
                <h3><?php echo $competency_name . ": (Self: " . $self . " Group Avg: " . $avg .")" ?></h3>
                <div class="table-wrapper">
                    <table class="alt">
                        <thead>
                            <tr>
                                <?php if($manager_view) echo "<th>Reviewer</th>"; ?>
                                <th>Rating</th>
                                <th>Strengthen Confidence</th>
                                <th>Improve Effectiveness</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviewer_feedback['feedback'] as $feedback) { ?>
                                <tr>
                                    <?php if($manager_view) echo "<td>".$feedback['reviewer']."</td>"; ?>
                                    <td><?php echo $feedback['rating'] ?></td>
                                    <td><?php echo $feedback['good'] ?></td>
                                    <td><?php echo $feedback['bad'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<?php
$categories = array_keys($all_reviews);
include TEMPLATE_PATH ."inc/highcharts.php";
?>
