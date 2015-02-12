<header id="header">
    <h1 id="logo"><a href="/">Feedback 360&deg;</a></h1>
    <nav id="nav">
        <ul>
            <li><a href="/survey/create">Create</a></li>
            <?php if(Session::is_inactive()) {?>
                <li><a href="/auth/login" class="button special">Login</a></li>
            <?php } else { ?>
                <li>
                    <a href="#">Reviews</a>
                    <ul>
                        <li><a href="/review/pending"><i class="icon fa-edit"></i> Pending</a></li>
                        <li><a href="/review/received"><i class="icon fa-edit"></i> Received</a></li>
                        <li><a href="/review/given"><i class="icon fa-edit"></i> Given</a></li>
                    </ul>
                </li>
                <li>
                    <a href="/user/<?php echo Session::get_user_property('username')?>" class="button special"><?php echo Session::get_user_property('name')?> <i class="icon fa-caret-down"></i></a>
                    <ul>
                        <li><a href="/user/update-profile"><i class="icon fa-edit"></i> Update Profile</a></li>
                        <li><a href='/user/change-password'><i class="icon fa-lock"></i> Change Password</a></li>
                        <li><a href="/auth/logout"><i class="icon fa-power-off"></i> Logout</a></li>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </nav>
</header>