<?php
//debemos tener la base de datos y los modelos
require_once "../config/database.php";
require_once "Vehiculo.php";
require_once "Ruta.php";

class RecoleccionController{
    private $db;
    private $vehiculo;
    private $ruta;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->vehiculo = new Vehiculo($this->db);
        $this->ruta = new Ruta($this->db);
    }

    private function getPayload() {
        $rawBody = file_get_contents("php://input");
        $data = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return [];
        }

        return $data;
    }

    // ===== VEHICULO =====
    
    public function crearVehiculo(){
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->modelo) && 
            !empty($data->matricula) && 
            !empty($data->en_uso) &&
            !empty($data->estado_optimo)) {

            $this->vehiculo->modelo = $data->modelo;
            $this->vehiculo->matricula = $data->matricula;
            $this->vehiculo->en_uso = $data->en_uso;
            $this->vehiculo->estado_optimo = $data->estado_optimo;

            if ($this->vehiculo->create()) {
                http_response_code(201);
                echo json_encode([
                    "message"=> "Vehiculo creado exitosamente"
                ]);
            } else {
                http_response_code(503);
                echo json_encode([
                    "message"=> "Vehiculo no creado"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message"=> "Datos incompletos"
            ]);
        }
    }

    public function leerVehiculos(){
        $result = $this->vehiculo->read();

        if ($result && $result->num_rows > 0) {
            $vehiculos_arr = [];
            $vehiculos_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $vehiculo_item = [
                    "modelo" => $row['modelo'],
                    "matricula" => $row['matricula'],
                    "en_uso" => $row['en_uso'],
                    "estado_optimo" => $row['estado_optimo']
                ];
                array_push($vehiculos_arr["registros"], $vehiculo_item);
            }

            http_response_code(200);
            echo json_encode($vehiculos_arr);
        } else {
            http_response_code(404);
            echo json_encode([
                "message"=> "Vehiculos no encontrados"
            ]);
        }
    }

    public function actualizarVehiculo(){
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->modelo) && 
            !empty($data->matricula) && 
            !empty($data->en_uso) &&
            !empty($data->estado_optimo)) {

            $this->vehiculo->modelo = $data->modelo;
            $this->vehiculo->matricula = $data->matricula;
            $this->vehiculo->en_uso = $data->en_uso;
            $this->vehiculo->estado_optimo = $data->estado_optimo;

            if ($this->vehiculo->update()) {
                http_response_code(200);
                echo json_encode([
                    "message"=> "Vehiculo actualizado exitosamente"
                ]);
            } else {
                http_response_code(503);
                echo json_encode([
                    "message"=> "Vehiculo no actualizado"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message"=> "Datos incompletos"
            ]);
        }
    }

    public function eliminarVehiculo(){
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->matricula)) {
            $this->vehiculo->matricula = $data->matricula;

            if ($this->vehiculo->delete()) {
                http_response_code(200);
                echo json_encode([
                    "message"=> "Vehiculo eliminado exitosamente"
                ]);
            } else {
                http_response_code(503);
                echo json_encode([
                    "message"=> "Vehiculo no eliminado"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message"=> "Matricula requerida"
            ]);
        }
    }

    // ===== RUTA =====
    
    public function crearRuta(){
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id_ruta) && 
            !empty($data->nom)) {

            $this->ruta->id_ruta = $data->id_ruta;
            $this->ruta->nom = $data->nom;

            if ($this->ruta->create()) {
                http_response_code(201);
                echo json_encode([
                    "message"=> "Ruta creada exitosamente"
                ]);
            } else {
                http_response_code(503);
                echo json_encode([
                    "message"=> "Ruta no creada"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message"=> "Datos incompletos"
            ]);
        }
    }

    public function leerRutas(){
        $result = $this->ruta->read();

        if ($result && $result->num_rows > 0) {
            $rutas_arr = [];
            $rutas_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $ruta_item = [
                    "id_ruta" => $row['id_ruta'],
                    "nom" => $row['nom']
                ];
                array_push($rutas_arr["registros"], $ruta_item);
            }

            http_response_code(200);
            echo json_encode($rutas_arr);
        } else {
            http_response_code(404);
            echo json_encode([
                "message"=> "Rutas no encontradas"
            ]);
        }
    }

    public function actualizarRuta(){
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id_ruta) && 
            !empty($data->nom)) {

            $this->ruta->id_ruta = $data->id_ruta;
            $this->ruta->nom = $data->nom;

            if ($this->ruta->update()) {
                http_response_code(200);
                echo json_encode([
                    "message"=> "Ruta actualizada exitosamente"
                ]);
            } else {
                http_response_code(503);
                echo json_encode([
                    "message"=> "Ruta no actualizada"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message"=> "Datos incompletos"
            ]);
        }
    }

    public function eliminarRuta(){
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id_ruta)) {
            $this->ruta->id_ruta = $data->id_ruta;

            if ($this->ruta->delete()) {
                http_response_code(200);
                echo json_encode([
                    "message"=> "Ruta eliminada exitosamente"
                ]);
            } else {
                http_response_code(503);
                echo json_encode([
                    "message"=> "Ruta no eliminada"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message"=> "ID Ruta requerido"
            ]);
        }
    }
}
