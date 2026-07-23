<?php
require_once "../config/database.php";
require_once "Ruta.php";
require_once "Vehiculo.php";

class RutaController {
    private $db;
    private $ruta;
    private $vehiculo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ruta = new Ruta($this->db);
        $this->vehiculo = new Vehiculo($this->db);
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
        $nom = $this->getInputValue($data, ["nom"]);

        if (!empty($id_ruta) && !empty($nom)) {
            $this->ruta->id_ruta = (int) $id_ruta;
            $this->ruta->nom = $nom;

            if ($this->ruta->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Ruta creada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Ruta no creada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function read() {
        $result = $this->ruta->read();

        if ($result && $result->num_rows > 0) {
            $rutas_arr = [];
            $rutas_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $ruta_item = [
                    "id_ruta" => $row["id_ruta"],
                    "nom" => $row["nom"]
                ];
                array_push($rutas_arr["registros"], $ruta_item);
            }

            http_response_code(200);
            echo json_encode($rutas_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Rutas no encontradas"]);
        }
    }

    public function update() {
        $data = $this->getPayload();
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $nom = $this->getInputValue($data, ["nom"]);

        if (!empty($id_ruta) && !empty($nom)) {
            $this->ruta->id_ruta = (int) $id_ruta;
            $this->ruta->nom = $nom;

            if ($this->ruta->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Ruta actualizada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Ruta no actualizada"]);
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
            $this->ruta->id_ruta = (int) $id_ruta;

            if ($this->ruta->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Ruta eliminada exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Ruta no eliminada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID Ruta requerido"]);
        }
    }

    public function asignarRuta() {
        $data = $this->getPayload();
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $matricula = $this->getInputValue($data, ["matricula"]);
        $fech = $this->getInputValue($data, ["fech", "fecha"]);

        if (!empty($id_ruta) && !empty($matricula) && !empty($fech)) {
            $id_ruta_int = (int) $id_ruta;

            $queryRuta = "SELECT id_ruta FROM `ruta` WHERE id_ruta = ?";
            $stmtRuta = $this->db->prepare($queryRuta);
            $stmtRuta->bind_param("i", $id_ruta_int);
            $stmtRuta->execute();
            $resultRuta = $stmtRuta->get_result();
            $stmtRuta->close();

            if ($resultRuta->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "La ruta no existe"]);
                return;
            }

            $queryVehiculo = "SELECT matricula FROM `vehiculo` WHERE matricula = ?";
            $stmtVehiculo = $this->db->prepare($queryVehiculo);
            $stmtVehiculo->bind_param("s", $matricula);
            $stmtVehiculo->execute();
            $resultVehiculo = $stmtVehiculo->get_result();
            $stmtVehiculo->close();

            if ($resultVehiculo->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "El vehículo no existe"]);
                return;
            }

            $queryInsert = "INSERT INTO `asigna` (id_ruta, matricula, fech) VALUES (?, ?, ?)";
            $stmtInsert = $this->db->prepare($queryInsert);
            $stmtInsert->bind_param("iss", $id_ruta_int, $matricula, $fech);

            if ($stmtInsert->execute()) {
                $stmtInsert->close();
                http_response_code(201);
                echo json_encode(["message" => "Asignación creada exitosamente"]);
            } else {
                $stmtInsert->close();
                http_response_code(503);
                echo json_encode(["message" => "Asignación no creada"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function obtenerAsignaciones() {
        $query = "SELECT id_ruta, matricula, fech FROM `asigna`";
        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            http_response_code(503);
            echo json_encode(["message" => "No se pudieron obtener las asignaciones"]);
            return;
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();

            if ($result && $result->num_rows > 0) {
                $asignaciones_arr = [];
                $asignaciones_arr["registros"] = [];

                while ($row = $result->fetch_assoc()) {
                    $asignacion_item = [
                        "id_ruta" => $row["id_ruta"],
                        "matricula" => $row["matricula"],
                        "fech" => $row["fech"]
                    ];
                    array_push($asignaciones_arr["registros"], $asignacion_item);
                }

                http_response_code(200);
                echo json_encode($asignaciones_arr);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "No se encontraron asignaciones"]);
            }
        } else {
            $stmt->close();
            http_response_code(503);
            echo json_encode(["message" => "No se pudieron obtener las asignaciones"]);
        }
    }
}
