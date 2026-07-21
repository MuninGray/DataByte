<?php

class Vehiculo {

    private $conn;
    private $table_name = "vehiculos";

    public $modelo;
    public $matricula;
    public $en_uso;
    public $estado_optivo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (modelo, matricula, en_uso, estado_optivo) VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->modelo = htmlspecialchars(strip_tags(trim($this->modelo)));
        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->en_uso = htmlspecialchars(strip_tags(trim($this->en_uso)));
        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));

        $stmt->bind_param("ssss", $this->modelo, $this->matricula, $this->en_uso, $this->estado_optimo);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT modelo, matricula, en_uso, estado_optimo FROM `" . $this->table_name . "`";
        $result = $this->conn->query($query);
        return $result;
    }

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET modelo = ?, en_uso = ?, estado_optivo = ? WHERE matricula = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->modelo = htmlspecialchars(strip_tags(trim($this->modelo)));
        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
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
