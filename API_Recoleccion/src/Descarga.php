<?php

class Descarga {

    private $conn;
    private $table_name = "Descarga";

    public $id_ruta;
    public $matricula;
    public $id_establcmto;
    public $hora;
    public $peso;
    public $cedula;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_ruta, matricula, id_establcmto, hora, peso, cedula) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_ruta = (int) $this->id_ruta;
        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->id_establcmto = (int) $this->id_establcmto;
        $this->hora = htmlspecialchars(strip_tags(trim($this->hora)));
        $this->peso = htmlspecialchars(strip_tags(trim($this->peso)));
        $this->cedula = htmlspecialchars(strip_tags(trim($this->cedula)));

        $stmt->bind_param("isssss", $this->id_ruta, $this->matricula, $this->id_establcmto, $this->hora, $this->peso, $this->cedula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_ruta, matricula, id_establcmto, hora, peso, cedula FROM `" . $this->table_name . "`";
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
        $query = "UPDATE `" . $this->table_name . "` SET matricula = ?, id_establcmto = ?, hora = ?, peso = ?, cedula = ? WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_ruta = (int) $this->id_ruta;
        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->id_establcmto = (int) $this->id_establcmto;
        $this->hora = htmlspecialchars(strip_tags(trim($this->hora)));
        $this->peso = htmlspecialchars(strip_tags(trim($this->peso)));
        $this->cedula = htmlspecialchars(strip_tags(trim($this->cedula)));

        $stmt->bind_param("sisssi", $this->matricula, $this->id_establcmto, $this->hora, $this->peso, $this->cedula, $this->id_ruta);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_ruta = (int) $this->id_ruta;
        $stmt->bind_param("i", $this->id_ruta);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
