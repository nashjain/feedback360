<?php

include_once MODELS_DIR . 'user.php';
include_once MODELS_DIR . 'mailer.php';

class Review
{
    public static $ratings = [
                                '0'=> '0: Not Applicable',
                                '1'=> '1: Never Demonstrated This',
                                '2'=> '2: Seldom Demonstrated This',
                                '3'=> '3: Sometimes Demonstrated This',
                                '4'=> '4: Usually Demonstrated This',
                                '5'=> '5: Always Demonstrated This',
                            ];

    public static function assign_reviewers($form)
    {
        $survey_id = $form['survey_id'];
        $surplus_fields = ['survey_id', 'survey_name', 'org_id', 'team_id'];
        $assignment = array_diff_key($form,array_flip($surplus_fields));
        $mapping = [];
        $all_reviewers = [];
        $now = date('Y-m-d H:i:s');
        foreach($assignment as $reviewee=>$reviewers){
            foreach($reviewers as $reviewer) {
                $mapping[] = ['survey_id' => $survey_id, 'reviewee' => $reviewee, 'reviewer' => $reviewer, 'created'=>$now];
            }
            $all_reviewers = array_merge($all_reviewers, $reviewers);
        }
        DB::insert('reviews', $mapping);
        $count = self::notify($all_reviewers);
        return ['status'=>'Success', 'value'=>"A notification email has been sent to $count reviewers."];
    }

    private static function notify($all_reviewers)
    {
        $unique_reviewers = array_unique($all_reviewers);
        $user_info = User::bulk_user_info($unique_reviewers);
        foreach($user_info as $user_details) {
            send_mail($user_details, 'new_review');
        }
        return count($unique_reviewers);
    }

    public static function pending()
    {
        return self::review_details("pending", 'created');
    }

    public static function given()
    {
        return self::review_details("completed", 'updated');
    }

    public static function received()
    {
        return DB::query("select survey.*, org.name as org_name, team.name as team_name from survey INNER JOIN org on org.id=org_id INNER JOIN team on team.id=team_id where survey.id IN (select DISTINCT (survey_id) from reviews where reviews.reviewee=%s and status='completed') order by survey.created desc", Session::get_user_property('username'));
    }

    private static function review_details($status, $sort_column)
    {
        return DB::query("select reviews.*, user.name as reviewee_name, survey.name as survey_name, org.name as org_name, team.name as team_name from reviews INNER JOIN user on user.`key`=reviewee INNER JOIN survey on survey.id=survey_id INNER JOIN org on org.id=org_id INNER JOIN team on team.id=team_id where status='" . $status . "' and reviewer=%s order by reviews.".$sort_column." desc", Session::get_user_property('username'));
    }

    public static function is_the_reviewer_for($id)
    {
        return (DB::queryFirstField("select count(*) as matches from reviews where id=%i and reviewer=%s", $id, Session::get_user_property('username'))>0);
    }

    public static function fetch_competencies_for($id)
    {
        return DB::query("select competencies.* from competencies INNER JOIN survey_competencies on survey_competencies.competency_id=competencies.id where survey_competencies.survey_id = (select survey_id from reviews where id=%i)", $id);
    }
}
