<?php
namespace LataDeMilhoVerde;

use LataDeMilhoVerde\Database;
use PDO;


Class MysqlModel implements Connection{

    protected static $sql;
    protected static $connection;

    public static function pdoStatement(){
        self::createConnctionApi();
    }

    public static function selectAll($table){
        self::$sql = "select * from ".$table;
    }

    public static function get(){
        //try {
            self::createConnction();
            /*$command = self::$connection->prepare(self::$sql);
            $result = $command->execute();
            self::$sql = null;
            return $command->fetchAll(PDO::FETCH_ASSOC);*/
        /*}catch(Exception $e){
            throw new Exception('Get in mysql error');
        }finally{
            self::clearConnection();
        }*/

    }

    protected static function createConnction(){
        $fileApi = file_get_contents('config/api.json');

        $api= json_decode($fileApi);

        self::mysqlConnection($api->connection->data);
    }

    //CONNECTION MYSQL
    protected static function mysqlConnection($data){
        var_dump(PDO("mysql:host=127.0.0.1;dbname=teste", "marco", ""));
        exit;
        self::$connection = new PDO("mysql:host=localhost;dbname=teste", "root", "123456");
    }

    //DESTROY CONNECTION
    protected static function clearConnection(){
        self::$connection = null;
    }
}