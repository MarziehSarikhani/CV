<?php
class Table{
    protected $data = array();
    public function __construct($data){
        foreach($data as $key=>$value){
            if(array_key_exists($key,$this->data))
                if(is_numeric($value))
                    $this->data[$key] = (int)$value;
                else
                    $this->data[$key] = $value;
        }
    }
    public function __get($property){
        if(array_key_exists($property,$this->data))
            return $this->data[$property];
        else
            die("invalid property");
    }
    protected static function connectMysqli(){
        $conn = new mysqli(HOST_NAME,DB_USER,DB_PASSWORD,DB_NAME);
        $conn->set_charset("utf8");
        return $conn;
    }
    protected static function connectPDO(){
        return new PDO("mysql:host=".HOST_NAME.";dbname=".DB_NAME.";charset=utf8",DB_USER,DB_PASSWORD);
    }
    protected static function disconnect($conn){
        unset($conn);
    }
}