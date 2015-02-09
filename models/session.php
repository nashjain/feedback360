<?php

class Session
{
    const ALERT = 'alert';
    const BLANK = '';

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

    public static function is_inactive()
    {
        //logic to check if session is valid
        return false;
    }
}