<?php

class Competencies
{
    public static function fetch_all()
    {
        return DB::query("SELECT `id`, name, description FROM competencies");
    }
}
