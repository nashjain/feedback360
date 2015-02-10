<?php

include_once MODELS_DIR . 'session.php';
include_once MODELS_DIR . 'email.php';
include_once MODELS_DIR . 'mailer.php';

class User
{
    static function register($form)
    {
        $form_errors = self::validate_details($form);
        if (empty($form_errors)) {
            $user_details = self::create_user($form);
            send_mail($user_details, 'registration');
        }
        return $form_errors;
    }

    private static function create_user($user_details)
    {
        $sign_up_date = date('Y-m-d H:i:s');
        $activation_token = md5((string)$sign_up_date);
        $password_hash = self::create_hash($user_details['password'], (string)$sign_up_date);
        $user_key = self::get_user_key($user_details['name']);
        $user_record = array(
            'name' => $user_details['name'],
            'key' => $user_key,
            'password' => $password_hash,
            'email' => $user_details['email'],
            'activation_token' => $activation_token,
            'last_activation_request' => strtotime("now"),
            'lost_password_request' => 0,
            'active' => 0,
            'sign_up_date' => $sign_up_date,
        );
        DB::insert('user', $user_record);
        return $user_record;
    }

    private static function get_user_key($name)
    {
        $name_key = self::add_hyphens($name);
        $results = DB::queryFirstColumn("SELECT user.key FROM user WHERE `key` LIKE %ss", $name_key);
        if (empty($results)) return $name_key;
        sort($results);
        $last_integer = (integer)str_replace($name_key . '-', '', end($results)) + 1;
        return $name_key . "-" . $last_integer;
    }

    private static function create_hash($password, $salt) {
        $string = $password . $salt;
        return md5($string);
    }

    private static function add_hyphens($word) {
        $word = preg_replace('/[^a-zA-Z0-9\s_&\\-]+/', '', $word);
        return strtolower(preg_replace('/[\s\W]+/','-',$word));
    }

    static function verify_email_address($info)
    {
        $user_details = self::fetch_user_details('email', $info['email'], "activation_token,active,email,name");
        if(!empty($user_details)){
            Email::subscribe($user_details);
            if ($user_details['active'] == 1) {
                return array('state' => 'info', 'text' => 'Account already active. Please login using your credentials.');
            } else if ($user_details['activation_token'] == $info['activation_token']) {
                self::update_user_details($info['email'], array('active' => 1));
                return array('state' => 'success', 'text' => 'Account has been activated. Please login using your credentials');
            }
        }
        return array('state' => 'error', 'text' => 'Wrong link used for Account Activation. Please contact support for help.');
    }

    static function authenticate_user($credentials)
    {
        $email = $credentials['email'];
        $user_details = self::fetch_user_details('email', $email, "name,email,`key`,password,sign_up_date,active");
        if (empty($user_details) || $user_details['password'] != self::create_hash($credentials['password'], $user_details['sign_up_date']))
            return 'Email address and Password combination does not match.';
        if ($user_details['active'] == 0)
            return 'Your credentials are correct. However, looks like you have not verified your email address.
            <br>Please check your inbox for the verification email, which was sent at the time of registration.
            <br>OR
            <br>Request for a <a href="/auth/resend-verification-email?email=' . $email . '">new verification email</a>.';
        return 'Success';
    }

    public static function resend_verification_email($req_param)
    {
        if (!array_key_exists('email', $req_param) || empty($req_param['email']))
            return array('error', 'Email address missing from the request.');
        $email = $req_param['email'];
        $user_details = self::fetch_user_details('email', $email, "name,email,activation_token");
        if (empty($user_details))
            return array('error', 'Email address "' . $email . '" does not belong to any registered user.');
        send_mail($user_details, 'email_verification');
        return array('success', 'New verification email sent to ' . $email . '. Please check your inbox.');
    }

    private static function set_user_data_session($user_details)
    {
        $username = $user_details['key'];
        $limited_user_details = array(
            'name' => $user_details['name'],
            'email' => $user_details['email'],
            'username' => $username
        );
        $program_team_details = DB::query("SELECT conf_id, theme, role FROM program_team WHERE `username`=%s", $username);
        if (!empty($program_team_details)) {
            $limited_user_details[Session::ORG_DETAILS] = convert_to_associative_array($program_team_details, 'conf_id');
        }
        Session::add_user_details($limited_user_details);
    }

