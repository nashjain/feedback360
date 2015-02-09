<?php

    use phpish\mysql;

class ModelName {

    public static function fetch_something($name) {
        return ['msg'=>"Hi ".ucwords($name)."!"];
//      return mysql\rows("select col_1, col_2 from table_name where col_1 ='%s' order by `time` asc", array($some_id));
    }
}