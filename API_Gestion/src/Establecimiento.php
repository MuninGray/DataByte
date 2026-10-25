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
    public $lat;
    public $lng;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (nombre, calle, nmro, esq, tipo, capac_actual, capac_max, tipo_res, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->nombre = htmlspecialchars(strip_tags(trim($this->nombre)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = (int) $this->nmro;
        $this->esq = htmlspecialchars(strip_tags(trim($this->esq)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->capac_actual = (float) $this->capac_actual;
        $this->capac_max = (float) $this->capac_max;
        $this->tipo_res = htmlspecialchars(strip_tags(trim($this->tipo_res)));
        $this->lat = ($this->lat === null || $this->lat === "") ? null : (float) $this->lat;
        $this->lng = ($this->lng === null || $this->lng === "") ? null : (float) $this->lng;

        $stmt->bind_param("ssissddsdd", $this->nombre, $this->calle, $this->nmro, $this->esq, $this->tipo, $this->capac_actual, $this->capac_max, $this->tipo_res, $this->lat, $this->lng);

        if ($stmt->execute()) {
            $this->id_establcmto = $this->conn->insert_id;
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_establcmto, nombre, calle, nmro, esq, tipo, capac_actual, capac_max, tipo_res, lat, lng FROM `" . $this->table_name . "`";
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
        $query = "SELECT id_establcmto, nombre, calle, nmro, esq, tipo, capac_actual, capac_max, tipo_res, lat, lng FROM `" . $this->table_name . "` WHERE id_establcmto = ?";
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

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET nombre = ?, calle = ?, nmro = ?, esq = ?, tipo = ?, capac_actual = ?, capac_max = ?, tipo_res = ?, lat = ?, lng = ? WHERE id_establcmto = ?";
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
        $this->lat = ($this->lat === null || $this->lat === "") ? null : (float) $this->lat;
        $this->lng = ($this->lng === null || $this->lng === "") ? null : (float) $this->lng;

        $stmt->bind_param("ssssssssddi", $this->nombre, $this->calle, $this->nmro, $this->esq, $this->tipo, $this->capac_actual, $this->capac_max, $this->tipo_res, $this->lat, $this->lng, $this->id_establcmto);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

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
