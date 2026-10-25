<?php
require_once "../config/database.php";
require_once "Vehiculo.php";

class VehiculoController {
    private $db;
    private $vehiculo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->vehiculo = new Vehiculo($this->db);
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
            return 'No se puede crear el vehículo porque ya existe una matrícula registrada.';
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
        $matricula = strtoupper(trim((string) $this->getInputValue($data, ["matricula"])));
        $estado_optivo = $this->getInputValue($data, ["estado_optivo"]);

        if (!empty($matricula) && !empty($estado_optivo)) {
            $checkStmt = $this->db->prepare("SELECT matricula FROM `Vehiculo` WHERE matricula = ? LIMIT 1");
            if ($checkStmt) {
                $checkStmt->bind_param("s", $matricula);
                $checkStmt->execute();
                $result = $checkStmt->get_result();
                if ($result && $result->num_rows > 0) {
                    $checkStmt->close();
                    http_response_code(409);
                    echo json_encode(["message" => "No se puede crear el vehículo porque ya existe una matrícula registrada."]);
                    return;
                }
                $checkStmt->close();
            }

            $this->vehiculo->matricula = $matricula;
            $this->vehiculo->estado_optivo = $estado_optivo;

            if ($this->vehiculo->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Vehículo creado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Vehículo no creado", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function read() {
        $result = $this->vehiculo->read();

        if ($result && $result->num_rows > 0) {
            $vehiculos_arr = [];
            $vehiculos_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $vehiculo_item = [
                    "matricula" => $row["matricula"],
                    "estado_optivo" => $row["estado_optivo"]
                ];
                array_push($vehiculos_arr["registros"], $vehiculo_item);
            }

            http_response_code(200);
            echo json_encode($vehiculos_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Vehículos no encontrados"]);
        }
    }

    public function update() {
        $data = $this->getPayload();
        $matricula = $this->getInputValue($data, ["matricula"]);
        $estado_optivo = $this->getInputValue($data, ["estado_optivo"]);

        if (!empty($matricula) && !empty($estado_optivo)) {
            $this->vehiculo->matricula = $matricula;
            $this->vehiculo->estado_optivo = $estado_optivo;

            if ($this->vehiculo->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Vehículo actualizado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Vehículo no actualizado", "actualizar");
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
        $matricula = $this->getInputValue($data, ["matricula"]);

        if (!empty($matricula)) {
            $this->vehiculo->matricula = $matricula;

            if ($this->vehiculo->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Vehículo eliminado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Vehículo no eliminado", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Matrícula requerida"]);
        }
    }
}
