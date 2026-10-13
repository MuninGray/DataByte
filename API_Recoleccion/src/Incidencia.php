<?php

class Incidencia {

    private $conn;
    private $table_name = "Incidencia";

    public $id_incidencia;
    public $id_contdor;
    public $tipo;
    public $estado;
    public $fch_apert;
    public $fch_resol;
    public $nom_cuadrilla;
    public $lat;
    public $lng;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_incidencia, id_contdor, tipo, estado, fch_apert, fch_resol, nom_cuadrilla, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $this->id_contdor = (int) $this->id_contdor;
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fch_apert = htmlspecialchars(strip_tags(trim($this->fch_apert)));
        $this->fch_resol = $this->fch_resol !== null ? htmlspecialchars(strip_tags(trim($this->fch_resol))) : null;
        $this->nom_cuadrilla = $this->nom_cuadrilla !== null ? htmlspecialchars(strip_tags(trim($this->nom_cuadrilla))) : null;
        $this->lat = ($this->lat === null || $this->lat === "") ? null : (float) $this->lat;
        $this->lng = ($this->lng === null || $this->lng === "") ? null : (float) $this->lng;

        if ($this->fch_resol === "") {
            $this->fch_resol = null;
        }
        if ($this->nom_cuadrilla === "") {
            $this->nom_cuadrilla = null;
        }

        $stmt->bind_param("iissssdd", $this->id_incidencia, $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->nom_cuadrilla, $this->lat, $this->lng);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_incidencia, id_contdor, tipo, estado, fch_apert, fch_resol, nom_cuadrilla, lat, lng FROM `" . $this->table_name . "`";
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
        $query = "UPDATE `" . $this->table_name . "` SET id_contdor = ?, tipo = ?, estado = ?, fch_apert = ?, fch_resol = ?, nom_cuadrilla = ?, lat = ?, lng = ? WHERE id_incidencia = ? AND id_contdor = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $this->id_contdor = (int) $this->id_contdor;
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fch_apert = htmlspecialchars(strip_tags(trim($this->fch_apert)));
        $this->fch_resol = $this->fch_resol !== null ? htmlspecialchars(strip_tags(trim($this->fch_resol))) : null;
        $this->nom_cuadrilla = $this->nom_cuadrilla !== null ? htmlspecialchars(strip_tags(trim($this->nom_cuadrilla))) : null;
        $this->lat = ($this->lat === null || $this->lat === "") ? null : (float) $this->lat;
        $this->lng = ($this->lng === null || $this->lng === "") ? null : (float) $this->lng;

        if ($this->fch_resol === "") {
            $this->fch_resol = null;
        }
        if ($this->nom_cuadrilla === "") {
            $this->nom_cuadrilla = null;
        }

        $stmt->bind_param("iissssddii", $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->nom_cuadrilla, $this->lat, $this->lng, $this->id_incidencia, $this->id_contdor);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE id_incidencia = ? AND id_contdor = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $this->id_contdor = (int) $this->id_contdor;
        $stmt->bind_param("ii", $this->id_incidencia, $this->id_contdor);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
