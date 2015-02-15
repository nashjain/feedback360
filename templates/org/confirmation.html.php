<section class="wrapper style special fade">
    <div class="container">
        <h2><?php echo $data['title'] ?></h2>

        <h4><?php echo $data['msg'] ?></h4>
        <div>
            <a href="<?php echo $data['ok_url'] ?>" class="button icon fa-trash">Delete</a>
            <a href="<?php echo $data['cancel_url'] ?>" class="button special icon fa-close">Cancel</a>&nbsp;
        </div>
    </div>
</section>