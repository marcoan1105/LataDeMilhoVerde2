<?php
namespace LataDeMilhoVerde;

use LataDeMilhoVerde\Database;
use LataDeMilhoVerde\Config;
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

    //CREATE SQL GET
    public static function get(){
        try {
            self::createConnction();
            $command = self::$connection->prepare(self::$sql);
            $result = $command->execute();
            self::$sql = null;
            if(!$result){
                return $command->errorInfo();
            }
            return $command->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e){
            throw new Exception('Get in mysql error');
        }finally{
            self::clearConnection();
        }
    }

    //CREATE SQL SET
    public static function set(){
        try {
            self::createConnction();
            $command = self::$connection->prepare(self::$sql);
            $result = $command->execute();
            self::$sql = null;
            if(!$result){
                return $command->errorInfo();
            }else{
                $id = self::$connection->lastInsertId();

                if($id == "0"){
                    return $result;
                }else{
                    return $id;
                }
            }
        }catch(PDOException $e){
            throw new PDOException('Get in mysql error');
        }finally{
            self::clearConnection();
        }
    }

    //CREATE INSERT
    public static function create($table, $data){
        $sql = "insert into {$table}(";

        $values = "(";

        $count = 0;
        foreach($data as $key => $now){
            $count++;

            $sql .= $key;
            $values .= "'".$now."'";
            if($count < count(json_decode(json_encode($data), true))){
                $sql .= ",";
                $values .= ",";
            }


        }

        $sql .= ") values ";
        $sql .= $values .= ")";

        self::$sql = $sql;

    }

    //DELETE LINE
    public static function delete($table, $primary, $id){
        self::$sql = "delete from ".$table." where ".$primary." = ".$id;
    }

    //CREATE UPDATE
    public static function update($table, $data, $primary){
        $sql = "update  {$table} set ";

        $where = "";

        $count = 0;
        foreach($data as $key => $now){
            $count++;

            if($key != $primary){
                $sql .= $key . " = '".$now."'";
            }else{
                $where .=  $key . " = '".$now."'";
            }

        }

        $sql .= " where ".$where;

        self::$sql = $sql;

    }

    //GET BY PRIMARY KEY TABLE
    public static function getByPrimaryKey($table, $primary, $key){
        $sql = 'select * from '.$table.' where '.$primary." = '".$key."' limit 1";
        self::$sql = $sql;
    }

    //START CONNECTION
    protected static function createConnction(){
        $fileApi = file_get_contents(Config::getFileApiJson());

        $api= json_decode($fileApi);

        self::mysqlConnection($api->connection->data);
    }

    //CONNECTION MYSQL
    protected static function mysqlConnection($data){
        if(self::$connection == null){
            self::$connection = new PDO("mysql:host={$data->host};dbname={$data->database}", $data->username, $data->password);
        }
    }

    //QUERY FUNCTION
    public static function query($query){
        self::$sql = $query;
    }

    //SELECT FLEX FUNCTION
    public static function selectQuery($select){
        self::query($select);
    }

    //DESTROY CONNECTION
    protected static function clearConnection(){
        self::$connection = null;
    }

    //GET SQL
    public static function getSql(){
        return self::$sql;
    }

    //MAKE QUERY INSERT
    public static function queryInsert($insert, $data){

        $count = 0;

        $colums = "";
        $values = "";
        /*var_dump($data);
        exit;*/
        foreach($data as $key => $now){
            $count++;

            $colums .= $key;
            $values .= "'".$now."'";
            if($count < count(json_decode(json_encode($data), true))){
                $colums .= ",";
                $values .= ",";
            }

        }

        $insert = str_replace(":colums", $colums, $insert);
        $insert = str_replace(":values", $values, $insert);

        self::query($insert);
    }
}