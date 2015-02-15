<section class="wrapper style special fade">
    <div class="container">
        <h2><?php echo $data['org_name']?> Details</h2>
        <div class="table-wrapper">
            <table class="alt">
                <thead>
                    <tr>
                        <th>Team Name</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['teams'] as $team) { ?>
                        <tr>
                            <td><?php echo $team['team_name'] ?></td>
                            <td><?php echo date( 'jS F Y', strtotime($team['time'])) ?></td>
                            <td>
                                <a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $team['team_id'] ?>/delete?name=<?php echo urlencode($team['team_name'])?>"><i class="icon fa-trash">&nbsp;</i></a>&nbsp;&nbsp;
                                <a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $team['team_id'] ?>"><i class="icon fa-edit">&nbsp;</i></a>
                            </td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <div>
            <div class="button-row">
                <a href="/org/<?php echo $data['org_id'] ?>/delete?name=<?php echo urlencode($data['org_name'])?>" class="button icon fa-trash">Delete This Org</a>
                <a href="/org/<?php echo $data['org_id'] ?>/team/add?name=<?php echo urlencode($data['org_name'])?>" class="button special icon fa-plus-circle">Add New Team</a>
            </div>
            <div class="clear-row"></div>
        </div>
    </div>
</section>