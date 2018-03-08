<?php
namespace LataDeMilhoVerde;

interface Connection
{
    public static function selectAll($table);
    public static function get();
}