<?php

class Incidencia {

    private $conn;
    private $table_name = "incidencia";

    public $id_incidencia;
    public $id_contdor;
    public $tipo;
    public $estado;
    public $fch_apert;
    public $fch_resol;
    public $nom_cuadrilla;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_incidencia, id_contdor, tipo, estado, fch_apert, fch_resol, nom_cuadrilla) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $this->id_contdor = (int) $this->id_contdor;
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fch_apert = htmlspecialchars(strip_tags(trim($this->fch_apert)));
        $this->fch_resol = $this->fch_resol !== null ? htmlspecialchars(strip_tags(trim($this->fch_resol))) : null;
        $this->nom_cuadrilla = $this->nom_cuadrilla !== null ? htmlspecialchars(strip_tags(trim($this->nom_cuadrilla))) : null;

        if ($this->fch_resol === "") {
            $this->fch_resol = null;
        }
        if ($this->nom_cuadrilla === "") {
            $this->nom_cuadrilla = null;
        }

        $stmt->bind_param("iisssss", $this->id_incidencia, $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->nom_cuadrilla);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_incidencia, id_contdor, tipo, estado, fch_apert, fch_resol, nom_cuadrilla FROM `" . $this->table_name . "`";
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
        $query = "UPDATE `" . $this->table_name . "` SET id_contdor = ?, tipo = ?, estado = ?, fch_apert = ?, fch_resol = ?, nom_cuadrilla = ? WHERE id_incidencia = ? AND id_contdor = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $this->id_contdor = (int) $this->id_contdor;
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fch_apert = htmlspecialchars(strip_tags(trim($this->fch_apert)));
        $this->fch_resol = $this->fch_resol !== null ? htmlspecialchars(strip_tags(trim($this->fch_resol))) : null;
        $this->nom_cuadrilla = $this->nom_cuadrilla !== null ? htmlspecialchars(strip_tags(trim($this->nom_cuadrilla))) : null;

        if ($this->fch_resol === "") {
            $this->fch_resol = null;
        }
        if ($this->nom_cuadrilla === "") {
            $this->nom_cuadrilla = null;
        }

        $stmt->bind_param("issssssi", $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->nom_cuadrilla, $this->id_incidencia, $this->id_contdor);

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
