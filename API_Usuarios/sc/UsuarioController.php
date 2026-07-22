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

    public function create() {
        $data = $this->getPayload();
        $correo = $this->getInputValue($data, ["correo"]);
        $ci = $this->getInputValue($data, ["CI", "ci"]);
        $contrasena = $this->getInputValue($data, ["contrasena"]);
        $confContrasena = $this->getInputValue($data, ["conf_contrasena"]);
        $prNom = $this->getInputValue($data, ["PrNom", "prNom"]);
        $prApel = $this->getInputValue($data, ["PrApel", "prApel"]);

        if (!empty($correo) && !empty($ci) && !empty($contrasena) && !empty($confContrasena)) {
            if ($contrasena !== $confContrasena) {
                http_response_code(400);
                echo json_encode([
                    "message" => "Las contraseñas no coinciden"
                ]);
                return;
            }

            $this->usuario->correo = $correo;
            $this->usuario->ci = $ci;
            $this->usuario->contrasena = $contrasena;
            $this->usuario->PrNom = $prNom;
            $this->usuario->PrApel = $prApel;

            if ($this->usuario->create()) {
                http_response_code(201);
                echo json_encode([
                    "message" => "Usuario creado exitosamente"
                ]);
            } else {
                http_response_code(503);
                echo json_encode([
                    "message" => "Usuario no creado"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message" => "Datos incompletos"
            ]);
        }
    }

    public function read() {
        $result = $this->usuario->read();

        if ($result && $result->num_rows > 0) {
            $users_arr = [];
            $users_arr["registros"] = [];

            while ($row = $result->fetch_assoc()) {
                $user_item = [
                    "correo" => $row['correo'],
                    "CI" => $row['ci'],
                    "PrNom" => $row['PrNom'],
                    "PrApel" => $row['PrApel'],
                    "rol" => $row['rol'],
                    "estado" => $row['estado']
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

    public function update() {
        $data = $this->getPayload();
        $correo = $this->getInputValue($data, ["correo"]);
        $ci = $this->getInputValue($data, ["CI", "ci"]);
        $contrasena = $this->getInputValue($data, ["contrasena"]);
        $prNom = $this->getInputValue($data, ["PrNom", "prNom"]);
        $prApel = $this->getInputValue($data, ["PrApel", "prApel"]);

        if (!empty($correo) && !empty($ci) && !empty($contrasena)) {
            $this->usuario->correo = $correo;
            $this->usuario->ci = $ci;
            $this->usuario->contrasena = $contrasena;
            $this->usuario->PrNom = $prNom;
            $this->usuario->PrApel = $prApel;

            if ($this->usuario->update()) {
                http_response_code(201);
                echo json_encode(["message" => "Usuario actualizado exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Usuario no actualizado"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function delete() {
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

    public function login() {
        $data = $this->getPayload();
        $correo = $this->getInputValue($data, ["correo"]);
        $contrasena = $this->getInputValue($data, ["contrasena"]);

        if (!empty($correo) && !empty($contrasena)) {
            $this->usuario->correo = $correo;
            $this->usuario->contrasena = $contrasena;

            $resultado = $this->usuario->login();

            if ($resultado["success"] === true) {
                http_response_code(200);
                echo json_encode([
                    "message" => $resultado["message"],
                    "usuario" => $resultado["usuario"]
                ]);
            } else {
                $statusCode = ($resultado["message"] === "Contraseña incorrecta") ? 401 : 403;
                http_response_code($statusCode);
                echo json_encode([
                    "message" => $resultado["message"]
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message" => "Datos incompletos"
            ]);
        }
    }

    public function asignarRol() {
        $data = $this->getPayload();
        $ci = $this->getInputValue($data, ["CI", "ci"]);
        $rol = $this->getInputValue($data, ["rol"]);

        if (!empty($ci) && !empty($rol)) {
            $this->usuario->ci = $ci;
            $this->usuario->rol = $rol;

            if ($this->usuario->asignarRol()) {
                http_response_code(200);
                echo json_encode(["message" => "Rol asignado correctamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo asignar el rol"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function rechazarUsuario() {
        $data = $this->getPayload();
        $ci = $this->getInputValue($data, ["CI", "ci"]);

        if (!empty($ci)) {
            $this->usuario->ci = $ci;

            if ($this->usuario->rechazarUsuario()) {
                http_response_code(200);
                echo json_encode(["message" => "Usuario rechazado correctamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo rechazar el usuario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }
}

