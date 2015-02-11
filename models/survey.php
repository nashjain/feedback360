<?php

include_once MODELS_DIR . 'util.php';

class Survey
{
    public static function create($form)
    {
        $required_fields = ['name' => 'Survey Name', 'org_id' => 'Org Name', 'team_id' => 'Team Name', 'competencies'=> 'Competencies'];
        $errors = Util::validate_form_contains_required_fields($form, $required_fields);

        if (!empty($errors)) return ['status'=>'error', 'value'=>$errors];

        DB::insert('survey', ['name'=>$form['name'], 'org_id'=>$form['org_id'], 'team_id'=>$form['team_id'], 'username'=>Session::get_user_property('username')]);
        $survey_id = DB::insertId();

        $competency_mapping = [];
        foreach($form['competencies'] as $competency) {
            $competency_mapping[] = ['survey_id'=>$survey_id, 'competency_id'=>$competency];
        }
        DB::insert('survey_competencies', $competency_mapping);

        return ['status'=>'Success', 'value'=>$survey_id];
    }

    public static function owner($survey_id)
    {
        return DB::queryFirstField("select username from survey where survey.id=%i LIMIT 1", $survey_id);
    }

    public static function details($id)
    {
        return DB::queryFirstRow("select * from survey where survey.id=%i", $id);
    }
}
