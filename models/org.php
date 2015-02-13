<?php

include_once MODELS_DIR . 'util.php';
include_once MODELS_DIR . 'team.php';
include_once MODELS_DIR . 'user.php';

class Org
{
    public static function fetch_all()
    {
        return DB::queryFirstColumn("SELECT `id` FROM team");
    }

    public static function create($form)
    {
        $required_fields = ['name' => 'Org Name', 'team_name' => 'Team Name', 'team_members' => 'Team Members'];
        $errors = Util::validate_form_contains_required_fields($form, $required_fields);

        if (!empty($errors)) return $errors;

        $name = $form['name'];
        $org_id = Util::add_hyphens($name);

        $org_owner = DB::queryFirstField("select owner from org where org.id=%s LIMIT 1", $org_id);
        if (!empty($org_owner))
            return "Org Name: $name already exists! It was created by <a href='/user/$org_owner'>$org_owner</a>";

        $owner = Session::get_user_property('username');
        $owner_email = Session::get_user_property('email');

        $team_members = Util::tokenize_email_ids($form['team_members'], [$owner_email=>$owner]);

        if (empty($team_members)) return "Team Members cannot be empty!";

        $team_name = $form['team_name'];
        $team_id = Util::add_hyphens($team_name);

        Team::add_only_if_new($team_id, $team_name);

        DB::insert('org', ['id'=>$org_id, 'name'=>$name, 'teams'=>$team_id, 'owner'=> $owner]);

        $user_ids = User::create_only_if_new($team_members);

        $org_struct = [['org_id'=>$org_id, 'team_id'=>$team_id, 'role'=>Session::MANAGER, 'username'=>$owner]];

        foreach($user_ids as $user_id) {
            $org_struct[] = ['org_id'=>$org_id, 'team_id'=>$team_id, 'role'=>Session::MEMBER, 'username'=>$user_id];
        }

        DB::insert('org_structure', $org_struct);

        Session::add_user_as_manager_for([Session::ORG_ID=>$org_id, Session::ROLE=>Session::MANAGER, Session::TEAM=>$team_id]);

        return 'success';
    }
}
