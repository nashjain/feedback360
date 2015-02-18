<?php

class Session
{
    const KEY = 'user_details';
    const ALERT = 'alert';
    const BLANK = '';

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

    public static function add_user_details($details)
    {
        self::_set(self::KEY, $details);
    }

    public static function set_alert($alert_msg)
    {
        $alert = self::get_alert();
        if (empty($alert)) $alert = [];
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

    public static function username()
    {
        return self::get_user_property('username');
    }

    public static function email()
    {
        return self::get_user_property('email');
    }

    public static function name()
    {
        return self::get_user_property('name');
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
}