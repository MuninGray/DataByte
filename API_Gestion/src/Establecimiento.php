<?php

class Establecimiento {

    private $conn;
    private $table_name = "Establecimiento";

    public $id_establcmto;
    public $nombre;
    public $calle;
    public $nmro;
    public $esq;
    public $tipo;
    public $capac_actual;
    public $capac_max;
    public $tipo_res;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear establecimiento usando prepared statement.
    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_establcmto, nombre, calle, nmro, esq, tipo, capac_actual, capac_max, tipo_res) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_establcmto = (int) $this->id_establcmto;
        $this->nombre = htmlspecialchars(strip_tags(trim($this->nombre)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = htmlspecialchars(strip_tags(trim($this->nmro)));
        $this->esq = htmlspecialchars(strip_tags(trim($this->esq)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->capac_actual = htmlspecialchars(strip_tags(trim($this->capac_actual)));
        $this->capac_max = htmlspecialchars(strip_tags(trim($this->capac_max)));
        $this->tipo_res = htmlspecialchars(strip_tags(trim($this->tipo_res)));

        $stmt->bind_param("issssssss", $this->id_establcmto, $this->nombre, $this->calle, $this->nmro, $this->esq, $this->tipo, $this->capac_actual, $this->capac_max, $this->tipo_res);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Leer todos los establecimientos usando prepared statement.
    public function read() {
        $query = "SELECT id_establcmto, nombre, calle, nmro, esq, tipo, capac_actual, capac_max, tipo_res FROM `" . $this->table_name . "`";
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

    // Obtener un establecimiento por id usando prepared statement.
    public function readOne() {
        $query = "SELECT id_establcmto, nombre, calle, nmro, esq, tipo, capac_actual, capac_max, tipo_res FROM `" . $this->table_name . "` WHERE id_establcmto = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $this->id_establcmto = (int) $this->id_establcmto;
        $stmt->bind_param("i", $this->id_establcmto);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return false;
    }

    // Actualizar establecimiento usando prepared statement.
    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET nombre = ?, calle = ?, nmro = ?, esq = ?, tipo = ?, capac_actual = ?, capac_max = ?, tipo_res = ? WHERE id_establcmto = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_establcmto = (int) $this->id_establcmto;
        $this->nombre = htmlspecialchars(strip_tags(trim($this->nombre)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = htmlspecialchars(strip_tags(trim($this->nmro)));
        $this->esq = htmlspecialchars(strip_tags(trim($this->esq)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->capac_actual = htmlspecialchars(strip_tags(trim($this->capac_actual)));
        $this->capac_max = htmlspecialchars(strip_tags(trim($this->capac_max)));
        $this->tipo_res = htmlspecialchars(strip_tags(trim($this->tipo_res)));

        $stmt->bind_param("ssssssssi", $this->nombre, $this->calle, $this->nmro, $this->esq, $this->tipo, $this->capac_actual, $this->capac_max, $this->tipo_res, $this->id_establcmto);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    // Eliminar establecimiento usando prepared statement.
    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE id_establcmto = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_establcmto = (int) $this->id_establcmto;
        $stmt->bind_param("i", $this->id_establcmto);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
