<section class="wrapper style special fade">
    <div class="container">
        <h2>My Surveys</h2>
        <?php if(empty($data)) {?>
            <h4>Looks like you've not created any surveys so far. <a href="/survey/create">Get started...</a></h4>
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
                                <td><a href="/survey/<?php echo $survey['id'] ?>/overview"><?php echo $survey['name'] ?></a></td>
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


