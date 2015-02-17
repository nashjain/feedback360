<section class="wrapper style special fade">
    <div class="container">
        <h2><?php echo $data['team_name']?> Members</h2>
        <div class="table-wrapper">
            <table class="alt">
                <thead>
                    <tr>
                        <th>Team Member</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['team_members'] as $team_member) { ?>
                        <tr>
                            <td><a href="/user/<?php echo $team_member['username'] ?>" target="_blank"><?php echo $team_member['member_name'] ?></a></td>
                            <td><?php echo ucwords($team_member['role']) ?></td>
                            <?php if(Session::MANAGER ==$team_member['role'] and $team_member['username']==Session::get_user_property('username')) echo "<td></td>"; else {?>
                                <td>
                                    <a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>/member/<?php echo $team_member['username'] ?>/delete?name=<?php echo urlencode($team_member['member_name'])?>"><i class="icon fa-trash">&nbsp;</i></a>&nbsp;&nbsp;
                                    <a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>/member/<?php echo $team_member['username'] ?>/update?name=<?php echo urlencode($team_member['member_name'])?>"><i class="icon fa-edit">&nbsp;</i></a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <div>
            <div class="button-row">
                <a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>/delete?name=<?php echo urlencode($data['team_name'])?>" class="button icon fa-trash">Delete This Team</a>
                <a href="/org/<?php echo $data['org_id'] ?>/team/<?php echo $data['team_id'] ?>/member/add?name=<?php echo urlencode($data['team_name'])?>" class="button special icon fa-plus-circle">Add New Members</a>
            </div>
            <div class="clear-row"></div>
        </div>
    </div>
</section>