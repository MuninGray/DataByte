<?php

class Cuadrilla {

    private $conn;
    private $table_name = "Cuadrilla";

    public $nom_cuadrilla;
    public $cedula_inspector;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (nom_cuadrilla, cedula_inspector, estado) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->nom_cuadrilla = htmlspecialchars(strip_tags(trim($this->nom_cuadrilla)));
        $this->cedula_inspector = htmlspecialchars(strip_tags(trim($this->cedula_inspector)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));

        $stmt->bind_param("sss", $this->nom_cuadrilla, $this->cedula_inspector, $this->estado);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT nom_cuadrilla, cedula_inspector, estado FROM `" . $this->table_name . "`";
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

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET cedula_inspector = ?, estado = ? WHERE nom_cuadrilla = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->nom_cuadrilla = htmlspecialchars(strip_tags(trim($this->nom_cuadrilla)));
        $this->cedula_inspector = htmlspecialchars(strip_tags(trim($this->cedula_inspector)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));

        $stmt->bind_param("sss", $this->cedula_inspector, $this->estado, $this->nom_cuadrilla);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE nom_cuadrilla = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->nom_cuadrilla = htmlspecialchars(strip_tags(trim($this->nom_cuadrilla)));
        $stmt->bind_param("s", $this->nom_cuadrilla);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
