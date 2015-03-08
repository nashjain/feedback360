<?php
$post_url = "/survey/".$data['survey_id']."/edit-reviewers";
$action = 'Update';
$current_assignment = $data['current_assignment'];
include_once TEMPLATE_PATH . "survey/inc/manage_reviewers.html.php";


