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
    public $lastError = "";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (id_contdor, tipo, estado, fch_apert, fch_resol, nom_cuadrilla, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

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

        $stmt->bind_param("isssssdd", $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->nom_cuadrilla, $this->lat, $this->lng);

        if ($stmt->execute()) {
            $this->id_incidencia = $this->conn->insert_id;
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
        $this->lastError = "";
        $consultaTipo = $this->conn->prepare("SELECT tipo FROM `" . $this->table_name . "` WHERE id_incidencia = ? AND id_contdor = ?");
        if (!$consultaTipo) {
            $this->lastError = $this->conn->error;
            return false;
        }
        $consultaTipo->bind_param("ii", $this->id_incidencia, $this->id_contdor);
        if (!$consultaTipo->execute()) {
            $this->lastError = $consultaTipo->error ?: $this->conn->error;
            $consultaTipo->close();
            return false;
        }
        $resultadoTipo = $consultaTipo->get_result();
        $tipoActual = $resultadoTipo ? $resultadoTipo->fetch_assoc() : null;
        $consultaTipo->close();
        if (!$tipoActual) {
            $this->lastError = "No se encontró la incidencia indicada.";
            return false;
        }

        $actualizarTipo = (string) $tipoActual["tipo"] !== (string) $this->tipo;
        $query = $actualizarTipo
            ? "UPDATE `" . $this->table_name . "` SET id_contdor = ?, tipo = ?, estado = ?, fch_apert = ?, fch_resol = ?, nom_cuadrilla = ?, lat = ?, lng = ? WHERE id_incidencia = ? AND id_contdor = ?"
            : "UPDATE `" . $this->table_name . "` SET id_contdor = ?, estado = ?, fch_apert = ?, fch_resol = ?, nom_cuadrilla = ?, lat = ?, lng = ? WHERE id_incidencia = ? AND id_contdor = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->lastError = $this->conn->error;
            return false;
        }

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

        if ($actualizarTipo) {
            $stmt->bind_param("iissssddii", $this->id_contdor, $this->tipo, $this->estado, $this->fch_apert, $this->fch_resol, $this->nom_cuadrilla, $this->lat, $this->lng, $this->id_incidencia, $this->id_contdor);
        } else {
            $stmt->bind_param("issssddii", $this->id_contdor, $this->estado, $this->fch_apert, $this->fch_resol, $this->nom_cuadrilla, $this->lat, $this->lng, $this->id_incidencia, $this->id_contdor);
        }

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $this->lastError = $stmt->error ?: $this->conn->error;
        $stmt->close();
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
