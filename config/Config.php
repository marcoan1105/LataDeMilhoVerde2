<?php
namespace LataDeMilhoVerde;

class Config{
	protected static $api_file = "json/api.json";
    protected static $sql_api_file = "json/sql.json";

	public static function getFileApiJson(){
		return self::$api_file;
	}

    public static function getSqlApiJson(){
        return self::$sql_api_file;
    }
}