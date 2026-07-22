<?php
require_once "../config/database.php";
require_once "Cuadrilla.php";

class CuadrillaController {
    private $db;
    private $cuadrilla;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cuadrilla = new Cuadrilla($this->db);
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
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $cedula_inspector = $this->getInputValue($data, ["cedula_inspector"]);
        $estado = $this->getInputValue($data, ["estado"]);

        if (!empty($nom_cuadrilla) && !empty($cedula_inspector) && !empty($estado)) {
            $this->cuadrilla->nom_cuadrilla = $nom_cuadrilla;
            $this->cuadrilla->cedula_inspector = $cedula_inspector;
            $this->cuadrilla->estado = $estado;

            if ($this->cuadrilla->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Cuadrilla creada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Cuadrilla no creada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function read() {
        $result = $this->cuadrilla->read();

        if ($result && $result->num_rows > 0) {
            $cuadrillas_arr = [];
            $cuadrillas_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $cuadrilla_item = [
                    "nom_cuadrilla" => $row["nom_cuadrilla"],
                    "cedula_inspector" => $row["cedula_inspector"],
                    "estado" => $row["estado"]
                ];
                array_push($cuadrillas_arr["registros"], $cuadrilla_item);
            }

            http_response_code(200);
            echo json_encode($cuadrillas_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Cuadrillas no encontradas"]);
        }
    }

    public function update() {
        $data = $this->getPayload();
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $cedula_inspector = $this->getInputValue($data, ["cedula_inspector"]);
        $estado = $this->getInputValue($data, ["estado"]);

        if (!empty($nom_cuadrilla) && !empty($cedula_inspector) && !empty($estado)) {
            $this->cuadrilla->nom_cuadrilla = $nom_cuadrilla;
            $this->cuadrilla->cedula_inspector = $cedula_inspector;
            $this->cuadrilla->estado = $estado;

            if ($this->cuadrilla->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Cuadrilla actualizada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Cuadrilla no actualizada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function delete() {
        $data = $this->getPayload();
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);

        if (!empty($nom_cuadrilla)) {
            $this->cuadrilla->nom_cuadrilla = $nom_cuadrilla;

            if ($this->cuadrilla->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Cuadrilla eliminada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Cuadrilla no eliminada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Nombre de cuadrilla requerido"]);
        }
    }
}
