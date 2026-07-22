<?php

class Ruta {

    private $conn;
    private $table_name = "Ruta";

    public $id_ruta;
    public $nom;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_ruta, nom) VALUES (?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_ruta = (int) $this->id_ruta;
        $this->nom = htmlspecialchars(strip_tags(trim($this->nom)));

        $stmt->bind_param("is", $this->id_ruta, $this->nom);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_ruta, nom FROM `" . $this->table_name . "`";
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
        $query = "UPDATE `" . $this->table_name . "` SET nom = ? WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_ruta = (int) $this->id_ruta;
        $this->nom = htmlspecialchars(strip_tags(trim($this->nom)));

        $stmt->bind_param("si", $this->nom, $this->id_ruta);

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
