<section class="wrapper style special fade">
    <div class="container">
        <h2>My Orgs</h2>

        <?php foreach ($data as $org_name=>$teams) { ?>
            <h3><?php echo $org_name ?></h3>
            <div class="table-wrapper">
                <table class="alt">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Created On</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($teams as $team) { ?>
                        <tr>
                            <td><?php echo $team['team_name'] ?></td>
                            <td><?php echo ucwords($team['role']) ?></td>
                            <td><?php echo date('jS F Y', strtotime($team['team_creation_time'])) ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
        <div>
            <div class="button-row">
                <a href="/org/create" class="button special icon fa-plus-circle">Add New Org</a>
            </div>
            <div class="clear-row"></div>
        </div>
    </div>
</section>