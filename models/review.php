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
        return DB::query("select survey.*, org.name as org_name, team.name as team_name from survey INNER JOIN org on org.id=org_id INNER JOIN team on team.id=team_id where survey.id IN (select DISTINCT (survey_id) from reviews where reviews.reviewee=%s and status='completed') order by survey.created desc", Session::username());
    }

    private static function review_details($status, $sort_column)
    {
        return DB::query("select reviews.*, user.name as reviewee_name, survey.name as survey_name, org.name as org_name, team.name as team_name from reviews INNER JOIN user on user.`key`=reviewee INNER JOIN survey on survey.id=survey_id INNER JOIN org on org.id=org_id INNER JOIN team on team.id=team_id where status='" . $status . "' and reviewer=%s order by reviews.".$sort_column." desc", Session::username());
    }

    public static function am_i_the_reviewer_for($id)
    {
        return (DB::queryFirstField("select count(*) as records from reviews where id=%i and reviewer=%s", $id, Session::username())>0);
    }

    public static function fetch_competencies_for($id)
    {
        return DB::query("select competencies.id as competency_id, name, description, 0 as rating, '' as good, '' as bad from competencies INNER JOIN survey_competencies on survey_competencies.competency_id=competencies.id where survey_competencies.survey_id = (select survey_id from reviews where id=%i)", $id);
    }

    public static function fetch_reviewee_name_for($id)
    {
        return DB::queryFirstField("SELECT user.name from reviews INNER JOIN user on user.`key`=reviews.reviewee where reviews.id=%i", $id);
    }

    public static function mark_completed($review_id)
    {
        DB::update('reviews', ['status'=>'completed'], '`id`=%i', $review_id);
    }

    public static function details_grouped_by_reviewee($survey_id)
    {
        $survey_name = DB::queryFirstField("select name from survey where survey.id=%i", $survey_id);
        $review = DB::query("select reviews.*, reviewee_user.name as reviewee_name, reviewer_user.name as reviewer_name from reviews INNER JOIN survey on survey_id=survey.id INNER JOIN user AS reviewee_user on reviewee_user.`key`=reviews.reviewee INNER JOIN user AS reviewer_user on reviewer_user.`key`=reviews.reviewer where survey_id=%i", $survey_id);
        $grouped_review = Util::group_to_associative_array($review, 'reviewee');
        return ['survey_id'=>$survey_id, 'survey_name'=>$survey_name, 'grouped_review'=>$grouped_review];
    }

    public static function current_assignment($survey_id)
    {
        $survey_details = DB::queryFirstRow("select name, org_id, team_id from survey where survey.id=%i", $survey_id);
        $org_id = $survey_details['org_id'];
        $team_id = $survey_details['team_id'];
        $employees = Team::all_members_from($org_id, $team_id);
        $team_members = Team::team_members_from($org_id, $team_id);
        $reviews = DB::query("select reviewee, reviewer from reviews where survey_id=%i order by reviewee", $survey_id);
        $current_assignment = Util::group_to_associative_array($reviews, 'reviewee');
        return ['survey_id'=>$survey_id, 'survey_name'=>$survey_details['name'], 'org_id'=>$org_id, 'team_id'=>$team_id, 'employees'=>$employees, 'team_members'=>$team_members, 'current_assignment'=>$current_assignment];
    }

    public static function update_reviewers($survey_id, $form)
    {
        $reviewers = DB::query("select id, reviewee, reviewer from reviews where survey_id=%i order by reviewee", $survey_id);
        $existing_mapping = self::to_associative_array($reviewers, 'reviewee', 'reviewer', 'id');
        $assignment = self::reviewer_reviewee_assignment($form);
        $mapping = [];
        $new_reviewers = [];
        $now = date('Y-m-d H:i:s');
        foreach($assignment as $reviewee=>$reviewers){
            $reviewers[] = $reviewee;
            foreach($reviewers as $reviewer) {
                $new_key = self::key($reviewee, $reviewer);
                if(array_key_exists($new_key, $existing_mapping))
                    $existing_mapping[$new_key] = 0;
                else {
                    $mapping[] = ['survey_id' => $survey_id, 'reviewee' => $reviewee, 'reviewer' => $reviewer, 'created'=>$now];
                    $new_reviewers[] = $reviewer;
                }
            }
        }

        $review_ids_to_be_deleted = array_filter(array_values($existing_mapping));

        DB::startTransaction();
        try {
            if(!empty($review_ids_to_be_deleted)) {
                DB::delete('reviews', 'id IN %li', $review_ids_to_be_deleted);
                DB::delete('feedback', 'review_id IN %li', $review_ids_to_be_deleted);
            }
            if(!empty($mapping))
                DB::insert('reviews', $mapping);
        } catch (MeekroDBException $e) {
            DB::rollback();
            return ['status'=>'error', 'value'=>"Could not assign the reviewers. Error: " . $e->getMessage()];
        }
        DB::commit();

        $count = self::notify($new_reviewers);
        $msg = 'Assigned reviewers';
        if($count>0)
            $msg .= " and a notification email has been sent to $count reviewers";
        return ['status'=>'success', 'value'=>$msg . "."];
    }

    private static function notify($all_reviewers)
    {
        if(empty($all_reviewers)) return 0;
        $unique_reviewers = array_unique($all_reviewers);
        $user_info = User::bulk_user_info($unique_reviewers);
        foreach($user_info as $user_details) {
            send_mail($user_details, 'new_review');
        }
        return count($unique_reviewers);
    }

    private static function reviewer_reviewee_assignment($form)
    {
        $surplus_fields = ['survey_name', 'org_id', 'team_id'];
        return array_diff_key($form, array_flip($surplus_fields));
    }

    private static function to_associative_array($reviewers, $key_1, $key_2, $value)
    {
        $existing_mapping = [];
        foreach ($reviewers as $reviewer_reviewee) {
            $key = self::key($reviewer_reviewee[$key_1], $reviewer_reviewee[$key_2]);
            $existing_mapping[$key] = $reviewer_reviewee[$value];
        }
        return $existing_mapping;
    }

    private static function key($reviewee, $reviewer)
    {
        return $reviewee . "__" . $reviewer;
    }
}
