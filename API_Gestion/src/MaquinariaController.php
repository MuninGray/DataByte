<?php
require_once "../config/database.php";
require_once "Maquinaria.php";

class MaquinariaController {
    private $db;
    private $maquinaria;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->maquinaria = new Maquinaria($this->db);
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

    // Crear maquinaria verificando que exista el establecimiento.
    public function create() {
        $data = $this->getPayload();
        $id_maquinaria = $this->getInputValue($data, ["id_maquinaria"]);
        $nombre = $this->getInputValue($data, ["nombre"]);
        $en_uso = $this->getInputValue($data, ["en_uso"]);
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);

        if (!empty($id_maquinaria) && !empty($nombre) && !empty($en_uso) && !empty($id_establcmto)) {
            $check = $this->db->prepare("SELECT id_establcmto FROM establecimiento WHERE id_establcmto = ?");
            $check->bind_param("i", $id_establcmto);
            $check->execute();
            $result = $check->get_result();
            $check->close();

            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "El establecimiento no existe"]);
                return;
            }

            $this->maquinaria->id_maquinaria = (int) $id_maquinaria;
            $this->maquinaria->nombre = $nombre;
            $this->maquinaria->en_uso = $en_uso;
            $this->maquinaria->id_establcmto = (int) $id_establcmto;

            if ($this->maquinaria->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Maquinaria creada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Maquinaria no creada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Leer maquinarias.
    public function read() {
        $id_maquinaria = $this->getInputValue([], ["id_maquinaria"]);

        if (!empty($id_maquinaria)) {
            $this->maquinaria->id_maquinaria = (int) $id_maquinaria;
            $result = $this->maquinaria->readOne();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                http_response_code(200);
                echo json_encode([
                    "id_maquinaria" => $row["id_maquinaria"],
                    "nombre" => $row["nombre"],
                    "en_uso" => $row["en_uso"],
                    "id_establcmto" => $row["id_establcmto"]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Maquinaria no encontrada"]);
            }
            return;
        }

        $result = $this->maquinaria->read();

        if ($result && $result->num_rows > 0) {
            $maquinarias_arr = [];
            $maquinarias_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $maquinaria_item = [
                    "id_maquinaria" => $row["id_maquinaria"],
                    "nombre" => $row["nombre"],
                    "en_uso" => $row["en_uso"],
                    "id_establcmto" => $row["id_establcmto"]
                ];
                array_push($maquinarias_arr["registros"], $maquinaria_item);
            }

            http_response_code(200);
            echo json_encode($maquinarias_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Maquinarias no encontradas"]);
        }
    }

    // Actualizar maquinaria verificando que exista y que el establecimiento exista.
    public function update() {
        $data = $this->getPayload();
        $id_maquinaria = $this->getInputValue($data, ["id_maquinaria"]);
        $nombre = $this->getInputValue($data, ["nombre"]);
        $en_uso = $this->getInputValue($data, ["en_uso"]);
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);

        if (!empty($id_maquinaria) && !empty($nombre) && !empty($en_uso) && !empty($id_establcmto)) {
            $checkEst = $this->db->prepare("SELECT id_establcmto FROM establecimiento WHERE id_establcmto = ?");
            $checkEst->bind_param("i", $id_establcmto);
            $checkEst->execute();
            $resultEst = $checkEst->get_result();
            $checkEst->close();

            if ($resultEst->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "El establecimiento no existe"]);
                return;
            }

            $this->maquinaria->id_maquinaria = (int) $id_maquinaria;
            $this->maquinaria->nombre = $nombre;
            $this->maquinaria->en_uso = $en_uso;
            $this->maquinaria->id_establcmto = (int) $id_establcmto;

            $result = $this->maquinaria->readOne();
            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Maquinaria no encontrada"]);
                return;
            }

            if ($this->maquinaria->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Maquinaria actualizada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Maquinaria no actualizada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Eliminar maquinaria verificando que exista.
    public function delete() {
        $data = $this->getPayload();
        $id_maquinaria = $this->getInputValue($data, ["id_maquinaria"]);

        if (!empty($id_maquinaria)) {
            $this->maquinaria->id_maquinaria = (int) $id_maquinaria;
            $result = $this->maquinaria->readOne();

            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Maquinaria no encontrada"]);
                return;
            }

            if ($this->maquinaria->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Maquinaria eliminada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Maquinaria no eliminada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
