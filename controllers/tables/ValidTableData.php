<?php

namespace LataDeMilhoVerde;

use LataDeMilhoVerde\Config;

class ValidTableData{

    public static function getPrimaryKeyTable($table){
        $json = json_decode(file_get_contents(Config::getFileApiJson()));

        foreach($json->tables as $key => $dataTable){
            if($table == $key){
                if(isset($dataTable->config->primary)){
                    return $dataTable->config->primary;
                }
                break;

            }
        }

        return "";
    }

    //GET PARAMS URL
    public static function getParamsUrl($url, $request){
        $urlExplode = explode("/", $url);

        $return = [];

        foreach ($urlExplode as $now){
            $nowExplode = explode("{", $now);

            if(count($nowExplode) == 2){
                $nowExplode2 = explode("}", $nowExplode[1]);

                $return[$nowExplode2[0]] = $request->getAttribute($nowExplode2[0]);
            }
        }

        return $return;
    }

    //REPLACE STRING BY TOKENS
    public static function changeParams($url, $params){

        foreach($params as $key => $now){
            $url = str_replace(":".$key, $now, $url);
        }

        return $url;
    }
}