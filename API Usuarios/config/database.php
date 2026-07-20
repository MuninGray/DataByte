<?php
//creacion de la base de datos y su conexion

class Database{
private $host = "localhost";
private $db_name = "databyte";
private $username ="root";
private $password = "";
public $conn;

public function getConnection(){
    $this->conn = null;
    try{
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        $this->conn->set_charset("utf8");
      }catch(throwable $th){
        echo "Error de conexion: " . $th->getMessage();

    }
    return $this->conn;
}
}