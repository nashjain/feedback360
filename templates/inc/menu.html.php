<header id="header">
    <h1 id="logo"><a href="/">Feedback 360&deg;</a></h1>
    <nav id="nav">
        <ul>
            <li>
                <a href="#">Survey</a>
                <ul>
                    <li><a href="/survey/create"><i class="icon fa-plus">&nbsp;</i> Create</a></li>
                    <li><a href="/survey"><i class="icon fa-list-alt">&nbsp;</i> View</a></li>
                </ul>
            </li>
            <?php if(Session::is_inactive()) {?>
                <li><a href="/auth/login" class="button special">Login</a></li>
            <?php } else { ?>
                <li>
                    <a href="#">Reviews</a>
                    <ul>
                        <li><a href="/review/pending"><i class="icon fa-warning">&nbsp;</i> Pending</a></li>
                        <li><a href="/review/received"><i class="icon fa-download">&nbsp;</i> Received</a></li>
                        <li><a href="/review/given"><i class="icon fa-check">&nbsp;</i> Given</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="button special"><?php echo Session::get_user_property('name')?> <i class="icon fa-caret-down">&nbsp;</i></a>
                    <ul>
                        <li><a href="/user/<?php echo Session::get_user_property('username')?>"><i class="icon fa-user">&nbsp;</i> View Profile</a></li>
                        <li><a href="/user/update-profile"><i class="icon fa-edit">&nbsp;</i> Update Profile</a></li>
                        <li><a href='/user/change-password'><i class="icon fa-lock">&nbsp;</i> Change Password</a></li>
                        <li><a href="/auth/logout"><i class="icon fa-power-off">&nbsp;</i> Logout</a></li>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </nav>
</header>