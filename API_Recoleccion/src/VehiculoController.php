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

    public function create() {
        $data = $this->getPayload();
        $matricula = $this->getInputValue($data, ["matricula"]);
        $estado_optivo = $this->getInputValue($data, ["estado_optivo"]);

        if (!empty($matricula) && !empty($estado_optivo)) {
            $this->vehiculo->matricula = $matricula;
            $this->vehiculo->estado_optivo = $estado_optivo;

            if ($this->vehiculo->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Vehículo creado exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Vehículo no creado"]);
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
                http_response_code(503);
                echo json_encode(["message" => "Vehículo no actualizado"]);
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
                http_response_code(503);
                echo json_encode(["message" => "Vehículo no eliminado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Matrícula requerida"]);
        }
    }
}
