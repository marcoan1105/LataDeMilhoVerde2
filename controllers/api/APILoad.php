<?php
namespace LataDeMilhoVerde;

use Slim\App;
use LataDeMilhoVerde\DatabaseConnection;
use LataDeMilhoVerde\Database;
use LataDeMilhoVerde\Config;
use LataDeMilhoVerde\ValidTableData;

class APILoad
{
    protected static $json;
    protected static $jsonSql;
    protected static $jsonDecode;
    protected static $jsonSqlDecode;

    public static function load()
    {
        //LOAD PARAMS
        self::$json = file_get_contents(Config::getFileApiJson());
        self::$jsonDecode = json_decode(self::$json);

        self::$jsonSql = file_get_contents(Config::getSqlApiJson());
        self::$jsonSqlDecode = json_decode(self::$jsonSql);
        self::makeApi();
    }

    //MAKE THE API
    protected static function makeApi(){

        $app = new App();
        /*$app->get('/teste/{primary}/{teste}', function ($request, $response, $args) {

            $route = $request->getAttribute('route');
            $routeName = $route->getName();
            $groups = $route->getGroups();
            $methods = $route->getMethods();
            $arguments = $route->getArguments();

            //print "Route Info: " . print_r($route->getPattern(), true);
            //print "Route Name: " . print_r($routeName, true);
            //print "Route Groups: " . print_r($groups, true);
            print "Route Methods: " . print_r($methods, true);
            //print "Route Arguments: " . print_r($arguments, true);
            exit;
        });*/

        foreach(self::$jsonSqlDecode as $now){
            $method = $now->method;
            $app->$method($now->url, function ($request, $response, $args) {
                $path = "/".$request->getUri()->getPath();
                $pattern = $request->getAttribute('route')->getPattern();

                $jsonSql = json_decode(file_get_contents(Config::getSqlApiJson()));

                $tokensPath = ValidTableData::getParamsUrl($pattern, $request);

                $method = strtolower($request->getAttribute('route')->getMethods()[0]);

                foreach($jsonSql as $nowSql){
                    if($nowSql->url == $pattern){
                        if($nowSql->method == $method){
                            if($method == "put" || $method == "post"){
                                $body = $request->getBody();

                                if($body != ""){
                                    $body = json_decode($body, true);

                                    $tokensPath = array_merge($tokensPath, $body);
                                }
                            }

                            $sql = ValidTableData::changeParams($nowSql->sql,  $tokensPath);
                            $connection = DatabaseConnection::getConnection();
                            $connection::query($sql);

                            $result = "";
                            
                            if($nowSql->type == "get"){
                                $result = $connection::get();    
                            }else{
                                $result = $connection::set();
                            }
                            

                            return $response->withJSON(
                                self::formatReturn(true, $result),
                                200,
                                JSON_UNESCAPED_UNICODE
                            );
                            break;
                        }
                    }else{
                        continue;
                    }
                }

                return $response->withJSON(
                    self::formatReturn(false, []),
                    500,
                    JSON_UNESCAPED_UNICODE
                );

            });
            /*else if($now->method == "put"){
                $app->put($now->url, function ($request, $response, $args) {
                    $path = "/".$request->getUri()->getPath();

                    $jsonSql = json_decode(file_get_contents(Config::getSqlApiJson()));

                    foreach($jsonSql as $nowSql){
                        if($nowSql->url == $path){

                            $body = json_decode($request->getBody());


                            $connection = DatabaseConnection::getConnection();
                            $connection::queryInsert($nowSql->sql, $body);
                            $result = $connection::set();

                            return $response->withJSON(
                                self::formatReturn(true, $result),
                                200,
                                JSON_UNESCAPED_UNICODE
                            );
                            break;
                        }
                    }

                    return $response->withJSON(
                        self::formatReturn(false, []),
                        500,
                        JSON_UNESCAPED_UNICODE
                    );

                });

            }else if($now->method == "PUT"){
                $app->put($now->url, function ($request, $response, $args) {
                    $path = "/".$request->getUri()->getPath();

                    $jsonSql = json_decode(file_get_contents(Config::getSqlApiJson()));

                    foreach($jsonSql as $nowSql){
                        if($nowSql->url == $path){

                            $body = json_decode($request->getBody());


                            $connection = DatabaseConnection::getConnection();
                            $connection::queryInsert($nowSql->sql, $body);
                            $result = $connection::set();

                            return $response->withJSON(
                                self::formatReturn(true, $result),
                                200,
                                JSON_UNESCAPED_UNICODE
                            );
                            break;
                        }
                    }

                    return $response->withJSON(
                        self::formatReturn(false, []),
                        500,
                        JSON_UNESCAPED_UNICODE
                    );

                });

            }*/
        }

        $app->get('/{table}', function ($request, $response, $args) {
            $table = $request->getAttribute('table');

            foreach(self::$jsonDecode->tables as $key => $nowTable){
                if($key == $table){
                    $connection = DatabaseConnection::getConnection();
                    $connection::selectAll($table);
                    $data = $connection::get();

                    return $response->withJSON(
                        self::formatReturn(true, $data),
                        200,
                        JSON_UNESCAPED_UNICODE
                    );
                }

            }

            return $response->withJSON(
                self::formatReturn(false, []),
                500,
                JSON_UNESCAPED_UNICODE
            );
        });

        $app->put('/{table}', function($request, $response, $args){            

            $table = $request->getAttribute('table');

            foreach(self::$jsonDecode->tables as $key => $nowTable){
                if($key == $table){
                    $body = json_decode($request->getBody());


                    $connection = DatabaseConnection::getConnection();
                    $connection::create($table, $body);
                    $result = $connection::set();

                    return $response->withJSON(
                        self::formatReturn(true, [
                            "result" => $result
                        ]),
                        200,
                        JSON_UNESCAPED_UNICODE
                    );
                }

            }

            return $response->withJSON(
                self::formatReturn(false, []),
                200,
                JSON_UNESCAPED_UNICODE
            );
        });

        $app->post('/{table}', function($request, $response, $args){

            $table = $request->getAttribute('table');

            foreach(self::$jsonDecode->tables as $key => $nowTable){
                if($key == $table){
                    $body = json_decode($request->getBody());


                    $connection = DatabaseConnection::getConnection();

                    $connection::update($table, $body, ValidTableData::getPrimaryKeyTable($table));
                    $result = $connection::set();

                    return $response->withJSON(
                        self::formatReturn(true, [
                            "result" => $result
                        ]),
                        200,
                        JSON_UNESCAPED_UNICODE
                    );
                }

            }

            return $response->withJSON(
                self::formatReturn(false, []),
                200,
                JSON_UNESCAPED_UNICODE
            );
        });

        $app->get('/{table}/{id}', function ($request, $response, $args) {
            $table = $request->getAttribute('table');
            $id = $request->getAttribute('id');

            foreach(self::$jsonDecode->tables as $key => $nowTable){
                if($key == $table){
                    $connection = DatabaseConnection::getConnection();
                    $connection::getByPrimaryKey($table, ValidTableData::getPrimaryKeyTable($table), $id);
                    $data = $connection::get();

                    return $response->withJSON(
                        self::formatReturn(true, $data[0]),
                        200,
                        JSON_UNESCAPED_UNICODE
                    );
                }

            }

            return $response->withJSON(
                self::formatReturn(false, []),
                200,
                JSON_UNESCAPED_UNICODE
            );
        });

        $app->delete('/{table}/{id}', function ($request, $response, $args) {
            $table = $request->getAttribute('table');
            $id = $request->getAttribute('id');

            foreach(self::$jsonDecode->tables as $key => $nowTable){
                if($key == $table){
                    $connection = DatabaseConnection::getConnection();
                    $connection::delete($table, ValidTableData::getPrimaryKeyTable($table), $id);
                    $data = $connection::set();

                    return $response->withJSON(
                        self::formatReturn(true, $data[0]),
                        200,
                        JSON_UNESCAPED_UNICODE
                    );
                }

            }

            return $response->withJSON(
                self::formatReturn(false, []),
                200,
                JSON_UNESCAPED_UNICODE
            );
        });


        $app->run();
    }

    public static function formatReturn($status, $data){
        return [
          "status" => $status,
          "data" => $data
        ];
    }
}