<?php

use phpish\app;
use phpish\template;

include_once MODELS_DIR . 'competencies.php';
include_once MODELS_DIR . 'survey.php';
include_once MODELS_DIR . 'review.php';
include_once MODELS_DIR . 'feedback.php';
include_once MODELS_DIR . 'user.php';

app\get("/survey", function ($req) {
    $data = Survey::fetch_my_surveys();
    return template\compose("survey/dashboard.html", compact('data'), "layout-no-sidebar.html");
});

app\get("/survey/{id}/feedback", function ($req) {
    $id = $req['matches']['id'];
    $data = Feedback::fetch_consolidated_reviewee_feedback_for($id);
    return template\compose("feedback/reviewee.html", compact('data'), "layout-no-sidebar.html");
});

app\any("/survey/[create|assign-reviewer]", function ($req) {
    if(Session::is_inactive()) {
        set_flash_msg('error', 'You need to login to perform this action.');
        return app\response_302('/auth/login?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    if(Session::does_not_belong_to_any_org()) {
        set_flash_msg('error', 'You need to be part of at least one organisation to perform this action.');
        return app\response_302('/org/create?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    if(Session::not_a_manager()) {
        set_flash_msg('error', 'You need to be a manager of at least one team to perform this action.');
        return app\response_302('/team/create?requested_url='.rawurlencode($_SERVER["REQUEST_URI"]));
    }
    return app\next($req);
});

app\get("/survey/create", function ($req) {
    $data = Session::orgs_and_teams_owned_by_me();
    $data['competencies'] = Competencies::fetch_all();
    return template\compose("survey/create.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/survey/create", function ($req) {
    $response = Survey::create($req['form']);
    if($response['status']!='success') {
        set_flash_msg($response['status'], $response['value']);
        return app\response_302('/survey/create');
    }
    $survey_id = $response['value'];
    $survey_name = $req['form']['name'];
    $org_id = $req['form']['org_id'];
    $team_id = $req['form']['team_id'];
    $employees = User::all_employees_from($org_id);
    $team_members = User::all_employees_from($org_id, $team_id);
    $data = ['survey_id'=>$survey_id, 'survey_name'=>$survey_name, 'org_id'=>$org_id, 'team_id'=>$team_id, 'employees'=>$employees, 'team_members'=>$team_members];
    return template\compose("survey/assign_reviewers.html", compact('data'), "layout-no-sidebar.html");
});

app\post("/survey/assign-reviewer", function ($req) {
    $survey_id = $req['form']['survey_id'];
    if(!Survey::is_owned_by($survey_id)){
        set_flash_msg('error', 'You are not authorised to assign reviewers to this survey');
        return app\response_302('/survey/create');
    }
    $response = Review::assign_reviewers($req['form']);
    set_flash_msg($response['status'], $response['value']);
    if($response['status']!='success') {
        $survey_name = $req['form']['survey_name'];
        $org_id = $req['form']['org_id'];
        $team_id = $req['form']['team_id'];
        $employees = User::all_employees_from($org_id);
        $team_members = User::all_employees_from($org_id, $team_id);
        $data = ['survey_id'=>$survey_id, 'survey_name'=>$survey_name, 'org_id'=>$org_id, 'team_id'=>$team_id, 'employees'=>$employees, 'team_members'=>$team_members];
        return template\compose("survey/assign_reviewers.html", compact('data'), "layout-no-sidebar.html");
    }
    return app\response_302('/survey/'.$survey_id);
});

app\get("/survey/{id}", function ($req) {
    $id = $req['matches']['id'];
    $data = Survey::details($id);
    return template\compose("survey/details.html", compact('data'), "layout-no-sidebar.html");
});