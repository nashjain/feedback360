<?php

class Session
{
    const KEY = 'user_details';
    const ALERT = 'alert';
    const BLANK = '';
    const ORG_DETAILS = 'org_details';

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
        self::_set(self::ALERT, $alert_msg);
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
        $conference = self::org($org_id);
        return !empty($conference);
    }

    public static function is_admin()
    {
        return self::belongs_to('_*all_*');
    }

    public static function is_manager($org)
    {
        return self::role($org)=='manager';
    }

    public static function is_member($org)
    {
        return self::role($org)=='member';
    }

    public static function org_details()
    {
        return self::get_user_property(self::ORG_DETAILS);
    }

    public static function add_user_as_manager_for($details) {
        $org_details = self::get_user_property(self::ORG_DETAILS, []);
        $org_details[$details['org_id']] = $details;
        self::set_user_property(self::ORG_DETAILS, $org_details);
    }

    private static function role($org_id)
    {
        $org = self::org($org_id);
        if(self::is_present_in('role', $org)) return $org['role'];
        return self::BLANK;
    }
}