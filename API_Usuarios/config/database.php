<?php

class Database {
    private $host = "192.168.1.51";
    private $db_name = "DataByte";
    private $username = "databyte_app";
    private $password = "DB2026";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            mysqli_report(MYSQLI_REPORT_OFF);

            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->db_name,
                3306
            );

            $this->conn->set_charset("utf8mb4");

        } catch (Throwable $th) {
            echo "Error de conexion: " . $th->getMessage();
        }

        return $this->conn;
    }
}