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

    private function getDbErrorMessage($defaultMessage, $operation) {
        $error = $this->db->error ?? "";
        if (empty($error)) {
            return $defaultMessage;
        }

        $error = preg_replace('/\s+/', ' ', trim($error));
        $lower = strtolower($error);

        if (strpos($lower, 'duplicate') !== false || strpos($lower, 'ya existe') !== false) {
            return 'Ya existe un registro con ese valor.';
        }

        if (strpos($lower, 'foreign key') !== false || strpos($lower, 'child row') !== false) {
            return 'La operación no se puede completar porque hay datos relacionados que no existen o no son válidos.';
        }

        if (strpos($lower, 'not null') !== false || strpos($lower, 'cannot be null') !== false) {
            return 'Falta un dato obligatorio.';
        }

        if (strpos($lower, 'data too long') !== false || strpos($lower, 'too long') !== false) {
            return 'Uno de los datos excede la longitud permitida.';
        }

        return ucfirst($operation) . ': ' . $error;
    }

    public function create() {
        $data = $this->getPayload();
        $id_incidencia = $this->getInputValue($data, ["id_incidencia"]);
        $id_contdor = $this->getInputValue($data, ["id_contdor"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $estado = $this->getInputValue($data, ["estado"]);
        $fch_apert = $this->getInputValue($data, ["fch_apert"]);
        $fch_resol = $this->getInputValue($data, ["fch_resol"]);
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $lat = $this->getInputValue($data, ["lat", "latitude"]);
        $lng = $this->getInputValue($data, ["lng", "longitude"]);

        if (!empty($id_incidencia) && !empty($id_contdor) && !empty($tipo) && !empty($estado) && !empty($fch_apert)) {
            $this->incidencia->id_incidencia = (int) $id_incidencia;
            $this->incidencia->id_contdor = (int) $id_contdor;
            $this->incidencia->tipo = $tipo;
            $this->incidencia->estado = $estado;
            $this->incidencia->fch_apert = $fch_apert;
            $this->incidencia->fch_resol = $fch_resol !== "" ? $fch_resol : null;
            $this->incidencia->nom_cuadrilla = $nom_cuadrilla !== "" ? $nom_cuadrilla : null;
            $this->incidencia->lat = $lat !== "" ? floatval($lat) : null;
            $this->incidencia->lng = $lng !== "" ? floatval($lng) : null;

            if ($this->incidencia->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Incidencia creada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Incidencia no creada", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
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
                    "nom_cuadrilla" => $row["nom_cuadrilla"],
                    "lat" => $row["lat"],
                    "lng" => $row["lng"]
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
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $lat = $this->getInputValue($data, ["lat", "latitude"]);
        $lng = $this->getInputValue($data, ["lng", "longitude"]);

        if (!empty($id_incidencia) && !empty($id_contdor) && !empty($tipo) && !empty($estado) && !empty($fch_apert)) {
            $this->incidencia->id_incidencia = (int) $id_incidencia;
            $this->incidencia->id_contdor = (int) $id_contdor;
            $this->incidencia->tipo = $tipo;
            $this->incidencia->estado = $estado;
            $this->incidencia->fch_apert = $fch_apert;
            $this->incidencia->fch_resol = $fch_resol !== "" ? $fch_resol : null;
            $this->incidencia->nom_cuadrilla = $nom_cuadrilla !== "" ? $nom_cuadrilla : null;
            $this->incidencia->lat = $lat !== "" ? floatval($lat) : null;
            $this->incidencia->lng = $lng !== "" ? floatval($lng) : null;

            if ($this->incidencia->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Incidencia actualizada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Incidencia no actualizada", "actualizar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function delete() {
        $data = $this->getPayload();
        $id_incidencia = $this->getInputValue($data, ["id_incidencia"]);
        $id_contdor = $this->getInputValue($data, ["id_contdor"]);

        if (!empty($id_incidencia) && !empty($id_contdor)) {
            $this->incidencia->id_incidencia = (int) $id_incidencia;
            $this->incidencia->id_contdor = (int) $id_contdor;

            if ($this->incidencia->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Incidencia eliminada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Incidencia no eliminada", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID de incidencia y código de contenedor requeridos"]);
        }
    }
}
