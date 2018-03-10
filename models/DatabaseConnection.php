<?php
namespace LataDeMilhoVerde;

use LataDeMilhoVerde\Database;
use LataDeMilhoVerde\MysqlModel;
use LataDeMilhoVerde\Config;

class DatabaseConnection
{
    public static function getConnection(){
        $fileApi = file_get_contents(Config::getFileApiJson());

        $api= json_decode($fileApi);

        switch ($api->connection->type){
            case "mysql":
                return new MysqlModel;
                break;
            default:
                throw new Exceptioin("Connection not found");
        }
    }
}