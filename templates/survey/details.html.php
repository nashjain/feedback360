<section class="wrapper style special fade">
    <div class="container">
        <h2><?php echo $data['survey_name'] ?> Details</h2>
        <div class="table-wrapper">
            <table class="alt">
                <thead>
                <tr>
                    <th>Reviewee</th>
                    <th>Reviewers</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data['grouped_review'] as $reviewee=>$reviews) { $count = 0; ?>
                    <?php foreach($reviews as $review) { ?>
                            <tr>
                                <?php if(++$count==1) { ?>
                                    <td rowspan="<?php echo count($reviews) ?>"><a href="/user/<?php echo $reviewee ?>"><?php echo $reviewee ?></a></td>
                                <?php } ?>
                                <td><a href="/user/<?php echo $review['reviewer'] ?>"><?php echo $review['reviewer'] ?></a></td>
                                <td><?php echo $review['status'] ?></td>
                                <td><?php echo date( 'jS F Y h:i A', strtotime($review['updated'])) ?></td>
                                <?php if($count==1) { ?>
                                    <td rowspan="<?php echo count($reviews) ?>"><a href="/survey/<?php echo $data['survey_id'] .'/reviewee/'.$reviewee ?>">View</a></td>
                                <?php } ?>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>