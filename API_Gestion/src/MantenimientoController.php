<?php
require_once "../config/database.php";
require_once "Mantenimiento.php";

class MantenimientoController {
    private $db;
    private $mantenimiento;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->mantenimiento = new Mantenimiento($this->db);
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

        foreach ($keys as $key) {
            if (isset($_GET[$key])) {
                return trim((string) $_GET[$key]);
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
        $estado = $this->getInputValue($data, ["estado"]);
        $fecha = $this->getInputValue($data, ["fecha"]);
        $tipo = $this->getInputValue($data, ["tipo"]);

        if (in_array($estado, ["en_curso", "finalizado"], true) && $this->fechaValida($fecha) && in_array($tipo, ["pendiente", "preventivo", "correctivo", "otro"], true)) {
            $this->mantenimiento->estado = $estado;
            $this->mantenimiento->fecha = $fecha;
            $this->mantenimiento->tipo = $tipo;

            if ($this->mantenimiento->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Servicio de mantenimiento creado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Servicio de mantenimiento no creado", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function read() {
        $id_serv = $this->getInputValue([], ["id_serv"]);

        if (!empty($id_serv)) {
            $this->mantenimiento->id_serv = (int) $id_serv;
            $result = $this->mantenimiento->readOne();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                http_response_code(200);
                echo json_encode([
                    "id_serv" => $row["id_serv"],
                    "estado" => $row["estado"],
                    "fecha" => $row["fecha"],
                    "tipo" => $row["tipo"]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Servicio de mantenimiento no encontrado"]);
            }
            return;
        }

        $result = $this->mantenimiento->read();

        if ($result && $result->num_rows > 0) {
            $mantenimientos_arr = [];
            $mantenimientos_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $mantenimiento_item = [
                    "id_serv" => $row["id_serv"],
                    "estado" => $row["estado"],
                    "fecha" => $row["fecha"],
                    "tipo" => $row["tipo"]
                ];
                array_push($mantenimientos_arr["registros"], $mantenimiento_item);
            }

            http_response_code(200);
            echo json_encode($mantenimientos_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Servicios de mantenimiento no encontrados"]);
        }
    }

    public function update() {
        $data = $this->getPayload();
        $id_serv = $this->getInputValue($data, ["id_serv"]);
        $estado = $this->getInputValue($data, ["estado"]);
        $fecha = $this->getInputValue($data, ["fecha"]);
        $tipo = $this->getInputValue($data, ["tipo"]);

        if (filter_var($id_serv, FILTER_VALIDATE_INT) !== false && (int)$id_serv > 0 && in_array($estado, ["en_curso", "finalizado"], true) && $this->fechaValida($fecha) && in_array($tipo, ["pendiente", "preventivo", "correctivo", "otro"], true)) {
            $this->mantenimiento->id_serv = (int) $id_serv;
            $this->mantenimiento->estado = $estado;
            $this->mantenimiento->fecha = $fecha;
            $this->mantenimiento->tipo = $tipo;

            $result = $this->mantenimiento->readOne();
            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Servicio de mantenimiento no encontrado"]);
                return;
            }

            if ($this->mantenimiento->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Servicio de mantenimiento actualizado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Servicio de mantenimiento no actualizado", "actualizar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    private function fechaValida($fecha) {
        $date = DateTime::createFromFormat('Y-m-d', $fecha);
        return $date && $date->format('Y-m-d') === $fecha;
    }

    public function delete() {
        $data = $this->getPayload();
        $id_serv = $this->getInputValue($data, ["id_serv"]);

        if (!empty($id_serv)) {
            $this->mantenimiento->id_serv = (int) $id_serv;
            $result = $this->mantenimiento->readOne();

            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Servicio de mantenimiento no encontrado"]);
                return;
            }

            if ($this->mantenimiento->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Servicio de mantenimiento eliminado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Servicio de mantenimiento no eliminado", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
