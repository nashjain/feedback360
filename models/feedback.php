<?php

include_once MODELS_DIR . 'competencies.php';
include_once MODELS_DIR . 'review.php';
include_once MODELS_DIR . 'util.php';

class Feedback
{
    public static function save($review_id, $form)
    {
        $feedback = self::grouped_feedback($review_id, $form);
        $competency_count = Competencies::count_for($review_id);
        if($competency_count!=count($feedback))
            return ['status'=>'error', 'msg'=>'Sorry, looks like you have not provided a rating on all the competencies. Please try again.'];
        DB::insert('feedback', $feedback);
        Review::mark_completed($review_id);
        return ['status'=>'success', 'msg'=>'Successfully saved your feedback.'];
    }

    private static function grouped_feedback($review_id, $form)
    {
        $feedback = [];
        $now = date('Y-m-d H:i:s');
        foreach($form as $field_name=>$field_value) {
            if (strpos($field_name,'_') === false) continue;
            list($competency_id, $actual_field_name) = explode('_', $field_name);
            if(!array_key_exists($competency_id, $feedback))
                $feedback[$competency_id] = ['review_id'=>$review_id, 'competency_id'=>$competency_id, 'created'=>$now, 'status'=>'completed'];
            $competency_values = $feedback[$competency_id];
            $competency_values[$actual_field_name] = $field_value;
            $feedback[$competency_id] = $competency_values;
        }
        return array_values($feedback);
    }

    public static function fetch_consolidated_reviewee_feedback_for($survey_id)
    {
        $feedback = DB::query("SELECT competencies.name, feedback.rating, feedback.good, feedback.bad FROM feedback INNER JOIN  competencies on competency_id=competencies.id INNER JOIN reviews on review_id=reviews.id INNER JOIN survey on reviews.survey_id=survey.id where reviews.status='completed' and reviews.reviewee=%s and survey.id=%i order by competency_id", Session::get_user_property('username'), $survey_id);
        $grouped_feedback = Util::group_to_associative_array($feedback, 'name');
        return self::update_average_rating($grouped_feedback);
    }

    public static function fetch_feedback_for_manager_for($review_id)
    {
        return DB::query("SELECT competencies.name, feedback.rating, feedback.good, feedback.bad FROM feedback INNER JOIN  competencies on competency_id=competencies.id INNER JOIN reviews on review_id=reviews.id where reviews.id=%i order by competency_id", $review_id);
    }

    private static function update_average_rating($grouped_feedback)
    {
        $result = [];
        foreach($grouped_feedback as $name=>$respective_feedback){
            $sum = 0;
            foreach($respective_feedback as $feedback)
                $sum += $feedback['rating'];
            $result[$name] = ['avg'=>$sum/count($respective_feedback), 'feedback'=>$respective_feedback];
        }
        return $result;
    }
}
