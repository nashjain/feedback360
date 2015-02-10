<?php
class Email {
    public static function add($input)
    {
        $data = [];
        $lines=preg_split("/[\r\n]+/", $input, -1, PREG_SPLIT_NO_EMPTY);
        foreach($lines as $line) {
            $email_name = explode("\t", $line);
            $data[] = ['email'=>$email_name[0], 'full_name'=>$email_name[1]];
        }
        DB::insertIgnore('user_email', $data);
        $inserted = DB::affectedRows();
        return "Added $inserted new emails. ".(count($data)-$inserted)." emails already exist/duplicates.";
    }

    public static function subscribe($user_info)
    {
        DB::insertIgnore('user_email', ['email'=> $user_info['email'], 'full_name'=>$user_info['name']]);
    }

    public static function send_bulk_email($form)
    {
        $emails = DB::query("select email, full_name as `name` from user_email where status='active'");
        foreach($emails as $email) {
            send_bulk_email($form['inputSubject'], $form['inputMessage'], $email);
        }
        return "Sent emails to ". count($emails) . " users";
    }

    public static function unsub($email_id)
    {
        if(self::user_does_not_exists($email_id))
            DB::insert('user_email', ['email'=> $email_id, 'full_name'=>"Unsub User", 'status'=>'unsub']);
        else
            DB::update("user_email", ["status"=>'unsub'], "email=%s", $email_id);
    }

    public static function is_not_active($email)
    {
        $status = DB::queryFirstField("select status from user_email where email=%s and status='active'", $email);
        return empty($status);
    }

    public static function enroll($email_id)
    {
        if(self::user_does_not_exists($email_id))
            DB::insert('user_email', ['email'=> $email_id, 'full_name'=>"Subscribed User", 'status'=>'active']);
        else
            DB::update("user_email", ["status"=>'active', 'full_name'=>"Subscribed User"], "email=%s", $email_id);
    }

    private static function user_does_not_exists($email_id)
    {
        $existing_email = DB::queryFirstField("select email from user_email where email=%s", $email_id);
        return empty($existing_email);
    }
}