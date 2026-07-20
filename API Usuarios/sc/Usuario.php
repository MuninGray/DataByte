<?php

class Usuario {

    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $correo;
    public $ci;
    public $contrasena;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (correo, ci, contrasena, creado_en) VALUES (?, ?, ?";

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
        $query = "SELECT id, correo, ci FROM `" . $this->table_name . "`";
        $result = $this->conn->query($query);
        return $result;
    }

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET correo = ?, ci = ?, contrasena = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->ci = htmlspecialchars(strip_tags($this->ci));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param(
            "sssi",
            $this->correo,
            $this->ci,
            $this->contrasena,
            $this->id
        );

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}