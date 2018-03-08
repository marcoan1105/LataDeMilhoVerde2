<?php
namespace LataDeMilhoVerde;

use Slim\App;
use LataDeMilhoVerde\DatabaseConnection;

class APILoad
{
    protected static $json;
    protected static $jsonDecode;

    public static function load()
    {
        //LOAD PARAMS
        self::$json = file_get_contents('config/api.json');
        self::$jsonDecode = json_decode(self::$json);
        self::makeApi();
    }

    //MAKE THE API
    protected static function makeApi(){

        $app = new App();

        $app->get('/{table}', function ($request, $response, $args) {
            $table = $request->getAttribute('table');

            $validTable = false;
            foreach(self::$jsonDecode->tables as $key => $nowTable){
                if($key == $table){
                    $connection = DatabaseConnection::getConnection();
                    $connection::selectAll($table);
                    $data = $connection::get();

                    /*return $response->withJSON(
                        self::formatReturn(true, $data),
                        200,
                        JSON_UNESCAPED_UNICODE
                    );*/
                }
                /*$primary = null;
                $colums = [];
                $validates = [];

                foreach($nowTable as $keyColums => $nowColums){
                    $colums[] = $keyColums;

                    $explode = explode('/', $keyColums->type);

                    foreach($explode as $explodeNow){
                        if($explodeNow == 'primary'){
                            $primary = $keyColums;
                            break;
                        }
                    }
                }*/



                /*if(!empty($primary)){
                    $app->post('/'.$key, function ($request, $response, $args) {
                        return $response->getBody()->write("Primary: Primary");
                    });
                }*/

            }

            /*return $response->withJSON(
                self::formatReturn(false, []),
                200,
                JSON_UNESCAPED_UNICODE
            );*/
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