<?php
require_once "../config/database.php";
require_once "Descarga.php";

class DescargaController {
    private $db;
    private $descarga;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->descarga = new Descarga($this->db);
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
        $matricula = $this->getInputValue($data, ["matricula"]);
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);
        $hora = $this->getInputValue($data, ["hora"]);
        $peso = $this->getInputValue($data, ["peso"]);

        if (!empty($matricula) && !empty($id_establcmto) && !empty($hora) && !empty($peso)) {
            $this->descarga->matricula = $matricula;
            $this->descarga->id_establcmto = (int) $id_establcmto;
            $this->descarga->hora = $hora;
            $this->descarga->peso = $peso;

            if ($this->descarga->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Descarga creada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Descarga no creada", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function read() {
        $result = $this->descarga->read();

        if ($result && $result->num_rows > 0) {
            $descargas_arr = [];
            $descargas_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $descarga_item = [
                    "matricula" => $row["matricula"],
                    "id_establcmto" => $row["id_establcmto"],
                    "hora" => $row["hora"],
                    "peso" => $row["peso"]
                ];
                array_push($descargas_arr["registros"], $descarga_item);
            }

            http_response_code(200);
            echo json_encode($descargas_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Descargas no encontradas"]);
        }
    }

    public function update() {
        $data = $this->getPayload();
        $matricula = $this->getInputValue($data, ["matricula"]);
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);
        $hora = $this->getInputValue($data, ["hora"]);
        $peso = $this->getInputValue($data, ["peso"]);

        if (!empty($matricula) && !empty($id_establcmto) && !empty($hora) && !empty($peso)) {
            $this->descarga->matricula = $matricula;
            $this->descarga->id_establcmto = (int) $id_establcmto;
            $this->descarga->hora = $hora;
            $this->descarga->peso = $peso;

            if ($this->descarga->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Descarga actualizada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Descarga no actualizada", "actualizar");
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
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);
        $hora = $this->getInputValue($data, ["hora"]);

        if (!empty($matricula) && !empty($id_establcmto) && !empty($hora)) {
            $this->descarga->matricula = $matricula;
            $this->descarga->id_establcmto = (int) $id_establcmto;
            $this->descarga->hora = $hora;

            if ($this->descarga->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Descarga eliminada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Descarga no eliminada", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
