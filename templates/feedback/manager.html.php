<section class="wrapper style special fade">
    <div class="container">
        <h2>Feedback</h2>
        <?php if (empty($data)) { ?>
            <h4>Looks like the review is not yet completed.</h4>
        <?php
        } else { ?>
            <div class="table-wrapper">
                <table class="alt">
                    <thead>
                        <tr>
                            <th>Competency</th>
                            <th>Rating</th>
                            <th>Strengthen Confidence</th>
                            <th>Improve Effectiveness</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $feedback) { ?>
                            <tr>
                                <td><?php echo $feedback['name'] ?></td>
                                <td><?php echo $feedback['rating'] ?></td>
                                <td><?php echo $feedback['good'] ?></td>
                                <td><?php echo $feedback['bad'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
</section>


