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

    // Crear usuario
    public function create(){
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->correo) && !empty($data->CI) && !empty($data->contrasena)) {
            $this->usuario->correo = $data->correo;
            $this->usuario->ci = $data->CI;
            $this->usuario->contrasena = $data->contrasena;
            
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
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->correo) && !empty($data->CI) && !empty($data->contrasena)) {
            $this->usuario->correo = $data->correo;
            $this->usuario->ci = $data->CI;
            $this->usuario->contrasena = $data->contrasena;
            
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
}


