<?php

class Contenedor {

    private $conn;
    private $table_name = "Contenedor";

    public $id_contdor;
    public $estado_optivo;
    public $tipo;
    public $calle;
    public $nmro;
    public $esq;
    public $codigo;
    public $matricula;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear contenedor usando prepared statement.
    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_contdor, estado_optivo, tipo, calle, nmro, esq, codigo, matricula) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_contdor = (int) $this->id_contdor;
        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = htmlspecialchars(strip_tags(trim($this->nmro)));
        $this->esq = htmlspecialchars(strip_tags(trim($this->esq)));
        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));

        $stmt->bind_param("isssssss", $this->id_contdor, $this->estado_optivo, $this->tipo, $this->calle, $this->nmro, $this->esq, $this->codigo, $this->matricula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Leer todos los contenedores usando prepared statement.
    public function read() {
        $query = "SELECT id_contdor, estado_optivo, tipo, calle, nmro, esq, codigo, matricula FROM `" . $this->table_name . "`";
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

    // Obtener un contenedor por id usando prepared statement.
    public function readOne() {
        $query = "SELECT id_contdor, estado_optivo, tipo, calle, nmro, esq, codigo, matricula FROM `" . $this->table_name . "` WHERE id_contdor = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $this->id_contdor = (int) $this->id_contdor;
        $stmt->bind_param("i", $this->id_contdor);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return false;
    }

    // Actualizar contenedor usando prepared statement.
    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET estado_optivo = ?, tipo = ?, calle = ?, nmro = ?, esq = ?, codigo = ?, matricula = ? WHERE id_contdor = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_contdor = (int) $this->id_contdor;
        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = htmlspecialchars(strip_tags(trim($this->nmro)));
        $this->esq = htmlspecialchars(strip_tags(trim($this->esq)));
        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $this->matricula = htmlspecialchars(strip_tags(trim($this->matricula)));

        $stmt->bind_param("sssssssi", $this->estado_optivo, $this->tipo, $this->calle, $this->nmro, $this->esq, $this->codigo, $this->matricula, $this->id_contdor);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Eliminar contenedor usando prepared statement.
    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE id_contdor = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_contdor = (int) $this->id_contdor;
        $stmt->bind_param("i", $this->id_contdor);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
