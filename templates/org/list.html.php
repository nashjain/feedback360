<section class="wrapper style special fade">
    <div class="container">
        <h2>My Orgs</h2>

        <div class="table-wrapper">
            <table class="alt">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Created On</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $org) { ?>
                    <tr>
                        <td><?php echo $org['name'] ?></td>
                        <td><?php echo date('jS F Y', strtotime($org['time'])) ?></td>
                        <td>
                            <a href="/org/<?php echo $org['id'] ?>/delete?name=<?php echo urlencode($org['name']) ?>"><i class="icon fa-trash">&nbsp;</i></a>&nbsp;&nbsp;
                            <a href="/org/<?php echo $org['id'] ?>/team"><i class="icon fa-edit">&nbsp;</i></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div>
            <div class="button-row">
                <a href="/org/create" class="button special icon fa-plus-circle">Add New Org</a>
            </div>
            <div class="clear-row"></div>
        </div>
    </div>
</section>