<?php

class Contenedor {

    private $conn;
    private $table_name = "Contenedor";

    public $id_contdor;
    public $estado_optivo;
    public $en_uso;
    public $tipo;
    public $calle;
    public $nmro;
    public $esq;
    public $codigo;
    public $matricula;
    public $id_ruta;
    public $lat;
    public $lng;
    public $lastError = null;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (estado_optivo, en_uso, tipo, calle, nmro, esq, codigo, matricula, id_ruta, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->estado_optivo = htmlspecialchars(strip_tags(trim($this->estado_optivo)));
        $this->en_uso = !is_null($this->en_uso) ? htmlspecialchars(strip_tags(trim($this->en_uso))) : null;
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->calle = htmlspecialchars(strip_tags(trim($this->calle)));
        $this->nmro = (int) $this->nmro;
        $this->esq = !is_null($this->esq) ? htmlspecialchars(strip_tags(trim($this->esq))) : null;
        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo)));
        $this->matricula = !is_null($this->matricula) ? htmlspecialchars(strip_tags(trim($this->matricula))) : null;
        $this->lat = ($this->lat === null || $this->lat === "") ? null : (float) $this->lat;
        $this->lng = ($this->lng === null || $this->lng === "") ? null : (float) $this->lng;

        $stmt->bind_param("ssssisssidd", $this->estado_optivo, $this->en_uso, $this->tipo, $this->calle, $this->nmro, $this->esq, $this->codigo, $this->matricula, $this->id_ruta, $this->lat, $this->lng);

        if ($stmt->execute()) {
            $this->id_contdor = $this->conn->insert_id;
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT c.id_contdor, c.estado_optivo, c.en_uso, c.tipo, c.calle, c.nmro, c.esq, c.codigo, c.matricula,
                         COALESCE(c.id_ruta, (SELECT r.id_ruta FROM Ruta r WHERE r.matricula = c.matricula ORDER BY r.id_ruta DESC LIMIT 1)) AS id_ruta,
                         c.lat, c.lng
                  FROM `" . $this->table_name . "` c";
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
        $query = "SELECT c.id_contdor, c.estado_optivo, c.en_uso, c.tipo, c.calle, c.nmro, c.esq, c.codigo, c.matricula,
                         COALESCE(c.id_ruta, (SELECT r.id_ruta FROM Ruta r WHERE r.matricula = c.matricula ORDER BY r.id_ruta DESC LIMIT 1)) AS id_ruta,
                         c.lat, c.lng
                  FROM `" . $this->table_name . "` c WHERE c.id_contdor = ?";
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

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET estado_optivo = ?, en_uso = ?, tipo = ?, calle = ?, nmro = ?, esq = ?, codigo = ?, matricula = ?, id_ruta = ?, lat = ?, lng = ? WHERE id_contdor = ?";
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
        $this->lat = ($this->lat === null || $this->lat === "") ? null : (float) $this->lat;
        $this->lng = ($this->lng === null || $this->lng === "") ? null : (float) $this->lng;

        $stmt->bind_param("ssssisssiddi", $this->estado_optivo, $this->en_uso, $this->tipo, $this->calle, $this->nmro, $this->esq, $this->codigo, $this->matricula, $this->id_ruta, $this->lat, $this->lng, $this->id_contdor);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $this->lastError = $stmt->error ?: $this->conn->error;
        $stmt->close();
        return false;
    }

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
