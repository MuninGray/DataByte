<?php
require_once "../config/database.php";
require_once "Incidencia.php";

class IncidenciaController {
    private $db;
    private $incidencia;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->incidencia = new Incidencia($this->db);
    }

    private function getPayload() {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return [];
        }

        return $data;
    }

    private function getInputValue($data, $keys) {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                return trim((string) $data[$key]);
            }
        }

        return "";
    }

    public function create() {
        $data = $this->getPayload();
        $id_incidencia = $this->getInputValue($data, ["id_incidencia"]);
        $id_contdor = $this->getInputValue($data, ["id_contdor"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $estado = $this->getInputValue($data, ["estado"]);
        $fch_apert = $this->getInputValue($data, ["fch_apert"]);
        $fch_resol = $this->getInputValue($data, ["fch_resol"]);
        $tmp_resol = $this->getInputValue($data, ["tmp_resol"]);
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $cedula = $this->getInputValue($data, ["cedula"]);

        if (!empty($id_incidencia) && !empty($id_contdor) && !empty($tipo) && !empty($estado) && !empty($fch_apert) && !empty($nom_cuadrilla) && !empty($cedula)) {
            $this->incidencia->id_incidencia = (int) $id_incidencia;
            $this->incidencia->id_contdor = $id_contdor;
            $this->incidencia->tipo = $tipo;
            $this->incidencia->estado = $estado;
            $this->incidencia->fch_apert = $fch_apert;
            $this->incidencia->fch_resol = $fch_resol;
            $this->incidencia->tmp_resol = $tmp_resol;
            $this->incidencia->nom_cuadrilla = $nom_cuadrilla;
            $this->incidencia->cedula = $cedula;

            if ($this->incidencia->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Incidencia creada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Incidencia no creada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function read() {
        $result = $this->incidencia->read();

        if ($result && $result->num_rows > 0) {
            $incidencias_arr = [];
            $incidencias_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $incidencia_item = [
                    "id_incidencia" => $row["id_incidencia"],
                    "id_contdor" => $row["id_contdor"],
                    "tipo" => $row["tipo"],
                    "estado" => $row["estado"],
                    "fch_apert" => $row["fch_apert"],
                    "fch_resol" => $row["fch_resol"],
                    "tmp_resol" => $row["tmp_resol"],
                    "nom_cuadrilla" => $row["nom_cuadrilla"],
                    "cedula" => $row["cedula"]
                ];
                array_push($incidencias_arr["registros"], $incidencia_item);
            }

            http_response_code(200);
            echo json_encode($incidencias_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Incidencias no encontradas"]);
        }
    }

    public function update() {
        $data = $this->getPayload();
        $id_incidencia = $this->getInputValue($data, ["id_incidencia"]);
        $id_contdor = $this->getInputValue($data, ["id_contdor"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $estado = $this->getInputValue($data, ["estado"]);
        $fch_apert = $this->getInputValue($data, ["fch_apert"]);
        $fch_resol = $this->getInputValue($data, ["fch_resol"]);
        $tmp_resol = $this->getInputValue($data, ["tmp_resol"]);
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $cedula = $this->getInputValue($data, ["cedula"]);

        if (!empty($id_incidencia) && !empty($id_contdor) && !empty($tipo) && !empty($estado) && !empty($fch_apert) && !empty($nom_cuadrilla) && !empty($cedula)) {
            $this->incidencia->id_incidencia = (int) $id_incidencia;
            $this->incidencia->id_contdor = $id_contdor;
            $this->incidencia->tipo = $tipo;
            $this->incidencia->estado = $estado;
            $this->incidencia->fch_apert = $fch_apert;
            $this->incidencia->fch_resol = $fch_resol;
            $this->incidencia->tmp_resol = $tmp_resol;
            $this->incidencia->nom_cuadrilla = $nom_cuadrilla;
            $this->incidencia->cedula = $cedula;

            if ($this->incidencia->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Incidencia actualizada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Incidencia no actualizada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function delete() {
        $data = $this->getPayload();
        $id_incidencia = $this->getInputValue($data, ["id_incidencia"]);

        if (!empty($id_incidencia)) {
            $this->incidencia->id_incidencia = (int) $id_incidencia;

            if ($this->incidencia->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Incidencia eliminada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Incidencia no eliminada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID de incidencia requerido"]);
        }
    }
}
