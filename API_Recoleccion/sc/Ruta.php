<?php

class Ruta {

    private $conn;
    private $table_name = "rutas";

    public $id_ruta;
    public $nom;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_ruta, nom) VALUES (?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_ruta = htmlspecialchars(strip_tags(trim($this->id_ruta)));
        $this->nom = htmlspecialchars(strip_tags(trim($this->nom)));

        $stmt->bind_param("ss", $this->id_ruta, $this->nom);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_ruta, nom FROM `" . $this->table_name . "`";
        $result = $this->conn->query($query);
        return $result;
    }

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET nom = ? WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_ruta = htmlspecialchars(strip_tags(trim($this->id_ruta)));
        $this->nom = htmlspecialchars(strip_tags(trim($this->nom)));

        $stmt->bind_param("ss", $this->nom, $this->id_ruta);

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

        $this->id_ruta = htmlspecialchars(strip_tags(trim($this->id_ruta)));
        $stmt->bind_param("s", $this->id_ruta);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
