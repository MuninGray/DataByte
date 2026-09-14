<?php
require_once "../config/database.php";
require_once "Municipio.php";

class MunicipioController {
    private $db;
    private $municipio;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->municipio = new Municipio($this->db);
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

    // Crear municipio.
    public function create() {
        $data = $this->getPayload();
        $codigo = $this->getInputValue($data, ["codigo"]);

        if (!empty($codigo)) {
            $this->municipio->codigo = $codigo;

            if ($this->municipio->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Municipio creado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Municipio no creado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Leer municipios.
    public function read() {
        $codigo = $this->getInputValue([], ["codigo"]);

        if (!empty($codigo)) {
            $this->municipio->codigo = $codigo;
            $result = $this->municipio->readOne();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                http_response_code(200);
                echo json_encode([
                    "codigo" => $row["codigo"]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Municipio no encontrado"]);
            }
            return;
        }

        $result = $this->municipio->read();

        if ($result && $result->num_rows > 0) {
            $municipios_arr = [];
            $municipios_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $municipio_item = [
                    "codigo" => $row["codigo"]
                ];
                array_push($municipios_arr["registros"], $municipio_item);
            }

            http_response_code(200);
            echo json_encode($municipios_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Municipios no encontrados"]);
        }
    }

    // Actualizar municipio.
    public function update() {
        $data = $this->getPayload();
        $codigo = $this->getInputValue($data, ["codigo"]);

        if (!empty($codigo)) {
            $this->municipio->codigo = $codigo;
            $result = $this->municipio->readOne();

            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Municipio no encontrado"]);
                return;
            }

            if ($this->municipio->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Municipio actualizado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Municipio no actualizado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Eliminar municipio.
    public function delete() {
        $data = $this->getPayload();
        $codigo = $this->getInputValue($data, ["codigo"]);

        if (!empty($codigo)) {
            $this->municipio->codigo = $codigo;
            $result = $this->municipio->readOne();

            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Municipio no encontrado"]);
                return;
            }

            if ($this->municipio->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Municipio eliminado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Municipio no eliminado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
