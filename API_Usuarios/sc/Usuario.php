<?php

class Usuario {

    private $conn;
    private $table_name = "usuarios";

    public $correo;
    public $ci;
    public $contrasena;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (correo, ci, contrasena) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->ci = htmlspecialchars(strip_tags($this->ci));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));

        $stmt->bind_param(
            "sss",
            $this->correo,
            $this->ci,
            $this->contrasena
        );

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT correo, ci FROM `" . $this->table_name . "`";
        $result = $this->conn->query($query);
        return $result;
    }

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET correo = ?, ci = ?, contrasena = ? WHERE ci = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->ci = htmlspecialchars(strip_tags($this->ci));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));

        $stmt->bind_param(
            "ssss",
            $this->correo,
            $this->ci,
            $this->contrasena,
            $this->ci
        );

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}