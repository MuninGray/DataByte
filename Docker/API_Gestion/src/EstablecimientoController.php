<?php
require_once "../config/database.php";
require_once "Establecimiento.php";

class EstablecimientoController {
    private $db;
    private $establecimiento;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->establecimiento = new Establecimiento($this->db);
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

    // Crear establecimiento.
    public function create() {
        $data = $this->getPayload();
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);
        $nombre = $this->getInputValue($data, ["nombre"]);
        $calle = $this->getInputValue($data, ["calle"]);
        $nmro = $this->getInputValue($data, ["nmro"]);
        $esq = $this->getInputValue($data, ["esq"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $capac_actual = $this->getInputValue($data, ["capac_actual"]);
        $capac_max = $this->getInputValue($data, ["capac_max"]);
        $tipo_res = $this->getInputValue($data, ["tipo_res"]);

        if (!empty($id_establcmto) && !empty($nombre) && !empty($calle) && !empty($nmro) && !empty($esq) && !empty($tipo) && !empty($capac_actual) && !empty($capac_max) && !empty($tipo_res)) {
            $this->establecimiento->id_establcmto = (int) $id_establcmto;
            $this->establecimiento->nombre = $nombre;
            $this->establecimiento->calle = $calle;
            $this->establecimiento->nmro = $nmro;
            $this->establecimiento->esq = $esq;
            $this->establecimiento->tipo = $tipo;
            $this->establecimiento->capac_actual = $capac_actual;
            $this->establecimiento->capac_max = $capac_max;
            $this->establecimiento->tipo_res = $tipo_res;

            if ($this->establecimiento->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Establecimiento creado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Establecimiento no creado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Leer establecimientos.
    public function read() {
        $id_establcmto = $this->getInputValue([], ["id_establcmto"]);

        if (!empty($id_establcmto)) {
            $this->establecimiento->id_establcmto = (int) $id_establcmto;
            $result = $this->establecimiento->readOne();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                http_response_code(200);
                echo json_encode([
                    "id_establcmto" => $row["id_establcmto"],
                    "nombre" => $row["nombre"],
                    "calle" => $row["calle"],
                    "nmro" => $row["nmro"],
                    "esq" => $row["esq"],
                    "tipo" => $row["tipo"],
                    "capac_actual" => $row["capac_actual"],
                    "capac_max" => $row["capac_max"],
                    "tipo_res" => $row["tipo_res"]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Establecimiento no encontrado"]);
            }
            return;
        }

        $result = $this->establecimiento->read();

        if ($result && $result->num_rows > 0) {
            $establecimientos_arr = [];
            $establecimientos_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $establecimiento_item = [
                    "id_establcmto" => $row["id_establcmto"],
                    "nombre" => $row["nombre"],
                    "calle" => $row["calle"],
                    "nmro" => $row["nmro"],
                    "esq" => $row["esq"],
                    "tipo" => $row["tipo"],
                    "capac_actual" => $row["capac_actual"],
                    "capac_max" => $row["capac_max"],
                    "tipo_res" => $row["tipo_res"]
                ];
                array_push($establecimientos_arr["registros"], $establecimiento_item);
            }

            http_response_code(200);
            echo json_encode($establecimientos_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Establecimientos no encontrados"]);
        }
    }

    // Actualizar establecimiento.
    public function update() {
        $data = $this->getPayload();
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);
        $nombre = $this->getInputValue($data, ["nombre"]);
        $calle = $this->getInputValue($data, ["calle"]);
        $nmro = $this->getInputValue($data, ["nmro"]);
        $esq = $this->getInputValue($data, ["esq"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $capac_actual = $this->getInputValue($data, ["capac_actual"]);
        $capac_max = $this->getInputValue($data, ["capac_max"]);
        $tipo_res = $this->getInputValue($data, ["tipo_res"]);

        if (!empty($id_establcmto) && !empty($nombre) && !empty($calle) && !empty($nmro) && !empty($esq) && !empty($tipo) && !empty($capac_actual) && !empty($capac_max) && !empty($tipo_res)) {
            $this->establecimiento->id_establcmto = (int) $id_establcmto;
            $this->establecimiento->nombre = $nombre;
            $this->establecimiento->calle = $calle;
            $this->establecimiento->nmro = $nmro;
            $this->establecimiento->esq = $esq;
            $this->establecimiento->tipo = $tipo;
            $this->establecimiento->capac_actual = $capac_actual;
            $this->establecimiento->capac_max = $capac_max;
            $this->establecimiento->tipo_res = $tipo_res;

            $result = $this->establecimiento->readOne();
            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Establecimiento no encontrado"]);
                return;
            }

            if ($this->establecimiento->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Establecimiento actualizado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Establecimiento no actualizado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Eliminar establecimiento.
    public function delete() {
        $data = $this->getPayload();
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);

        if (!empty($id_establcmto)) {
            $this->establecimiento->id_establcmto = (int) $id_establcmto;
            $result = $this->establecimiento->readOne();

            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Establecimiento no encontrado"]);
                return;
            }

            if ($this->establecimiento->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Establecimiento eliminado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Establecimiento no eliminado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
