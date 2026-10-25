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

    private function datosEstablecimientoValidos($nombre, $calle, $nmro, $esq, $tipo, $actual, $maximo, $tipoRes, $lat, $lng) {
        $numero = filter_var($nmro, FILTER_VALIDATE_INT);
        $coordsVacias = $lat === "" && $lng === "";
        $coordsValidas = $lat !== "" && $lng !== "" && is_numeric($lat) && is_numeric($lng)
            && (float)$lat >= -90 && (float)$lat <= 90 && (float)$lng >= -180 && (float)$lng <= 180;
        return $nombre !== "" && strlen($nombre) <= 50 && $calle !== "" && strlen($calle) <= 100
            && $numero !== false && $numero > 0 && $esq !== "" && strlen($esq) <= 100
            && in_array($tipo, ["vertedero", "centro de acopio"], true)
            && is_numeric($actual) && (float)$actual >= 0 && is_numeric($maximo) && (float)$maximo > 0
            && (float)$actual <= (float)$maximo && $tipoRes !== "" && strlen($tipoRes) <= 50
            && ($coordsVacias || $coordsValidas);
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

    // Crear establecimiento.
    public function create() {
        $data = $this->getPayload();
        $nombre = $this->getInputValue($data, ["nombre"]);
        $calle = $this->getInputValue($data, ["calle"]);
        $nmro = $this->getInputValue($data, ["nmro"]);
        $esq = $this->getInputValue($data, ["esq"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $capac_actual = $this->getInputValue($data, ["capac_actual"]);
        $capac_max = $this->getInputValue($data, ["capac_max"]);
        $tipo_res = $this->getInputValue($data, ["tipo_res"]);
        $lat = $this->getInputValue($data, ["lat", "latitude"]);
        $lng = $this->getInputValue($data, ["lng", "longitude"]);

        if ($this->datosEstablecimientoValidos($nombre, $calle, $nmro, $esq, $tipo, $capac_actual, $capac_max, $tipo_res, $lat, $lng)) {
            $this->establecimiento->nombre = $nombre;
            $this->establecimiento->calle = $calle;
            $this->establecimiento->nmro = $nmro;
            $this->establecimiento->esq = $esq;
            $this->establecimiento->tipo = $tipo;
            $this->establecimiento->capac_actual = $capac_actual;
            $this->establecimiento->capac_max = $capac_max;
            $this->establecimiento->tipo_res = $tipo_res;
            $this->establecimiento->lat = $lat !== "" ? floatval($lat) : null;
            $this->establecimiento->lng = $lng !== "" ? floatval($lng) : null;

            if ($this->establecimiento->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Establecimiento creado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Establecimiento no creado", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
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
                    "tipo_res" => $row["tipo_res"],
                    "lat" => $row["lat"],
                    "lng" => $row["lng"]
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
                    "tipo_res" => $row["tipo_res"],
                    "lat" => $row["lat"],
                    "lng" => $row["lng"]
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
        $lat = $this->getInputValue($data, ["lat", "latitude"]);
        $lng = $this->getInputValue($data, ["lng", "longitude"]);

        if (filter_var($id_establcmto, FILTER_VALIDATE_INT) !== false && (int)$id_establcmto > 0 && $this->datosEstablecimientoValidos($nombre, $calle, $nmro, $esq, $tipo, $capac_actual, $capac_max, $tipo_res, $lat, $lng)) {
            $this->establecimiento->id_establcmto = (int) $id_establcmto;
            $this->establecimiento->nombre = $nombre;
            $this->establecimiento->calle = $calle;
            $this->establecimiento->nmro = $nmro;
            $this->establecimiento->esq = $esq;
            $this->establecimiento->tipo = $tipo;
            $this->establecimiento->capac_actual = $capac_actual;
            $this->establecimiento->capac_max = $capac_max;
            $this->establecimiento->tipo_res = $tipo_res;
            $this->establecimiento->lat = $lat !== "" ? floatval($lat) : null;
            $this->establecimiento->lng = $lng !== "" ? floatval($lng) : null;

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
                $message = $this->getDbErrorMessage("Establecimiento no actualizado", "actualizar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
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
                $message = $this->getDbErrorMessage("Establecimiento no eliminado", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
