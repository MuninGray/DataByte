<?php
//creacion de la base de datos y su conexion

class Database{
private $host;
private $db_name;
private $username;
private $password;
private $conn;

public function __construct(){
    $this->host = getenv("DB_HOST") ?: "localhost";
    $this->db_name = getenv("DB_NAME") ?: "DataByte";
    $this->username = getenv("DB_USER") ?: "root";
    $this->password = getenv("DB_PASSWORD") ?: "";
}

public function getConnection(){
    $this->conn = null;
    try{
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        $this->conn->set_charset("utf8");
      }catch(throwable $th){
        echo "Error de conexion: " . $th->getMessage();

    }
    return $this->conn;
}
}
