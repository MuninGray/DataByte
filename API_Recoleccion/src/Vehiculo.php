<?php

class Vehiculo {

    private $conn;
    private $table_name = "Vehiculo";

    public $matricula;
    public $modelo;
    public $en_uso;
    public $estado_optivo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (matricula, modelo, en_uso, estado_optivo) VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->modelo = htmlspecialchars(strip_tags(trim($this->modelo)));
        $this->en_uso = htmlspecialchars(strip_tags(trim($this->en_uso)));
        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));

        $stmt->bind_param("ssss", $this->matricula, $this->modelo, $this->en_uso, $this->estado_optivo);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT matricula, modelo, en_uso, estado_optivo FROM `" . $this->table_name . "`";
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
        $query = "UPDATE `" . $this->table_name . "` SET modelo = ?, en_uso = ?, estado_optivo = ? WHERE matricula = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->modelo = htmlspecialchars(strip_tags(trim($this->modelo)));
        $this->en_uso = htmlspecialchars(strip_tags(trim($this->en_uso)));
        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));

        $stmt->bind_param("ssss", $this->modelo, $this->en_uso, $this->estado_optivo, $this->matricula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE matricula = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $stmt->bind_param("s", $this->matricula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
