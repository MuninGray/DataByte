<?php

class Descarga {

    private $conn;
    private $table_name = "descarga";

    public $matricula;
    public $id_establcmto;
    public $hora;
    public $peso;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (matricula, id_establcmto, hora, peso) VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->id_establcmto = (int) $this->id_establcmto;
        $this->hora = htmlspecialchars(strip_tags(trim($this->hora)));
        $this->peso = htmlspecialchars(strip_tags(trim($this->peso)));

        $stmt->bind_param("siss", $this->matricula, $this->id_establcmto, $this->hora, $this->peso);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT matricula, id_establcmto, hora, peso FROM `" . $this->table_name . "`";
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
        $query = "UPDATE `" . $this->table_name . "` SET peso = ? WHERE matricula = ? AND id_establcmto = ? AND hora = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->id_establcmto = (int) $this->id_establcmto;
        $this->hora = htmlspecialchars(strip_tags(trim($this->hora)));
        $this->peso = htmlspecialchars(strip_tags(trim($this->peso)));

        $stmt->bind_param("ssis", $this->peso, $this->matricula, $this->id_establcmto, $this->hora);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE matricula = ? AND id_establcmto = ? AND hora = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));
        $this->id_establcmto = (int) $this->id_establcmto;
        $this->hora = htmlspecialchars(strip_tags(trim($this->hora)));

        $stmt->bind_param("sis", $this->matricula, $this->id_establcmto, $this->hora);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
