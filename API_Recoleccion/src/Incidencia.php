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
    public $tmp_resol;
    public $nom_cuadrilla;
    public $cedula;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_incidencia, id_contdor, tipo, estado, fch_apert, fch_resol, tmp_resol, nom_cuadrilla, cedula) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $this->id_contdor = htmlspecialchars(strip_tags(trim($this->id_contdor)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fch_apert = htmlspecialchars(strip_tags(trim($this->fch_apert)));
        $this->fch_resol = htmlspecialchars(strip_tags(trim($this->fch_resol)));
        $this->tmp_resol = htmlspecialchars(strip_tags(trim($this->tmp_resol)));
        $this->nom_cuadrilla = htmlspecialchars(strip_tags(trim($this->nom_cuadrilla)));
        $this->cedula = htmlspecialchars(strip_tags(trim($this->cedula)));

        $stmt->bind_param("issssssss", $this->id_incidencia, $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->tmp_resol, $this->nom_cuadrilla, $this->cedula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id_incidencia, id_contdor, tipo, estado, fch_apert, fch_resol, tmp_resol, nom_cuadrilla, cedula FROM `" . $this->table_name . "`";
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
        $query = "UPDATE `" . $this->table_name . "` SET id_contdor = ?, tipo = ?, estado = ?, fch_apert = ?, fch_resol = ?, tmp_resol = ?, nom_cuadrilla = ?, cedula = ? WHERE id_incidencia = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $this->id_contdor = htmlspecialchars(strip_tags(trim($this->id_contdor)));
        $this->tipo = htmlspecialchars(strip_tags(trim($this->tipo)));
        $this->estado = htmlspecialchars(strip_tags(trim($this->estado)));
        $this->fch_apert = htmlspecialchars(strip_tags(trim($this->fch_apert)));
        $this->fch_resol = htmlspecialchars(strip_tags(trim($this->fch_resol)));
        $this->tmp_resol = htmlspecialchars(strip_tags(trim($this->tmp_resol)));
        $this->nom_cuadrilla = htmlspecialchars(strip_tags(trim($this->nom_cuadrilla)));
        $this->cedula = htmlspecialchars(strip_tags(trim($this->cedula)));

        $stmt->bind_param("ssssssssi", $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->tmp_resol, $this->nom_cuadrilla, $this->cedula, $this->id_incidencia);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE id_incidencia = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->id_incidencia = (int) $this->id_incidencia;
        $stmt->bind_param("i", $this->id_incidencia);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}
