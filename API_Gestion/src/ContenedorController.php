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

    // Crear contenedor verificando que exista el municipio.
    public function create() {
        $data = $this->getPayload();
        $id_contdor = $this->getInputValue($data, ["id_contdor"]);
        $estado_optivo = $this->getInputValue($data, ["estado_optivo"]);
        $tipo = $this->getInputValue($data, ["tipo"]);
        $calle = $this->getInputValue($data, ["calle"]);
        $nmro = $this->getInputValue($data, ["nmro"]);
        $esq = $this->getInputValue($data, ["esq"]);
        $codigo = $this->getInputValue($data, ["codigo"]);
        $matricula = $this->getInputValue($data, ["matricula"]);

        if (!empty($id_contdor) && !empty($estado_optivo) && !empty($tipo) && !empty($calle) && !empty($nmro) && !empty($esq) && !empty($codigo) && !empty($matricula)) {
            $check = $this->db->prepare("SELECT codigo FROM Municipio WHERE codigo = ?");
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
            $this->contenedor->tipo = $tipo;
            $this->contenedor->calle = $calle;
            $this->contenedor->nmro = $nmro;
            $this->contenedor->esq = $esq;
            $this->contenedor->codigo = $codigo;
            $this->contenedor->matricula = $matricula;

            if ($this->contenedor->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Contenedor creado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Contenedor no creado"]);
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
                    "tipo" => $row["tipo"],
                    "calle" => $row["calle"],
                    "nmro" => $row["nmro"],
                    "esq" => $row["esq"],
                    "codigo" => $row["codigo"],
                    "matricula" => $row["matricula"]
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
                    "tipo" => $row["tipo"],
                    "calle" => $row["calle"],
                    "nmro" => $row["nmro"],
                    "esq" => $row["esq"],
                    "codigo" => $row["codigo"],
                    "matricula" => $row["matricula"]
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
        $tipo = $this->getInputValue($data, ["tipo"]);
        $calle = $this->getInputValue($data, ["calle"]);
        $nmro = $this->getInputValue($data, ["nmro"]);
        $esq = $this->getInputValue($data, ["esq"]);
        $codigo = $this->getInputValue($data, ["codigo"]);
        $matricula = $this->getInputValue($data, ["matricula"]);

        if (!empty($id_contdor) && !empty($estado_optivo) && !empty($tipo) && !empty($calle) && !empty($nmro) && !empty($esq) && !empty($codigo) && !empty($matricula)) {
            $check = $this->db->prepare("SELECT codigo FROM Municipio WHERE codigo = ?");
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
            $this->contenedor->tipo = $tipo;
            $this->contenedor->calle = $calle;
            $this->contenedor->nmro = $nmro;
            $this->contenedor->esq = $esq;
            $this->contenedor->codigo = $codigo;
            $this->contenedor->matricula = $matricula;

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
                http_response_code(500);
                echo json_encode(["message" => "Contenedor no actualizado"]);
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
                http_response_code(500);
                echo json_encode(["message" => "Contenedor no eliminado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}
