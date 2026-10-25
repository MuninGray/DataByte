<?php

class Mantenimiento {

    private $conn;
    private $table_name = "Servicios_Y_Mantenimientos";

    public $id_serv;
    public $estado;
    public $fecha;
    public $tipo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (estado, fecha, tipo) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fecha = htmlspecialchars(strip_tags(trim($this->fecha)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));

        $stmt->bind_param("sss", $this->estado, $this->fecha, $this->tipo);

        if ($stmt->execute()) {
            $this->id_serv = $this->conn->insert_id;
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_serv, estado, fecha, tipo FROM `" . $this->table_name . "`";
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

    public function readOne() {
        $query = "SELECT id_serv, estado, fecha, tipo FROM `" . $this->table_name . "` WHERE id_serv = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $this->id_serv = (int) $this->id_serv;
        $stmt->bind_param("i", $this->id_serv);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return false;
    }

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET estado = ?, fecha = ?, tipo = ? WHERE id_serv = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_serv = (int) $this->id_serv;
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fecha = htmlspecialchars(strip_tags(trim($this->fecha)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));

        $stmt->bind_param("sssi", $this->estado, $this->fecha, $this->tipo, $this->id_serv);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE id_serv = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_serv = (int) $this->id_serv;
        $stmt->bind_param("i", $this->id_serv);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
