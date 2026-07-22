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

    public function create() {
        $data = $this->getPayload();
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $matricula = $this->getInputValue($data, ["matricula"]);
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);
        $hora = $this->getInputValue($data, ["hora"]);
        $peso = $this->getInputValue($data, ["peso"]);
        $cedula = $this->getInputValue($data, ["cedula"]);

        if (!empty($id_ruta) && !empty($matricula) && !empty($id_establcmto) && !empty($hora) && !empty($peso) && !empty($cedula)) {
            $this->descarga->id_ruta = (int) $id_ruta;
            $this->descarga->matricula = $matricula;
            $this->descarga->id_establcmto = (int) $id_establcmto;
            $this->descarga->hora = $hora;
            $this->descarga->peso = $peso;
            $this->descarga->cedula = $cedula;

            if ($this->descarga->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Descarga creada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Descarga no creada"]);
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
                    "id_ruta" => $row["id_ruta"],
                    "matricula" => $row["matricula"],
                    "id_establcmto" => $row["id_establcmto"],
                    "hora" => $row["hora"],
                    "peso" => $row["peso"],
                    "cedula" => $row["cedula"]
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
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $matricula = $this->getInputValue($data, ["matricula"]);
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);
        $hora = $this->getInputValue($data, ["hora"]);
        $peso = $this->getInputValue($data, ["peso"]);
        $cedula = $this->getInputValue($data, ["cedula"]);

        if (!empty($id_ruta) && !empty($matricula) && !empty($id_establcmto) && !empty($hora) && !empty($peso) && !empty($cedula)) {
            $this->descarga->id_ruta = (int) $id_ruta;
            $this->descarga->matricula = $matricula;
            $this->descarga->id_establcmto = (int) $id_establcmto;
            $this->descarga->hora = $hora;
            $this->descarga->peso = $peso;
            $this->descarga->cedula = $cedula;

            if ($this->descarga->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Descarga actualizada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Descarga no actualizada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function delete() {
        $data = $this->getPayload();
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);

        if (!empty($id_ruta)) {
            $this->descarga->id_ruta = (int) $id_ruta;

            if ($this->descarga->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Descarga eliminada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Descarga no eliminada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID de ruta requerido"]);
        }
    }
}
