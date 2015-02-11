<?php

class Team
{
    public static function fetch_all()
    {
        return DB::queryFirstColumn("SELECT `id` FROM team");
    }

    public static function add_only_if_new($team_id, $team_name)
    {
        DB::insert('team', ['id'=>$team_id, 'name'=>$team_name]);
    }
}
