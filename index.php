<?php
	require __DIR__.'/vendor/autoload.php';

	use phpish\app;
	use phpish\mysql;
	use phpish\template;

    require __DIR__.'/path.config.php';
	require __DIR__.'/conf/'.app\ENV.'.conf.php';

    include_once __DIR__ . "/models/session.php";

    function set_flash_msg($type, $msg) {
        Session::set_alert(array('msg'=>$msg, 'type'=>$type));
    }

    function display_flash_msg() {
        $alert_msg = Session::get_alert();
        if(!empty($alert_msg)) {
            $msg_block = "<div class='alert alert-" . $alert_msg['type'] . "'>
            <div class='close' data-dismiss='alert'>&times;</div>
            <span>" . $alert_msg['msg'] . "</span>
        </div>";
            Session::remove_alert();
            return $msg_block;
        }
        return "";
    }


    app\any('.*', function($req) {
        session_start();
//        mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
        return app\next($req);
    });

    //drop the slash from the end of the URL
    app\get('{path:.*}/$', function($req) {
        $url = $req['matches']['path'];
        if(empty($url))
            return app\next($req);
        return app\response_301($url);
    });

    app\path_macro(['/', '/hello', '/hello/.*'], function() {
        require CONTROLLER_DIR . 'app_request_handler.php';
    });

    app\path_macro(['/user/.*'], function() {
        require CONTROLLER_DIR . 'user_request_handler.php';
    });

?>