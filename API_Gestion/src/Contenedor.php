<?php

class Contenedor {

    private $conn;
    private $table_name = "contenedor";

    public $id_contdor;
    public $estado_optivo;
    public $en_uso;
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
        $query = "INSERT INTO `" . $this->table_name . "` (id_contdor, estado_optivo, en_uso, tipo, calle, nmro, esq, codigo, matricula) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_contdor = (int) $this->id_contdor;
        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));
        $this->en_uso = !is_null($this->en_uso) ? htmlspecialchars(strip_tags(trim($this->en_uso))) : null;
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = (int) $this->nmro;
        $this->esq = !is_null($this->esq) ? htmlspecialchars(strip_tags(trim($this->esq))) : null;
        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $this->matricula = !is_null($this->matricula) ? htmlspecialchars(strip_tags(trim($this->matricula))) : null;

        $stmt->bind_param("isssissss", $this->id_contdor, $this->estado_optivo, $this->en_uso, $this->tipo, $this->calle, $this->nmro, $this->esq, $this->codigo, $this->matricula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Leer todos los contenedores usando prepared statement.
    public function read() {
        $query = "SELECT id_contdor, estado_optivo, en_uso, tipo, calle, nmro, esq, codigo, matricula FROM `" . $this->table_name . "`";
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
        $query = "SELECT id_contdor, estado_optivo, en_uso, tipo, calle, nmro, esq, codigo, matricula FROM `" . $this->table_name . "` WHERE id_contdor = ?";
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
        $query = "UPDATE `" . $this->table_name . "` SET estado_optivo = ?, en_uso = ?, tipo = ?, calle = ?, nmro = ?, esq = ?, codigo = ?, matricula = ? WHERE id_contdor = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_contdor = (int) $this->id_contdor;
        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));
        $this->en_uso = !is_null($this->en_uso) ? htmlspecialchars(strip_tags(trim($this->en_uso))) : null;
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = (int) $this->nmro;
        $this->esq = !is_null($this->esq) ? htmlspecialchars(strip_tags(trim($this->esq))) : null;
        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $this->matricula = !is_null($this->matricula) ? htmlspecialchars(strip_tags(trim($this->matricula))) : null;

        $stmt->bind_param("ssssisssi", $this->estado_optivo, $this->en_uso, $this->tipo, $this->calle, $this->nmro, $this->esq, $this->codigo, $this->matricula, $this->id_contdor);

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
