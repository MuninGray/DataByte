<?php

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;

    public function __construct() {
        $host = getenv("DB_HOST");
        $dbName = getenv("DB_NAME");
        $username = getenv("DB_USER");
        $password = getenv("DB_PASSWORD");
        $port = getenv("DB_PORT");

        $this->host = $host !== false ? $host : "192.168.1.51";
        $this->db_name = $dbName !== false ? $dbName : "DataByte";
        $this->username = $username !== false ? $username : "";
        $this->password = $password !== false ? $password : "";
        $this->port = $port !== false && $port !== "" ? (int) $port : 3306;
        $this->conn = null;
    }

    public function getConnection() {
        $this->conn = null;

        if (trim($this->username) === "" || trim($this->password) === "") {
            error_log("DataByte MySQL configuration error: DB_USER and DB_PASSWORD are required.");
            return null;
        }

        try {
            mysqli_report(MYSQLI_REPORT_OFF);

            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->db_name,
                $this->port
            );

            if ($this->conn->connect_errno !== 0) {
                error_log(sprintf(
                    "DataByte MySQL connection failed (%d): %s",
                    $this->conn->connect_errno,
                    $this->conn->connect_error
                ));
                $this->conn->close();
                $this->conn = null;
                return null;
            }

            if (!$this->conn->set_charset("utf8mb4")) {
                error_log(sprintf(
                    "DataByte MySQL connection error (%d): %s",
                    $this->conn->errno,
                    $this->conn->error
                ));
                $this->conn->close();
                $this->conn = null;
                return null;
            }

            return $this->conn;
        } catch (Throwable $th) {
            error_log("DataByte MySQL connection exception: " . $th->getMessage());

            if ($this->conn instanceof mysqli) {
                $this->conn->close();
            }

            $this->conn = null;
            return null;
        }
    }
}
