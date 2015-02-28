<?php

include_once MODELS_DIR . 'util.php';
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

    private static function create_user($user_details, $lost_password_request=0)
    {
        $sign_up_date = date('Y-m-d H:i:s');
        $activation_token = md5((string)$sign_up_date);
        $password_hash = self::create_hash($user_details['password'], (string)$sign_up_date);
        $user_key = self::get_user_key($user_details['name']);
        $user_record = [
            'name' => $user_details['name'],
            'key' => $user_key,
            'password' => $password_hash,
            'email' => $user_details['email'],
            'activation_token' => $activation_token,
            'last_activation_request' => strtotime("now"),
            'lost_password_request' => $lost_password_request,
            'active' => 0,
            'sign_up_date' => $sign_up_date,
        ];
        DB::insert('user', $user_record);
        return $user_record;
    }

    public static function create_only_if_new($team_members){
        if(empty($team_members)) return [];
        $exiting_user = DB::query("select user.key, email from user where email in %ls", array_keys($team_members));
        $exiting_user = Util::convert_to_associative_map($exiting_user, 'email', 'key');

        $new_members =array_diff_key($team_members,$exiting_user);

        $new_ids = [];
        foreach($new_members as $email=>$name) {
            $user_details = self::create_user(['name'=>$name, 'email'=>$email, 'password'=>substr(str_shuffle(MD5(microtime())), 0, 9)], 1);
            send_mail($user_details, 'registration');
            $new_ids[] = $user_details['key'];
        }

        return array_merge(array_values($exiting_user), $new_ids);
    }

    private static function get_user_key($name)
    {
        $name_key = Util::add_hyphens($name);
        $results = DB::queryFirstColumn("SELECT user.key FROM user WHERE `key` = %s", $name_key);
        if (empty($results)) return $name_key;
        sort($results);
        $last_integer = (integer)str_replace($name_key . '-', '', end($results)) + 1;
        return $name_key . "-" . $last_integer;
    }

    private static function create_hash($password, $salt) {
        $string = $password . $salt;
        return md5($string);
    }

    static function verify_email_address($info)
    {
        $user_details = self::fetch_user_details('email', $info['email'], "activation_token,active,email,name,lost_password_request");
        if(!empty($user_details)){
            Email::subscribe($user_details);
            if ($user_details['active'] == 1) {
                return ['state' => 'info', 'text' => 'Account already active. Please login using your credentials.'];
            } else if ($user_details['activation_token'] == $info['activation_token']) {
                self::update_user_details($info['email'], ['active' => 1]);
                if($user_details['lost_password_request']== 0)
                    return ['state' => 'success', 'text' => 'Account has been activated. Please login using your credentials'];
                else
                    return ['state' => 'reset_pwd', 'text' => 'Account has been activated. Please set your password'];
            }
        }
        return ['state' => 'error', 'text' => 'Wrong link used for Account Activation. Please contact support for help.'];
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
        self::set_user_data_session($user_details);
        return 'success';
    }

    public static function resend_verification_email($req_param)
    {
        if (!array_key_exists('email', $req_param) || empty($req_param['email']))
            return ['error', 'Email address missing from the request.'];
        $email = $req_param['email'];
        $user_details = self::fetch_user_details('email', $email, "name,email,activation_token");
        if (empty($user_details))
            return ['error', 'Email address "' . $email . '" does not belong to any registered user.'];
        send_mail($user_details, 'email_verification');
        return ['success', 'New verification email sent to ' . $email . '. Please check your inbox.'];
    }

    private static function set_user_data_session($user_details)
    {
        $username = $user_details['key'];
        $limited_user_details = [
            'name' => $user_details['name'],
            'email' => $user_details['email'],
            'username' => $username
        ];
        Session::add_user_details($limited_user_details);
    }

    private static function validate_details($user)
    {
        $errors = [];
        if (empty($user['name'])) {
            $errors['name'] = 'Please enter your name';
        }
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if (empty($user['password']) || strlen($user['password']) <= 6) {
            $errors['password'] = 'Password should be more than 6 characters.';
        }
        if ($user['password'] != $user['confirmation-password']) {
            $errors['confirmation-password'] = 'Confirmation password does not match.';
        }
        $records = self::fetch_user_details('email', $user['email']);
        if (!empty($records)) {
            $errors['email'] = 'Email already in use, please use another email address.';
        }
        return $errors;
    }

    static function process_forgot_password_request($email)
    {
        $user_details = self::fetch_user_details('email', $email);
        if ($user_details) {
            send_mail($user_details, 'password-recovery');
            self::update_user_details($user_details['email'], ['lost_password_request' => 1]);
            return ['success', "<h4>Password Recovery Mail sent to " . $user_details['email'] . "</h4><span>Please check your mail account, and click on the password recovery link to reset your password.</span>"];
        }
        return ['error', '<h4>Email Address Not found!</h4><span>Please check the email address entered and try again.</span>'];
    }

    static function process_password_reset_request($query_details)
    {
        if (isset($query_details) && array_key_exists('email', $query_details) && array_key_exists('activation_token', $query_details)) {
            $user_details = self::fetch_user_details('email', $query_details['email']);
            if ($user_details['activation_token'] == $query_details['activation_token'])
                return $user_details;
        }
        return ['error', '<h4>Invalid Password Recovery Link</h4><span>The password recovery link is broken. Please try to reset the password again.</span>'];
    }

    static function reset_password($form)
    {
        $user_details = self::process_password_reset_request($form);
        if (is_array($user_details) && 'error' == current($user_details)) return $user_details;
        $errors = self::validate_reset_password($form);
        if (!empty($errors)) return ['error', $errors];
        $password_hash = self::create_hash($form['password'], $user_details['sign_up_date']);
        self::update_user_details($form['email'], ['password' => $password_hash, 'lost_password_request' => 0]);
        Session::destroy();
        return ['success', 'Password Reset Successful! Please login using your new credentials.'];
    }

    static function fetch_user_details($query_param, $email, $required_params = "email,name,activation_token,sign_up_date")
    {
        return DB::queryFirstRow("SELECT %l FROM user WHERE `" . $query_param . "`=%s LIMIT 1", $required_params, $email);
    }

    static function display_profile($user_key)
    {
        return self::fetch_user_details('key', $user_key, "user.id, name, user.key, email, sign_up_date, bio, title, organization, active, sign_up_date");
    }

    private static function update_user_details($email, $params)
    {
        DB::update('user', $params, "email=%s", $email);
    }

    private static function validate_reset_password($form)
    {
        return self::validations_for($form['password'], [
            'min_length' => ['length' => 6],
            'compare' => ['value' => $form['confirmation-password']]
        ]);
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
        $username = Session::username();
        $bio = DB::queryFirstField("SELECT user.bio FROM user WHERE `key`=%s", $username);
        return !empty($bio) && strlen($bio) > 1;
    }

    public static function update_profile($username, $form)
    {
        $errors = self::validate_form($form);
        if (!empty($errors)) return $errors;
        $updated_email_address = $form['inputEmail'] != Session::email();
        if ($updated_email_address) {
            $user_details = self::fetch_user_details('email', $form['inputEmail'], "active");
            if (!empty($user_details)) {
                return "Email Address already associated with another account.";
            }
        }
        $profile_values = [
            'name' => $form['inputName'],
            'email' => $form['inputEmail'],
            'title' => $form['inputTitle'],
            'organization' => $form['inputOrganization'],
            'bio' => $form['inputBio'],
        ];
        if ($updated_email_address) {
            $activation_token = md5($form['sign_up_date']);
            $profile_values['activation_token'] = $activation_token;
            $profile_values['active'] = 0;
            $email_values = [
                'name' => Session::name(),
                'activation_token' => $activation_token,
                'email' => $form['inputEmail']
            ];
        }
        DB::update('user', $profile_values, "`key`=%s", $username);
        if ($updated_email_address and !(empty($email_values))) {
            send_mail($email_values, 'email_verification');
            self::logout();
            return 'ResetEmail';
        }
        return 'success';
    }

    private static function validate_form($form)
    {
        $required_fields = ['inputName' => 'Name', 'inputTitle' => 'Title', 'inputOrganization' => 'Organization', 'inputBio' => 'Bio', 'inputEmail' => 'Email Address'];
        return Util::validate_form_contains_required_fields($form, $required_fields);
    }

    public static function fetch_profile_data($username)
    {
        return self::display_profile($username);
    }

    public static function fetch_logged_in_users_info()
    {
        return DB::queryFirstRow("SELECT user.id, user.email, user.sign_up_date, user.active FROM user WHERE `key`=%s", Session::username());
    }

    public static function logout()
    {
        Session::destroy();
    }

    public static function fetch_email_and_activation_token()
    {
        return DB::queryFirstRow("select email, activation_token from user where `key`=%s", Session::username());
    }

    public static function fetch_all_ids()
    {
        return DB::queryFirstColumn("SELECT `key` FROM user where active=1");
    }

    public static function bulk_user_info($users)
    {
        return DB::query("select name, email from user where `key` in %ls", $users);
    }

    public static function resend_activation_email($org_id, $team_id)
    {
        $all_members = DB::query("select user.name, user.email, user.activation_token from user INNER JOIN org_structure on user.`key`=org_structure.username where org_structure.org_id=%s and org_structure.team_id=%s and user.active=0", $org_id, $team_id);
        foreach($all_members as $member) {
            send_mail($member, 'registration');
        }
        return count($all_members);
    }
}