    private static function validate_details($user)
    {
        $errors = array();
        if (empty($user['name'])) {
            $errors['name'] = 'Please enter your name';
        }
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid Email Address';
        }
        if (empty($user['password']) || strlen($user['password']) <= 6) {
            $errors['password'] = 'Please enter a valid password with more than 6 characters';
        }
        if ($user['password'] != $user['confirmation-password']) {
            $errors['confirmation-password'] = 'Confirmation password does not match';
        }
        $records = self::fetch_user_details('email', $user['email']);
        if (!empty($records)) {
            $errors['email'] = 'Please use another Email Address, current email is already used with another account';
        }
        return $errors;
    }

    static function process_forgot_password_request($email)
    {
        $user_details = self::fetch_user_details('email', $email);
        if ($user_details) {
            send_mail($user_details, 'password-recovery');
            self::update_user_details($user_details['email'], array('lost_password_request' => 1));
            return array('success', "<h4>Password Recovery Mail sent to " . $user_details['email'] . "</h4><span>Please check your mail account, and click on the password recovery link to reset your password.</span>");
        }
        return array('error', '<h4>Email Address Not found!</h4><span>Please check the email address entered and try again.</span>');
    }

    static function process_password_reset_request($query_details)
    {
        if (isset($query_details) && array_key_exists('email', $query_details) && array_key_exists('activation_token', $query_details)) {
            $user_details = self::fetch_user_details('email', $query_details['email']);
            if ($user_details['activation_token'] == $query_details['activation_token'])
                return $user_details;
        }
        return array('error', '<h4>Invalid Password Recovery Link</h4><span>The password recovery link is broken. Please try to reset the password again.</span>');
    }

    static function reset_password($form)
    {
        $user_details = self::process_password_reset_request($form);
        if (is_array($user_details) && 'error' == current($user_details)) return $user_details;
        $errors = self::validate_reset_password($form);
        if (!empty($errors)) return array('error', $errors);
        $password_hash = self::create_hash($form['password'], $user_details['sign_up_date']);
        self::update_user_details($form['email'], array('password' => $password_hash, 'lost_password_request' => 0));
        Session::destroy();
        return array('success', 'Password Reset Successful! Please login using your new credentials.');
    }

    private static function fetch_user_details($query_param, $email, $required_params = "email,name,activation_token,sign_up_date")
    {
        return DB::queryFirstRow("SELECT %l FROM user WHERE `" . $query_param . "`=%s LIMIT 1", $required_params, $email);
    }

    static function display_profile($user_key)
    {
        return self::fetch_user_details('key', $user_key, "user_id, name, `key`, email, sign_up_date, bio, title, organization, active, sign_up_date");
    }

    private static function update_user_details($email, $params)
    {
        DB::update('user', $params, "email=%s", $email);
    }

    private static function validate_reset_password($form)
    {
        return self::validations_for($form['password'], array(
            'min_length' => array('length' => 6),
            'compare' => array('value' => $form['confirmation-password'])
        ));
    }

    private static function validations_for($value, $required_validations)
    {
        $errors = "";
        foreach ($required_validations as $validation => $params) {
            switch ($validation) {
                case 'min_length':
                    if (strlen($value) < $params['length']) {
                        $errors .= 'Should be at least ' . $params['length'] . ' characters long.<br>';
                    }
                    break;
                case 'compare':
                    if ($value != $params['value']) {
                        $errors .= 'Password does not match.<br>';
                    }
                    break;
            }
        }
        return $errors;
    }

    public static function isProfileComplete()
    {
        $username = Session::get_user_property('username');
        $bio = DB::queryFirstField("SELECT user.bio FROM user WHERE `key`=%s", $username);
        return !empty($bio) && strlen($bio) > 1;
    }

    public static function create_profile($form)
    {
        $errors = self::validate_form($form);
        if (!empty($errors)) return $errors;
        $updated_email_address = $form['inputEmail'] != Session::get_user_property('email');
        $username = Session::get_user_property('username');
        if(!empty($form['username']) and Session::is_admin()) {
            $username = $form['username'];
            $updated_email_address = false;
        }
        if ($updated_email_address) {
            $user_details = self::fetch_user_details('email', $form['inputEmail'], "active");
            if (!empty($user_details)) {
                return "Email Address already associated with another account.";
            }
        }
        $profile_values = array('title' => $form['inputTitle'],
            'organization' => $form['inputOrganization'],
            'country' => $form['inputCountry'],
            'phone' => $form['inputPhone'],
            'bio' => $form['inputBio'],
            'agile_experience' => $form['inputAgileExperience'],
            'email' => $form['inputEmail'],
            'twitter' => $form['inputTwitter'],
            'website' => $form['inputWebsite'],
            'profile_link' => $form['inputProfileLink']
        );
        if ($updated_email_address) {
            $activation_token = md5($form['inputSignUpDate']);
            $profile_values['activation_token'] = $activation_token;
            $profile_values['active'] = 0;
            $email_values = array(
                'name' => Session::get_user_property('name'),
                'activation_token' => $activation_token,
                'email' => $form['inputEmail']
            );
        }
        DB::update('user', $profile_values, "`key`=%s", $username);
        if ($updated_email_address and !(empty($email_values))) {
            send_mail($email_values, 'email_verification');
            self::logout();
            return 'ResetEmail';
        }
        return 'Success';
    }

    private static function validate_form($form)
    {
        $required_fields = array('inputTitle' => 'Title', 'inputOrganization' => 'Organization', 'inputBio' => 'Bio', 'inputEmail' => 'Email Address');
        return self::validate_form_contains_required_fields($form, $required_fields);
    }

    private static function validate_form_contains_required_fields($form, $required_fields)
    {
        $errors = '';
        foreach ($required_fields as $required_field => $field_name) {
            $actual_value = trim($form[$required_field]);
            if (empty($actual_value)) {
                $errors .= $field_name . " cannot be null.<br>";
            }
        }
        return $errors;
    }

    public static function fetch_profile_data($username)
    {
        $data = array('show_meter' => 'Profile');
        $data['profile'] = self::display_profile($username);
        $data['user_id'] = $data['profile']['user_id'];
        $data['email'] = $data['profile']['email'];
        $data['topic'] = 'Update your Profile';
        return $data;
    }

    public static function fetch_logged_in_users_info()
    {
        return DB::queryFirstRow("SELECT user.user_id, user.email, user.sign_up_date, user.active FROM user WHERE `key`=%s", Session::get_user_property('username'));
    }

    public static function logout()
    {
        Session::destroy();
    }

    private static function can_update($username) {
        return $username == Session::get_user_property('username') || Session::is_admin();
    }

    public static function is_authorized_to_view_dashboard($org_id)
    {
        return Session::is_manager($org_id);
    }

    public static function is_authorized_to_provide_feedback($org_id)
    {
        return Session::is_member($org_id);
    }

    public static function fetch_email_and_activation_token()
    {
        return DB::queryFirstRow("select email, activation_token from user where `key`=%s", Session::get_user_property('username'));
    }

    public static function fetch_all_ids()
    {
        return DB::queryFirstColumn("SELECT `key` FROM user where active=1");
    }
}
