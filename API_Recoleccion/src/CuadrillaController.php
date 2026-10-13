<?php
require_once "../config/database.php";
require_once "Cuadrilla.php";

class CuadrillaController {
    private $db;
    private $cuadrilla;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cuadrilla = new Cuadrilla($this->db);
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
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $cedula_inspector = $this->getInputValue($data, ["cedula_inspector"]);

        if (!empty($nom_cuadrilla) && !empty($cedula_inspector)) {
            $this->cuadrilla->nom_cuadrilla = $nom_cuadrilla;
            $this->cuadrilla->cedula_inspector = (int) $cedula_inspector;

            if ($this->cuadrilla->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Cuadrilla creada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Cuadrilla no creada", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function read() {
        $result = $this->cuadrilla->read();

        if ($result && $result->num_rows > 0) {
            $cuadrillas_arr = [];
            $cuadrillas_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $cuadrilla_item = [
                    "nom_cuadrilla" => $row["nom_cuadrilla"],
                    "cedula_inspector" => $row["cedula_inspector"]
                ];
                array_push($cuadrillas_arr["registros"], $cuadrilla_item);
            }

            http_response_code(200);
            echo json_encode($cuadrillas_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Cuadrillas no encontradas"]);
        }
    }

    public function update() {
        $data = $this->getPayload();
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $cedula_inspector = $this->getInputValue($data, ["cedula_inspector"]);

        if (!empty($nom_cuadrilla) && !empty($cedula_inspector)) {
            $this->cuadrilla->nom_cuadrilla = $nom_cuadrilla;
            $this->cuadrilla->cedula_inspector = (int) $cedula_inspector;

            if ($this->cuadrilla->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Cuadrilla actualizada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Cuadrilla no actualizada", "actualizar");
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
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);

        if (!empty($nom_cuadrilla)) {
            $this->cuadrilla->nom_cuadrilla = $nom_cuadrilla;

            if ($this->cuadrilla->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Cuadrilla eliminada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Cuadrilla no eliminada", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Nombre de cuadrilla requerido"]);
        }
    }
}
