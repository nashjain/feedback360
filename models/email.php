<?php
class Email {
    public static function add($input)
    {
        $data = array();
        $lines=preg_split("/[\r\n]+/", $input, -1, PREG_SPLIT_NO_EMPTY);
        foreach($lines as $line) {
            $email_name = explode("\t", $line);
            $data[] = array('email'=>$email_name[0], 'full_name'=>$email_name[1]);
        }
        DB::insertIgnore('user_email', $data);
        $inserted = DB::affectedRows();
        return "Added $inserted new emails. ".(count($data)-$inserted)." emails already exist/duplicates.";
    }

    public static function subscribe($user_info)
    {
        $email = $user_info['email'];
        if(!preg_match("/^\d+@twitter.com/i", $email))
            DB::insertIgnore('user_email', array('email'=> $email, 'full_name'=>$user_info['name']));
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
        $existing_email = DB::queryFirstField("select email from user_email where email=%s", $email_id);
        if(empty($existing_email))
            DB::insert('user_email', array('email'=> $email_id, 'full_name'=>"Unsub User", 'status'=>'unsub'));
        else
            DB::update("user_email", array("status"=>'unsub'), "email=%s", $email_id);
    }

    public static function is_not_active($email)
    {
        $status = DB::queryFirstField("select status from user_email where email=%s and status='active'", $email);
        return empty($status);
    }
}