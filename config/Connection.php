<?php
namespace LataDeMilhoVerde;

abstract class ConnectonA{

    protected static $connection;

    protected function createConnctionApi(){
        $fileApi = file_get_contents('config/api.json');

        $api= json_decode($fileApi);

        switch ($api->connection->type){
            case "mysql":
                $this->mysqlConnection($api->connection->data);
                break;
            default:
                throw new Exceptioin("Connection not found");
        }
    }

    //CONNECTION MYSQL
    protected static function mysqlConnection($data){
        self::$connection = new PDO("mysql:host={$data->host};dbname={$data->database}", $data->user, $data->password);
    }

    //DESTROY CONNECTION
    protected static function clearConnection(){
        self::$connection = null;
    }
}