<?php

class Municipio {

    private $conn;
    private $table_name = "municipio";

    public $codigo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear municipio usando prepared statement.
    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (codigo) VALUES (?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $stmt->bind_param("s", $this->codigo);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Leer todos los municipios usando prepared statement.
    public function read() {
        $query = "SELECT codigo FROM `" . $this->table_name . "`";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return false;
    }

    // Obtener un municipio por codigo usando prepared statement.
    public function readOne() {
        $query = "SELECT codigo FROM `" . $this->table_name . "` WHERE codigo = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $stmt->bind_param("s", $this->codigo);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return false;
    }

    // Actualizar municipio usando prepared statement.
    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET codigo = ? WHERE codigo = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $stmt->bind_param("ss", $this->codigo, $this->codigo);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Eliminar municipio usando prepared statement.
    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE codigo = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $stmt->bind_param("s", $this->codigo);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
