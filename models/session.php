<?php

class Session
{
    const KEY = 'user_details';
    const ALERT = 'alert';
    const BLANK = '';
    const ORG_DETAILS = 'org_details';
    const MANAGER = 'manager';
    const MEMBER = 'member';
    const ROLE = 'role';
    const ORG_ID = 'org_id';
    const TEAM = 'team_id';

    public static function is_inactive()
    {
        return !self::_check(self::KEY);
    }

    public static function destroy()
    {
        foreach ($_SESSION as $key => $value) {
            self::_remove($key);
        }
    }

    public static function get_user_property($key, $default = '')
    {
        $user_value = $default;
        $user_details = self::_get(self::KEY);
        if (!empty($user_details) and self::is_present_in($key, $user_details))
            $user_value = $user_details[$key];
        return $user_value;
    }

    private static function set_user_property($key, $value) {
        $user_details = self::_get(self::KEY);
        $user_details[$key] = $value;
        self::add_user_details($user_details);
    }

    public static function add_user_details($details)
    {
        self::_set(self::KEY, $details);
    }

    public static function set_alert($alert_msg)
    {
        $alert = self::get_alert();
        if(empty($alert)) $alert = [];
        $alert[] = $alert_msg;
        self::_set(self::ALERT, $alert);
    }

    public static function get_alert()
    {
        return self::_get(self::ALERT);
    }

    public static function remove_alert()
    {
        self::_remove(self::ALERT);
    }

    private static function _get($key)
    {
        return self::_check($key) ? $_SESSION[$key] : self::BLANK;
    }

    private static function _set($variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    private static function _remove($key)
    {
        unset($_SESSION[$key]);
    }

    private static function is_present_in($key, $array)
    {
        return !empty($array) and array_key_exists($key, $array) and !empty($array[$key]);
    }

    private static function _check($key)
    {
        return self::is_present_in($key, $_SESSION);
    }

    private static function org($org_id)
    {
        $org_details = self::org_details();
        if(empty($org_details)) return [];
        if(!array_key_exists($org_id, $org_details)) return [];
        return $org_details[$org_id];
    }

    private static function belongs_to($org_id)
    {
        $org = self::org($org_id);
        return !empty($org);
    }

    public static function is_admin()
    {
        return self::belongs_to('_*all_*');
    }

    public static function is_manager($org_id)
    {
        return self::role($org_id)== self::MANAGER;
    }

    public static function is_member($org_id)
    {
        return self::role($org_id)== self::MEMBER;
    }

    public static function org_details()
    {
        return self::get_user_property(self::ORG_DETAILS, []);
    }

    public static function add_user_as_manager_for($details) {
        $org_details = self::org_details();
        $org_details[$details[self::ORG_ID]] = $details;
        self::set_user_property(self::ORG_DETAILS, $org_details);
    }

    private static function role($org_id)
    {
        $org = self::org($org_id);
        if(self::is_present_in(self::ROLE, $org)) return $org[self::ROLE];
        return self::BLANK;
    }

    public static function does_not_belong_to_any_org() {
        $org_details = self::org_details();
        return empty($org_details);
    }

    public static function not_a_manager()
    {
        foreach(self::org_details() as $org_id=>$org) {
            if(self::is_manager($org_id)) return false;
        }
        return true;
    }

    public static function orgs_and_teams_owned_by_me()
    {
        $orgs = [];
        $teams = [];
        foreach(self::org_details() as $org_id=>$org) {
            if(self::is_manager($org_id)) {
                $orgs[] = $org_id;
                $teams[] = $org[self::TEAM];
            }
        }
        return ['org_ids'=>$orgs, 'teams'=>$teams];
    }
}