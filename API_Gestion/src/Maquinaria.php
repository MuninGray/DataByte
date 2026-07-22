<?php

class Maquinaria {

    private $conn;
    private $table_name = "Maquinaria";

    public $id_maquinaria;
    public $nombre;
    public $en_uso;
    public $id_establcmto;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear maquinaria usando prepared statement.
    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_maquinaria, nombre, en_uso, id_establcmto) VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_maquinaria = (int) $this->id_maquinaria;
        $this->nombre = htmlspecialchars(strip_tags(trim($this->nombre)));
        $this->en_uso = htmlspecialchars(strip_tags(trim($this->en_uso)));
        $this->id_establcmto = (int) $this->id_establcmto;

        $stmt->bind_param("issi", $this->id_maquinaria, $this->nombre, $this->en_uso, $this->id_establcmto);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Leer todas las maquinarias usando prepared statement.
    public function read() {
        $query = "SELECT id_maquinaria, nombre, en_uso, id_establcmto FROM `" . $this->table_name . "`";
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

    // Obtener una maquinaria por id usando prepared statement.
    public function readOne() {
        $query = "SELECT id_maquinaria, nombre, en_uso, id_establcmto FROM `" . $this->table_name . "` WHERE id_maquinaria = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $this->id_maquinaria = (int) $this->id_maquinaria;
        $stmt->bind_param("i", $this->id_maquinaria);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return false;
    }

    // Actualizar maquinaria usando prepared statement.
    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET nombre = ?, en_uso = ?, id_establcmto = ? WHERE id_maquinaria = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_maquinaria = (int) $this->id_maquinaria;
        $this->nombre = htmlspecialchars(strip_tags(trim($this->nombre)));
        $this->en_uso = htmlspecialchars(strip_tags(trim($this->en_uso)));
        $this->id_establcmto = (int) $this->id_establcmto;

        $stmt->bind_param("ssii", $this->nombre, $this->en_uso, $this->id_establcmto, $this->id_maquinaria);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Eliminar maquinaria usando prepared statement.
    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE id_maquinaria = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_maquinaria = (int) $this->id_maquinaria;
        $stmt->bind_param("i", $this->id_maquinaria);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
