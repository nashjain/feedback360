<section class="wrapper style special fade">
    <div class="container">
        <h2>Feedback Received</h2>
        <?php if(empty($data)) {?>
            <h4>Sorry! Currently, you have not received any reviews.</h4>
        <?php } else { ?>
            <div class="table-wrapper">
                <table class="alt">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Org</th>
                            <th>Team</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data as $survey) { ?>
                            <tr>
                                <td><a href="/feedback/survey/<?php echo $survey['id'] ?>"><?php echo $survey['name'] ?></a></td>
                                <td><?php echo $survey['org_name'] ?></td>
                                <td><?php echo $survey['team_name'] ?></td>
                                <td><?php echo date( 'jS F Y', strtotime($survey['created'])) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
</section>


