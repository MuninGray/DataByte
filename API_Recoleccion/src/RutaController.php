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
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $nom = $this->getInputValue($data, ["nom"]);
        $matricula = $this->getInputValue($data, ["matricula"]);

        if (!empty($id_ruta) && !empty($nom) && !empty($matricula)) {
            $queryVehiculo = "SELECT matricula FROM `Vehiculo` WHERE matricula = ?";
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

            $this->ruta->id_ruta = (int) $id_ruta;
            $this->ruta->nom = $nom;
            $this->ruta->matricula = $matricula;

            if ($this->ruta->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Ruta creada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Ruta no creada", "crear");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
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
                    "nom" => $row["nom"],
                    "matricula" => $row["matricula"]
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
        $matricula = $this->getInputValue($data, ["matricula"]);

        if (!empty($id_ruta) && !empty($nom) && !empty($matricula)) {
            $queryVehiculo = "SELECT matricula FROM `Vehiculo` WHERE matricula = ?";
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

            $this->ruta->id_ruta = (int) $id_ruta;
            $this->ruta->nom = $nom;
            $this->ruta->matricula = $matricula;

            if ($this->ruta->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Ruta actualizada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Ruta no actualizada", "actualizar");
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
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);

        if (!empty($id_ruta)) {
            $this->ruta->id_ruta = (int) $id_ruta;

            if ($this->ruta->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Ruta eliminada exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Ruta no eliminada", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
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

            $queryRuta = "SELECT id_ruta FROM `Ruta` WHERE id_ruta = ?";
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

            $queryVehiculo = "SELECT matricula FROM `Vehiculo` WHERE matricula = ?";
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

            $queryInsert = "INSERT INTO `Asigna` (id_ruta, matricula, fech) VALUES (?, ?, ?)";
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
        $query = "SELECT id_ruta, matricula, fech FROM `Asigna`";
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

    public function eliminarAsignacion() {
        $data = $this->getPayload();
        $id_ruta = $this->getInputValue($data, ["id_ruta"]);
        $matricula = $this->getInputValue($data, ["matricula"]);
        $fech = $this->getInputValue($data, ["fech", "fecha"]);

        if (!empty($id_ruta) && !empty($matricula) && !empty($fech)) {
            $id_ruta_int = (int) $id_ruta;

            $query = "DELETE FROM `Asigna` WHERE id_ruta = ? AND matricula = ? AND fech = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo eliminar la asignación"]);
                return;
            }

            $stmt->bind_param("iss", $id_ruta_int, $matricula, $fech);

            if ($stmt->execute()) {
                $filasAfectadas = $stmt->affected_rows;
                $stmt->close();

                if ($filasAfectadas > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Asignación eliminada exitosamente"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "La asignación no existe"]);
                }
            } else {
                $stmt->close();
                http_response_code(503);
                echo json_encode(["message" => "No se pudo eliminar la asignación"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID Ruta, matrícula y fecha requeridos"]);
        }
    }
}
