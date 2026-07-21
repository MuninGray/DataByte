<?php
//debemos tener la base de datos y el producto
require_once "../config/database.php";
require_once "Usuario.php";

class UsuarioController{
    private $db;
    private $usuario;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
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

    // Crear usuario
    public function create(){
        $data = $this->getPayload();
        $correo = $this->getInputValue($data, ["correo"]);
        $ci = $this->getInputValue($data, ["CI", "ci"]);
        $contrasena = $this->getInputValue($data, ["contrasena"]);
        
        if (!empty($correo) && !empty($ci) && !empty($contrasena)) {
            $this->usuario->correo = $correo;
            $this->usuario->ci = $ci;
            $this->usuario->contrasena = $contrasena;
            
            if ($this->usuario->create()) {
                http_response_code(201);
                echo json_encode(["message"=> "Usuario creado exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message"=> "Usuario no creado"]);
            }
            
        } else {
            http_response_code(400);
            echo json_encode(["message"=> "Datos incompletos"]);
        }
    }

    public function read(){
        $result = $this->usuario->read();

        if ($result && $result->num_rows > 0) {
            $users_arr = [];
            $users_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $user_item = [
                    "correo" => $row['correo'],
                    "CI" => $row['ci']
                ];
                array_push($users_arr["registros"], $user_item);
            }

            http_response_code(200);
            echo json_encode($users_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No se encontraron usuarios"]);
        }
    }

    public function update(){
        $data = $this->getPayload();
        $correo = $this->getInputValue($data, ["correo"]);
        $ci = $this->getInputValue($data, ["CI", "ci"]);
        $contrasena = $this->getInputValue($data, ["contrasena"]);
        
        if (!empty($correo) && !empty($ci) && !empty($contrasena)) {
            $this->usuario->correo = $correo;
            $this->usuario->ci = $ci;
            $this->usuario->contrasena = $contrasena;
            
            if ($this->usuario->update()) {
                http_response_code(201);
                echo json_encode(["message"=> "Usuario actualizado exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message"=> "Usuario no actualizado"]);
            }
            
        } else {
            http_response_code(400);
            echo json_encode(["message"=> "Datos incompletos"]);
        }
    }

    public function delete(){
        $data = $this->getPayload();
        $ci = $this->getInputValue($data, ["CI", "ci"]);

        if (!empty($ci)) {
            $this->usuario->ci = $ci;

            if ($this->usuario->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Usuario eliminado exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Usuario no eliminado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function login(){
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->correo) && !empty($data->contrasena)) {

        $this->usuario->correo = $data->correo;

        $resultado = $this->usuario->login();

        if ($resultado && $resultado->num_rows > 0) {

            $usuario = $resultado->fetch_assoc();

            if ($usuario["contrasena"] == $data->contrasena) {
                http_response_code(200);
                echo json_encode([
                    "message" => "Login correcto"
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    "message" => "Contraseña incorrecta"
                ]);
            }

        } else {
            http_response_code(404);
            echo json_encode([
                "message" => "Usuario no encontrado"
            ]);
        }

    } else {
        http_response_code(400);
        echo json_encode([
            "message" => "Datos incompletos"
        ]);
    }
}
}

