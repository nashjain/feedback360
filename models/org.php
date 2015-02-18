<?php

include_once MODELS_DIR . 'util.php';
include_once MODELS_DIR . 'team.php';
include_once MODELS_DIR . 'user.php';

class Org
{
    public static function fetch_orgs_owned_by_me()
    {
        return DB::query("SELECT `id`, name, time from org where owner=%s", Session::username());
    }

    public static function fetch_orgs_and_teams_to_which_i_belong()
    {
        $all_orgs = DB::query("SELECT org.name as org_name, team.name as team_name, team.time as team_creation_time, org_structure.role from org_structure INNER JOIN org on org_id=org.id INNER JOIN team on team_id=team.id where username=%s", Session::username());
        return Util::group_to_associative_array($all_orgs, 'org_name');
    }

    public static function is_owner_of($org_id)
    {
        return Session::username()==DB::queryFirstField("select owner from org where `id`=%s", $org_id);
    }

    public static function teams_belonging_to($org_id)
    {
        $org_name = DB::queryFirstField("select org.name from org where id=%s", $org_id);
        $teams = DB::query("select DISTINCT(team_id), team.name as team_name, team.time from org_structure INNER JOIN team on team.id=org_structure.team_id where org_id=%s ORDER BY team_id", $org_id);
        return ['org_id'=>$org_id, 'org_name'=>$org_name, 'teams'=>$teams];
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

        $team_members = $form['team_members'];
        $stakeholders = $form['stakeholders'];
        $team_name = $form['team_name'];
        $owner = Session::username();
        $org_details = ['id' => $org_id, 'name' => $name, 'owner' => $owner];
        $owner_email_name = [Session::email()=>Session::name()];
        return self::save_team($org_id, $team_name, $org_details, $team_members, $stakeholders, $owner_email_name, $owner);
    }

    public static function delete($org_id)
    {
        return Team::delete_entity("org", $org_id);
    }

    private static function save_team($org_id, $team_name, $org_details, $input_team_members, $input_stakeholders, $owner_email_name, $owner_username)
    {
        $team_members = Util::tokenize_email_ids($input_team_members, $owner_email_name);
        if (empty($team_members)) return "Team Members cannot be empty!";

        $stakeholders = Util::tokenize_email_ids($input_stakeholders, $owner_email_name);

        $team_id = Util::add_hyphens($team_name);

        DB::startTransaction();
        try {
            Team::add_only_if_new($team_id, $team_name);
            if(!empty($org_details))
                DB::insert('org', $org_details);
            Team::save_org_structure($team_members, $stakeholders, $team_id, $org_id, $owner_username);
        } catch (MeekroDBException $e) {
            DB::rollback();
            return "Could not save the details. Please try again. Error: " . $e->getMessage();
        }
        DB::commit();

        return 'success';
    }

    public static function add_team($form, $org_id)
    {
        $required_fields = ['team_name' => 'Team Name', 'team_owner_name' => "Team Owner's Name", 'team_owner_email' => "Team Owner's Email", 'team_members' => 'Team Members'];
        $errors = Util::validate_form_contains_required_fields($form, $required_fields);
        if (!empty($errors)) return $errors;

        $team_name = $form['team_name'];
        $team_owner_name = $form['team_owner_name'];
        $team_owner_email = $form['team_owner_email'];
        $owner_email_name = [$team_owner_email => $team_owner_name];

        $user_names = User::create_only_if_new($owner_email_name);

        $team_members = $form['team_members'];
        $stakeholders = $form['stakeholders'];

        return self::save_team($org_id, $team_name, [], $team_members, $stakeholders, $owner_email_name, current($user_names));
    }
}
