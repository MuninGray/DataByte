<?php
//debemos tener la base de datos y el producto
require_once "../config/database.php";
require_once "Usuario.php";

class UsuarioController {
    private $db;
    private $usuario;

    public function __construct() {
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
        $email = $this->getInputValue($data, ["correo", "email"]);
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);
        $pass = $this->getInputValue($data, ["contrasena", "pass"]);
        $confPass = $this->getInputValue($data, ["conf_contrasena", "confirmar_contrasena"]);
        $prNom = $this->getInputValue($data, ["PrNom", "prNom"]);
        $prApel = $this->getInputValue($data, ["PrApel", "prApel"]);
        $rol = $this->getInputValue($data, ["rol"]);

        if (!empty($email) && !empty($cedula) && !empty($pass) && !empty($confPass)) {
            if ($pass !== $confPass) {
                http_response_code(400);
                echo json_encode([
                    "message" => "Las contraseñas no coinciden"
                ]);
                return;
            }

            $this->usuario->email = $email;
            $this->usuario->cedula = (int) $cedula;
            $this->usuario->pass = $pass;
            $this->usuario->PrNom = $prNom;
            $this->usuario->PrApel = $prApel;
            $this->usuario->rol = !empty($rol) ? $rol : "usuario";

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
                    "email" => $row['email'],
                    "cedula" => $row['cedula'],
                    "PrNom" => $row['PrNom'],
                    "PrApel" => $row['PrApel'],
                    "rol" => $row['rol'],
                    "estado_habil" => $row['estado_habil']
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
        $email = $this->getInputValue($data, ["correo", "email"]);
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);
        $pass = $this->getInputValue($data, ["contrasena", "pass"]);
        $prNom = $this->getInputValue($data, ["PrNom", "prNom"]);
        $prApel = $this->getInputValue($data, ["PrApel", "prApel"]);
        $rol = $this->getInputValue($data, ["rol"]);

        if (!empty($email) && !empty($cedula) && !empty($pass)) {
            $this->usuario->email = $email;
            $this->usuario->cedula = (int) $cedula;
            $this->usuario->pass = $pass;
            $this->usuario->PrNom = $prNom;
            $this->usuario->PrApel = $prApel;
            $this->usuario->rol = !empty($rol) ? $rol : "usuario";

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
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);

        if (!empty($cedula)) {
            $this->usuario->cedula = (int) $cedula;

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
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);
        $pass = $this->getInputValue($data, ["contrasena", "pass"]);

        if (!empty($cedula) && !empty($pass)) {
            $this->usuario->cedula = (int) $cedula;
            $this->usuario->pass = $pass;

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
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);
        $rol = $this->getInputValue($data, ["rol"]);
        $codigo = $this->getInputValue($data, ["codigo"]);
        $nom_cuadrilla = $this->getInputValue($data, ["nom_cuadrilla"]);
        $id_establcmto = $this->getInputValue($data, ["id_establcmto"]);

        if (!empty($cedula) && !empty($rol)) {
            $this->usuario->cedula = (int) $cedula;
            $this->usuario->rol = $rol;
            $this->usuario->codigo = $codigo;
            $this->usuario->nom_cuadrilla = $nom_cuadrilla;
            $this->usuario->id_establcmto = (int) $id_establcmto;

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
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);

        if (!empty($cedula)) {
            $this->usuario->cedula = (int) $cedula;

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

