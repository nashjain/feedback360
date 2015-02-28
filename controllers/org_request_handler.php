<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'org.php';
include_once MODELS_DIR . 'team.php';
include_once MODELS_DIR . 'user.php';

app\any("/org[/.*]", function ($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/auth/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/org", function ($req) {
    $data = Org::fetch_orgs_owned_by_me();
    if(empty($data)){
        $data = Org::fetch_orgs_and_teams_to_which_i_belong();
        if(empty($data)) {
            set_flash_msg('error', "You don't seem to belong to any organisation. Please create one.");
            return app\response_302("/org/create");
        }
        return template\compose("org/read_only_list.html", compact('data'), "layout-no-sidebar.html");
    }
    return template\compose("org/list.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/org/create", function ($req) {
    $data = [];
    if (array_key_exists('requested_url', $req['query']))
        $data['requested_url'] = $req['query']['requested_url'];
    return template\compose("org/create.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/org/create", function ($req) {
    $response = Org::create($req['form']);
    if ('success'!= $response)
        set_flash_msg('error', $response);
    else
        set_flash_msg('success', 'Successfully created your organisation');
    return app\response_302($req['form']['requested_url']);
});

app\any("/org/{org_id}/[delete[/yes]|team[/.*]]", function ($req) {
    $org_id = $req['matches']['org_id'];
    if(!Org::is_owner_of($org_id)){
        set_flash_msg('error', 'You are not authorised to perform this operation on this org.');
        return app\response_302('/org');
    }
    return app\next($req);
});

app\get("/org/{org_id}/team", function ($req) {
    $org_id = $req['matches']['org_id'];
    $data = Org::teams_belonging_to($org_id);
    return template\compose("org/teams.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/org/{org_id}/team/add", function ($req) {
    $org_id = $req['matches']['org_id'];
    $org_name = query_param($req, 'name');
    $data = ['org_id'=>$org_id, 'org_name'=>$org_name];
    return template\compose("org/add_team.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/org/{org_id}/team/add", function ($req) {
    $org_id = $req['matches']['org_id'];
    $response = Org::add_team($req['form'], $org_id);
    if ('success'!= $response)
        set_flash_msg('error', $response);
    return app\response_302("/org/$org_id/team");
});

app\get("/org/{org_id}/team/{team_id}", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $data = Team::members_of($org_id, $team_id);
    return template\compose("org/team_members.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/org/{org_id}/delete", function ($req) {
    $org_id = $req['matches']['org_id'];
    $org_name = query_param($req, 'name');
    $data = ['title'=>"Deleting your Organisation: $org_name...", 'ok_url'=>"/org/$org_id/delete/yes", 'cancel_url'=>"/org/$org_id/team", 'msg'=>'Are you sure you want to delete your entire org? This operation cannot be undone. You will loose all your reviews and team information!'];
    return template\compose("org/confirmation.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/org/{org_id}/delete/yes", function ($req) {
    $org_id = $req['matches']['org_id'];
    $response = Org::delete($org_id);
    set_flash_msg($response['status'], $response['msg']);
    return app\response_302('/org');
});

app\get("/org/{org_id}/team/{team_id}/delete", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $team_name = query_param($req, 'name');
    $data = ['title'=>"Deleting your Team: $team_name...", 'ok_url'=>"/org/$org_id/team/$team_id/delete/yes", 'cancel_url'=>"/org/$org_id/team", 'msg'=>'Are you sure you want to delete your entire team? This operation cannot be undone. You will loose all your reviews and team information!'];
    return template\compose("org/confirmation.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/org/{org_id}/team/{team_id}/delete/yes", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $response = Team::delete($org_id, $team_id);
    set_flash_msg($response['status'], $response['msg']);
    return app\response_302("/org/$org_id/team");
});

app\get("/org/{org_id}/team/{team_id}/resend-activation-email", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $team_name = query_param($req, 'name');
    $num_email = User::resend_activation_email($org_id, $team_id);
    set_flash_msg('success', "Successfully resent activation email to $num_email inactive members of $team_name");
    return app\response_302("/org/$org_id/team/$team_id");
});

app\get("/org/{org_id}/team/{team_id}/member/{username}/delete", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $username = $req['matches']['username'];
    $member_name = query_param($req, 'name');
    $data = ['title'=>"Deleting your member: $member_name...", 'ok_url'=>"/org/$org_id/team/$team_id/member/$username/delete/yes", 'cancel_url'=>"/org/$org_id/team/$team_id", 'msg'=>'Are you sure you want to remove the user from your team? This operation cannot be undone. <br/>All pending reviews will be deleted. Existing reviews will remain as is.'];
    return template\compose("org/confirmation.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/org/{org_id}/team/{team_id}/member/{username}/delete/yes", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $username = $req['matches']['username'];
    $response = Team::delete_member($username, $team_id, $org_id);
    set_flash_msg($response['status'], $response['msg']);
    return app\response_302("/org/$org_id/team/$team_id");
});

app\get("/org/{org_id}/team/{team_id}/member/{username}/update", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $username = $req['matches']['username'];
    $member_name = query_param($req, 'name');
    $current_role = Team::current_role_of($username, $team_id, $org_id);
    $data = ['org_id'=>$org_id, 'team_id'=>$team_id, 'username'=>$username, 'member_name'=>$member_name, 'current_role'=>$current_role, 'roles'=>Team::all_roles()];
    return template\compose("org/change_role.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/org/{org_id}/team/{team_id}/member/{username}/update", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $username = $req['matches']['username'];
    Team::update_role($req['form'], $username, $team_id, $org_id);
    return app\response_302("/org/$org_id/team/$team_id");
});

app\get("/org/{org_id}/team/{team_id}/member/add", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $team_name = query_param($req, 'name');
    $data = ['org_id'=>$org_id, 'team_id'=>$team_id, 'team_name'=>$team_name];
    return template\compose("org/add_team_members.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/org/{org_id}/team/{team_id}/member/add", function ($req) {
    $org_id = $req['matches']['org_id'];
    $team_id = $req['matches']['team_id'];
    $response = Team::add_members($req['form'], $team_id, $org_id);
    if ('success'!= $response)
        set_flash_msg('error', $response);
    return app\response_302("/org/$org_id/team/$team_id");
});