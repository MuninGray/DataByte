<?php
require_once "../config/database.php";
require_once "Contenedor.php";

class ContenedorController {
    private $db;
    private $contenedor;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->contenedor = new Contenedor($this->db);
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

    // Crear contenedor verificando que exista el municipio.
    public function create() {
        $data = $this->getPayload();
        $estado_optivo = $this->getInputValue($data, ["estado_optivo"]);
        $en_uso = $this->getInputValue($data, ["en_uso"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $calle = $this->getInputValue($data, ["calle"]);
        $nmro = $this->getInputValue($data, ["nmro"]);
        $esq = $this->getInputValue($data, ["esq"]);
        $codigo = $this->getInputValue($data, ["codigo"]);
        $matricula = $this->getInputValue($data, ["matricula"]);
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $lat = $this->getInputValue($data, ["lat", "latitude"]);
        $lng = $this->getInputValue($data, ["lng", "longitude"]);
        $rutaValida = $id_ruta === "" || (is_numeric($id_ruta) && (int) $id_ruta > 0 && (float) $id_ruta === (float) (int) $id_ruta);
        $coordenadasVacias = $lat === "" && $lng === "";
        $coordenadasValidas = $coordenadasVacias || ($lat !== "" && $lng !== "" && is_numeric($lat) && is_numeric($lng)
            && (float) $lat >= -90 && (float) $lat <= 90 && (float) $lng >= -180 && (float) $lng <= 180);

        if ($estado_optivo !== "" && $tipo !== "" && $calle !== "" && $nmro !== "" && is_numeric($nmro) && $codigo !== "" && $coordenadasValidas && $rutaValida) {
            $check = $this->db->prepare("SELECT codigo FROM `Municipio` WHERE codigo = ?");
            $check->bind_param("s", $codigo);
            $check->execute();
            $result = $check->get_result();
            $check->close();

            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "El municipio no existe"]);
                return;
            }

            $this->contenedor->estado_optivo = $estado_optivo;
            $this->contenedor->en_uso = $en_uso !== "" ? $en_uso : null;
            $this->contenedor->tipo = $tipo;
            $this->contenedor->calle = $calle;
            $this->contenedor->nmro = $nmro;
            $this->contenedor->esq = $esq !== "" ? $esq : null;
            $this->contenedor->codigo = $codigo;
            $this->contenedor->matricula = $matricula !== "" ? $matricula : null;
            $this->contenedor->id_ruta = $id_ruta !== "" ? (int) $id_ruta : null;
            $this->contenedor->lat = $lat !== "" ? floatval($lat) : null;
            $this->contenedor->lng = $lng !== "" ? floatval($lng) : null;

            if ($this->contenedor->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Contenedor creado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Contenedor no creado", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode([
                    "message" => $message,
                    "detail" => $this->contenedor->lastError ?: $this->db->error ?: "Error desconocido"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Leer contenedores.
    public function read() {
        $id_contdor = $this->getInputValue([], ["id_contdor"]);

        if (!empty($id_contdor)) {
            $this->contenedor->id_contdor = (int) $id_contdor;
            $result = $this->contenedor->readOne();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                http_response_code(200);
                echo json_encode([
                    "id_contdor" => $row["id_contdor"],
                    "estado_optivo" => $row["estado_optivo"],
                    "en_uso" => $row["en_uso"],
                    "tipo" => $row["tipo"],
                    "calle" => $row["calle"],
                    "nmro" => $row["nmro"],
                    "esq" => $row["esq"],
                    "codigo" => $row["codigo"],
                    "matricula" => $row["matricula"],
                    "id_ruta" => $row["id_ruta"],
                    "lat" => $row["lat"],
                    "lng" => $row["lng"]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Contenedor no encontrado"]);
            }
            return;
        }

        $result = $this->contenedor->read();

        if ($result && $result->num_rows > 0) {
            $contenedores_arr = [];
            $contenedores_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $contenedor_item = [
                    "id_contdor" => $row["id_contdor"],
                    "estado_optivo" => $row["estado_optivo"],
                    "en_uso" => $row["en_uso"],
                    "tipo" => $row["tipo"],
                    "calle" => $row["calle"],
                    "nmro" => $row["nmro"],
                    "esq" => $row["esq"],
                    "codigo" => $row["codigo"],
                    "matricula" => $row["matricula"],
                    "id_ruta" => $row["id_ruta"],
                    "lat" => $row["lat"],
                    "lng" => $row["lng"]
                ];
                array_push($contenedores_arr["registros"], $contenedor_item);
            }

            http_response_code(200);
            echo json_encode($contenedores_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Contenedores no encontrados"]);
        }
    }

    // Actualizar contenedor verificando que exista y que el municipio exista.
    public function update() {
        $data = $this->getPayload();
        $id_contdor = $this->getInputValue($data, ["id_contdor"]);
        $estado_optivo = $this->getInputValue($data, ["estado_optivo"]);
        $en_uso = $this->getInputValue($data, ["en_uso"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $calle = $this->getInputValue($data, ["calle"]);
        $nmro = $this->getInputValue($data, ["nmro"]);
        $esq = $this->getInputValue($data, ["esq"]);
        $codigo = $this->getInputValue($data, ["codigo"]);
        $matricula = $this->getInputValue($data, ["matricula"]);
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $lat = $this->getInputValue($data, ["lat", "latitude"]);
        $lng = $this->getInputValue($data, ["lng", "longitude"]);
        $rutaValida = $id_ruta === "" || (is_numeric($id_ruta) && (int) $id_ruta > 0 && (float) $id_ruta === (float) (int) $id_ruta);
        $coordenadasVacias = $lat === "" && $lng === "";
        $coordenadasValidas = $coordenadasVacias || ($lat !== "" && $lng !== "" && is_numeric($lat) && is_numeric($lng)
            && (float) $lat >= -90 && (float) $lat <= 90 && (float) $lng >= -180 && (float) $lng <= 180);

        if (!empty($id_contdor) && !empty($estado_optivo) && !empty($tipo) && !empty($calle) && !empty($nmro) && !empty($codigo) && $coordenadasValidas && $rutaValida) {
            $check = $this->db->prepare("SELECT codigo FROM `Municipio` WHERE codigo = ?");
            $check->bind_param("s", $codigo);
            $check->execute();
            $result = $check->get_result();
            $check->close();

            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "El municipio no existe"]);
                return;
            }

            $this->contenedor->id_contdor = (int) $id_contdor;
            $this->contenedor->estado_optivo = $estado_optivo;
            $this->contenedor->en_uso = $en_uso !== "" ? $en_uso : null;
            $this->contenedor->tipo = $tipo;
            $this->contenedor->calle = $calle;
            $this->contenedor->nmro = $nmro;
            $this->contenedor->esq = $esq !== "" ? $esq : null;
            $this->contenedor->codigo = $codigo;
            $this->contenedor->matricula = $matricula !== "" ? $matricula : null;
            $this->contenedor->id_ruta = $id_ruta !== "" ? (int) $id_ruta : null;
            $this->contenedor->lat = $lat !== "" ? floatval($lat) : null;
            $this->contenedor->lng = $lng !== "" ? floatval($lng) : null;

            $resultExist = $this->contenedor->readOne();
            if ($resultExist && $resultExist->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Contenedor no encontrado"]);
                return;
            }

            if ($this->contenedor->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Contenedor actualizado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Contenedor no actualizado", "actualizar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode([
                    "message" => $message,
                    "detail" => $this->contenedor->lastError ?: $this->db->error ?: "Error desconocido"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // Eliminar contenedor verificando que exista.
    public function delete() {
        $data = $this->getPayload();
        $id_contdor = $this->getInputValue($data, ["id_contdor"]);

        if (!empty($id_contdor)) {
            $this->contenedor->id_contdor = (int) $id_contdor;
            $result = $this->contenedor->readOne();

            if ($result && $result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Contenedor no encontrado"]);
                return;
            }

            if ($this->contenedor->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Contenedor eliminado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Contenedor no eliminado", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 500);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
