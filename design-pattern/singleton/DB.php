<?php

class DB
{
    private $conn;
    private static $_instance = null;

    private function __construct()
    {
        require_once 'db.config.php';

        $this->conn = mysqli_connect($db['host'],$db['user'],$db['password'],$db['database']);
        if(!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

    }

    private function __clone() {
    }

    public static function getInstance() {
        if(!self::$_instance instanceof  self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


}

$db = DB::getInstance();
var_dump($db);
