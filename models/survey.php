<?php

include_once MODELS_DIR . 'util.php';

class Survey
{
    public static function create($form)
    {
        $required_fields = ['name' => 'Survey Name', 'org_id' => 'Org Name', 'team_id' => 'Team Name', 'competencies'=> 'Competencies'];
        $errors = Util::validate_form_contains_required_fields($form, $required_fields);

        if (!empty($errors)) return ['status'=>'error', 'value'=>$errors];

        $now = date('Y-m-d H:i:s');
        try{
            DB::insert('survey', ['name'=>$form['name'], 'org_id'=>$form['org_id'], 'team_id'=>$form['team_id'], 'username'=>Session::username(), 'created'=>$now]);
        }catch (MeekroDBException $e) {
            return ['status'=>'error', 'value'=>$e->getMessage()];
        }
        $survey_id = DB::insertId();

        $competency_mapping = [];
        foreach($form['competencies'] as $competency) {
            $competency_mapping[] = ['survey_id'=>$survey_id, 'competency_id'=>$competency];
        }
        DB::insert('survey_competencies', $competency_mapping);

        return ['status'=>'success', 'value'=>$survey_id];
    }

    public static function owner($survey_id)
    {
        return DB::queryFirstField("select username from survey where survey.id=%i LIMIT 1", $survey_id);
    }

    public static function fetch_my_surveys()
    {
        return DB::query("select survey.*, org.name as org_name, team.name as team_name from survey INNER JOIN org on org.id=org_id INNER JOIN team on team.id=team_id where survey.username=%s order by created desc", Session::username());
    }

    public static function is_owned_by($survey_id)
    {
        return Session::username() == self::owner($survey_id);
    }
}
