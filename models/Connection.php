<?php
namespace LataDeMilhoVerde;

interface Connection
{
    public static function selectAll($table);
    public static function get();
    public static function create($table, $data);
    public static function set();
    public static function update($table, $data, $primary);
    public static function getByPrimaryKey($table, $primary, $key);
    public static function getSql();
    public static function query($query);
    public static function selectQuery($select);
    public static function delete($table, $primary, $id);
}