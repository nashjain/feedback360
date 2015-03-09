<?php

class Team
{
    const MANAGER = 'manager';
    const MEMBER = 'member';
    const STAKEHOLDER = 'stakeholder';
    const ROLE = 'role';
    const ORG_ID = 'org_id';
    const TEAM = 'team_id';

    public static function all_roles()
    {
        return [self::MEMBER, self::MANAGER, self::STAKEHOLDER];
    }

    public static function add_only_if_new($team_id, $team_name)
    {
        DB::insertIgnore('team', ['id'=>$team_id, 'name'=>$team_name]);
    }

    public static function members_of($org_id, $team_id)
    {
        $all_members = DB::query("select org_structure.username, user.name as member_name, org_structure.role, user.active from org_structure INNER JOIN user on user.`key`=org_structure.username where org_structure.org_id=%s and org_structure.team_id=%s ORDER BY org_structure.role", $org_id, $team_id);
        $team_name = DB::queryFirstField("select team.name from team where id=%s", $team_id);
        return ['org_id'=>$org_id, 'team_id'=>$team_id, 'team_name'=>$team_name, 'team_members'=>$all_members];
    }

    public static function delete($org_id, $team_id)
    {
        if(empty($team_id) or preg_match('/[^a-z\-0-9]/i', $team_id))
            return ['status'=>'error', 'msg'=>"Cannot delete an invalid team."];

        return self::delete_entity("team", $org_id, $team_id);
    }

    public static function delete_member($username, $team_id, $org_id)
    {
        DB::startTransaction();
        try {
            DB::delete('reviews', "survey_id in (select id from survey where org_id=%s and team_id=%s) and (reviews.reviewee=%s or reviews.reviewer=%s) and status='pending'", $org_id, $team_id, $username, $username);
            DB::delete('org_structure', "org_id=%s and team_id=%s and username=%s", $org_id, $team_id, $username);
        } catch (MeekroDBException $e) {
            DB::rollback();
            return ['status'=>'error', 'msg'=>"Could not delete the user. Error: ".$e->getMessage()];
        }
        DB::commit();
        return ['status'=>'success', 'msg'=>"Successfully removed the user from the team."];
    }

    public static function current_role_of($username, $team_id, $org_id)
    {
        return DB::queryFirstField("select role from org_structure where org_id=%s and team_id=%s and username=%s", $org_id, $team_id, $username);
    }

    public static function update_role($form, $username, $team_id, $org_id)
    {
        $role = strip_tags($form['role']);
        $current_role = strip_tags($form['current_role']);
        if($current_role!=$role)
            DB::update('org_structure', ['role'=>$role], "org_id=%s and team_id=%s and username=%s", $org_id, $team_id, $username);
    }

    public static function add_members($form, $team_id, $org_id)
    {
        $owner_details = [Session::email()=>Session::name()];
        $team_members = Util::tokenize_email_ids($form['team_members'], $owner_details);
        if (empty($team_members)) return "Team Members cannot be empty!";
        $stakeholders = Util::tokenize_email_ids($form['stakeholders'], $owner_details);
        DB::startTransaction();
        try {
            self::save_org_structure($team_members, $stakeholders, $team_id, $org_id);
        } catch (MeekroDBException $e) {
            DB::rollback();
            return "Could not save the details. Please try again. Error: ".$e->getMessage();
        }
        DB::commit();
        return 'success';
    }

    public static function save_org_structure($team_members, $stakeholders, $team_id, $org_id, $manager = '')
    {
        $user_ids = User::create_only_if_new($team_members);
        $stakeholder_ids = User::create_only_if_new($stakeholders);

        $org_struct = [];

        if (!empty($manager))
            $org_struct[] = self::org_struct($org_id, $team_id, $manager, self::MANAGER);

        foreach ($user_ids as $user_id) {
            $org_struct[] = self::org_struct($org_id, $team_id, $user_id, self::MEMBER);
        }

        foreach ($stakeholder_ids as $user_id) {
            $org_struct[] = self::org_struct($org_id, $team_id, $user_id, self::STAKEHOLDER);
        }
        if(!empty($org_struct))
            DB::insert('org_structure', $org_struct);
    }

    private static function org_struct($org_id, $team_id, $user_id, $role)
    {
        return ['org_id' => $org_id, 'team_id' => $team_id, 'username' => $user_id, 'role' => $role];
    }

    static function delete_entity($entity, $org_id, $team_id='')
    {
        $team_clause = '';
        if (!empty($team_id))
            $team_clause = " and team_id='$team_id'";
        DB::startTransaction();
        try {
            $survey_ids = DB::queryFirstColumn("select id from survey where org_id=%s $team_clause", $org_id);
            if(!empty($survey_ids)) {
                DB::delete('survey_competencies', "survey_id in %li", $survey_ids);
                DB::delete('feedback', "review_id in (select id from reviews where survey_id in %li)", $survey_ids);
                DB::delete('reviews', "survey_id in %li", $survey_ids);
                DB::delete('survey', "id in %li", $survey_ids);
            }
            DB::delete('org_structure', "org_id=%s $team_clause", $org_id);
            $teams_left = DB::queryFirstColumn('SELECT DISTINCT(team_id) from org_structure WHERE team_id=%s', $team_id);
            if(empty($teams_left))
                DB::delete('team', "id=%s", $team_id);
            if($entity=='org')
                DB::delete('org', "id=%s", $org_id);
        } catch (MeekroDBException $e) {
            DB::rollback();
            return ['status' => 'error', 'msg' => "Could not delete the " . $entity . ". Error: " . $e->getTraceAsString()];
        }
        DB::commit();
        return ['status' => 'success', 'msg' => "Successfully removed the " . $entity . "."];
    }

    public static function orgs_and_teams_managed_by_me()
    {
        $orgs = DB::query("select org_id, team_id, team.name from org_structure INNER JOIN team on team.id=team_id where org_structure.role=%s and username=%s", self::MANAGER, Session::username());
        return Util::group_to_associative_map($orgs, 'org_id', 'team_id', 'name');
    }

    public static function not_a_manager()
    {
        return DB::queryFirstField("select count(*) as records from org_structure where org_structure.role=%s and username=%s", self::MANAGER, Session::username())==0;
    }

    public static function does_not_belong_to_any_org()
    {
        return DB::queryFirstField("select count(*) as records from org_structure where username=%s", Session::username())==0;
    }

    public static function all_members_from($org_id, $team_id)
    {
        return self::fetch_user_matching($org_id, $team_id, self::all_roles());
    }

    public static function team_members_from($org_id, $team_id)
    {
        return self::fetch_user_matching($org_id, $team_id, [self::MANAGER, self::MEMBER]);
    }

    private static function fetch_user_matching($org_id, $team_id, $roles)
    {
        $all = DB::query("select user.key, user.name from org_structure INNER JOIN user on user.key=org_structure.username where org_id=%s and team_id=%s and org_structure.role in %ls", $org_id, $team_id, $roles);
        return Util::convert_to_associative_map($all, 'key', 'name');
    }
}
