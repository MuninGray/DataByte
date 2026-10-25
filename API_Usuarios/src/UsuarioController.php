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

    private function getAdminSecret() {
        $envFile = __DIR__ . '/../../.env';
        $secret = getenv("DATA_BYTE_ADMIN_SECRET");

        if (file_exists($envFile)) {
            $parsed = parse_ini_file($envFile, true, INI_SCANNER_RAW);
            if (is_array($parsed) && isset($parsed['DATA_BYTE_ADMIN_SECRET'])) {
                $secret = (string) $parsed['DATA_BYTE_ADMIN_SECRET'];
            } elseif (is_array($parsed) && isset($parsed['DATA_BYTE_ADMIN_SECRET'][0])) {
                $secret = (string) $parsed['DATA_BYTE_ADMIN_SECRET'][0];
            }
        }

        if ($secret === false || $secret === null) {
            return "";
        }

        $secret = trim((string) $secret);
        if (strlen($secret) >= 2 && $secret[0] === '"' && substr($secret, -1) === '"') {
            $secret = substr($secret, 1, -1);
        }

        return $secret;
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

    private function existeUsuarioDuplicado($cedula, $email) {
        $cedula = (int) preg_replace('/\D+/', '', (string) $cedula);
        $email = trim((string) $email);

        if ($cedula <= 0 && $email === "") {
            return false;
        }

        $query = "SELECT cedula FROM `Usuario` WHERE";
        $params = [];
        $types = "";
        $conditions = [];

        if ($cedula > 0) {
            $conditions[] = "cedula = ?";
            $params[] = $cedula;
            $types .= "i";
        }

        if ($email !== "") {
            $conditions[] = "email = ?";
            $params[] = strtolower($email);
            $types .= "s";
        }

        $query .= " " . implode(" OR ", $conditions);

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            return false;
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result && $result->num_rows > 0;
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
        $estadoHabil = $this->getInputValue($data, ["estado_habil"]);
        $cedulaAdmin = $this->getInputValue($data, ["cedula_admin"]);
        $adminSecret = $this->getAdminSecret();
        $providedAdminSecret = $this->getInputValue($data, ["admin_secret"]);

        $rolNormalizada = strtoupper($rol);
        if ($rolNormalizada === "ADMIN") {
            if ($adminSecret === "" || $providedAdminSecret !== $adminSecret) {
                http_response_code(403);
                echo json_encode([
                    "message" => "La creación de administradores está protegida y requiere un secreto válido."
                ]);
                return;
            }

            if (empty($cedulaAdmin) || (int) $cedulaAdmin <= 0) {
                http_response_code(400);
                echo json_encode([
                    "message" => "Para crear un administrador debes enviar cedula_admin válida."
                ]);
                return;
            }
        }

        $requiredFields = [
            "correo" => $email,
            "CI" => $cedula,
            "PrNom" => $prNom,
            "PrApel" => $prApel,
            "contrasena" => $pass,
            "conf_contrasena" => $confPass
        ];

        $missing = [];
        foreach ($requiredFields as $field => $value) {
            if (trim((string) $value) === "") {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode([
                "message" => "Faltan datos obligatorios: " . implode(", ", $missing)
            ]);
            return;
        }

        $cedulaDigits = preg_replace('/\D+/', '', $cedula);
        if (strlen($cedulaDigits) < 8) {
            http_response_code(400);
            echo json_encode([
                "message" => "La cédula debe tener al menos 8 dígitos."
            ]);
            return;
        }

        if ($this->existeUsuarioDuplicado($cedula, $email)) {
            http_response_code(409);
            echo json_encode([
                "message" => "Ya existe un usuario con esa cédula o ese correo."
            ]);
            return;
        }

        if (strlen($prNom) < 2 || strlen($prApel) < 2) {
            http_response_code(400);
            echo json_encode([
                "message" => "El nombre y el apellido deben tener al menos 2 caracteres cada uno."
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "message" => "El correo ingresado no es válido"
            ]);
            return;
        }

        if (strlen($pass) < 6) {
            http_response_code(400);
            echo json_encode([
                "message" => "La contraseña debe tener al menos 6 caracteres"
            ]);
            return;
        }

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
        $this->usuario->rol = !empty($rol) ? strtoupper($rol) : "USUARIO";
        $this->usuario->estado_habil = !empty($estadoHabil) ? $estadoHabil : "pendiente";
        $this->usuario->cedula_admin = !empty($cedulaAdmin) ? (int) $cedulaAdmin : 0;

        if ($this->usuario->create()) {
            http_response_code(201);
            echo json_encode([
                "message" => "Usuario creado exitosamente"
            ]);
        } else {
            $message = $this->getDbErrorMessage("Usuario no creado", "crear");
            http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
            echo json_encode([
                "message" => $message,
                "detail" => $this->db->error ?? ""
            ]);
        }
    }

    public function crearAdminTecnico() {
        $data = $this->getPayload();
        $email = $this->getInputValue($data, ["correo", "email"]);
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);
        $pass = $this->getInputValue($data, ["contrasena", "pass"]);
        $confPass = $this->getInputValue($data, ["conf_contrasena", "confirmar_contrasena"]);
        $prNom = $this->getInputValue($data, ["PrNom", "prNom"]);
        $prApel = $this->getInputValue($data, ["PrApel", "prApel"]);
        $adminSecret = $this->getAdminSecret();
        $providedAdminSecret = $this->getInputValue($data, ["admin_secret"]);

        if ($adminSecret === "" || $providedAdminSecret !== $adminSecret) {
            http_response_code(403);
            echo json_encode([
                "message" => "La creación de administradores requiere el secreto del servidor."
            ]);
            return;
        }

        $sessionCedula = $_SESSION['usuario']['cedula'] ?? null;
        $sessionRol = strtoupper((string) ($_SESSION['usuario']['rol'] ?? ""));

        if (empty($sessionCedula) || (int) $sessionCedula <= 0 || $sessionRol !== "ADMIN") {
            http_response_code(403);
            echo json_encode([
                "message" => "Solo un administrador autenticado puede crear otro administrador."
            ]);
            return;
        }

        $cedulaAdmin = (string) $sessionCedula;

        $requiredFields = [
            "correo" => $email,
            "CI" => $cedula,
            "PrNom" => $prNom,
            "PrApel" => $prApel,
            "contrasena" => $pass,
            "conf_contrasena" => $confPass,
            "cedula_admin" => $cedulaAdmin
        ];

        $missing = [];
        foreach ($requiredFields as $field => $value) {
            if (trim((string) $value) === "") {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode([
                "message" => "Faltan datos obligatorios: " . implode(", ", $missing)
            ]);
            return;
        }

        $cedulaDigits = preg_replace('/\D+/', '', $cedula);
        if (strlen($cedulaDigits) < 8) {
            http_response_code(400);
            echo json_encode([
                "message" => "La cédula debe tener al menos 8 dígitos."
            ]);
            return;
        }

        if ($this->existeUsuarioDuplicado($cedula, $email)) {
            http_response_code(409);
            echo json_encode([
                "message" => "Ya existe un usuario con esa cédula o ese correo."
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "message" => "El correo ingresado no es válido"
            ]);
            return;
        }

        if (strlen($pass) < 6) {
            http_response_code(400);
            echo json_encode([
                "message" => "La contraseña debe tener al menos 6 caracteres"
            ]);
            return;
        }

        if ($pass !== $confPass) {
            http_response_code(400);
            echo json_encode([
                "message" => "Las contraseñas no coinciden"
            ]);
            return;
        }

        $this->db->begin_transaction();

        $adminHash = password_hash($pass, PASSWORD_DEFAULT);

        $adminStmt = $this->db->prepare("INSERT INTO `Admin_Tecnico` (cedula_admin, email, PrNom, PrApel, pass) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE email = VALUES(email), PrNom = VALUES(PrNom), PrApel = VALUES(PrApel), pass = VALUES(pass)");
        if (!$adminStmt) {
            $this->db->rollback();
            http_response_code(503);
            echo json_encode(["message" => "No se pudo preparar la creación del administrador técnico."]);
            return;
        }

        $adminStmt->bind_param("issss", $cedulaAdmin, $email, $prNom, $prApel, $adminHash);
        if (!$adminStmt->execute()) {
            $adminStmt->close();
            $this->db->rollback();
            http_response_code(503);
            echo json_encode(["message" => "No se pudo guardar el administrador técnico."]);
            return;
        }
        $adminStmt->close();

        $this->usuario->email = $email;
        $this->usuario->cedula = (int) $cedula;
        $this->usuario->pass = $pass;
        $this->usuario->PrNom = $prNom;
        $this->usuario->PrApel = $prApel;
        $this->usuario->rol = "ADMIN";
        $this->usuario->estado_habil = "aprobado";
        $this->usuario->cedula_admin = (int) $cedulaAdmin;

        if (!$this->usuario->create()) {
            $this->db->rollback();
            http_response_code(503);
            echo json_encode(["message" => "No se pudo crear el usuario administrador."]);
            return;
        }

        $this->db->commit();
        http_response_code(201);
        echo json_encode([
            "message" => "Administrador técnico creado correctamente",
            "cedula_admin" => (int) $cedulaAdmin,
            "cedula_usuario" => (int) $cedula,
            "rol" => "ADMIN"
        ]);
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
        $estadoHabil = $this->getInputValue($data, ["estado_habil"]);

        if (!empty($email) && !empty($cedula)) {
            $this->usuario->email = $email;
            $this->usuario->cedula = (int) $cedula;
            $this->usuario->pass = $pass;
            $this->usuario->PrNom = $prNom;
            $this->usuario->PrApel = $prApel;
            $this->usuario->rol = !empty($rol) ? strtoupper($rol) : "USUARIO";
            $this->usuario->estado_habil = !empty($estadoHabil) ? $estadoHabil : "pendiente";

            $checkAdmin = $this->db->prepare("SELECT 1 FROM `Usuario` WHERE cedula = ? AND rol = 'ADMIN' LIMIT 1");
            if ($checkAdmin) {
                $checkAdmin->bind_param("i", $this->usuario->cedula);
                $checkAdmin->execute();
                $adminResult = $checkAdmin->get_result();
                $checkAdmin->close();

                if ($adminResult && $adminResult->num_rows > 0) {
                    http_response_code(403);
                    echo json_encode(["message" => "No se puede editar un administrador del sistema."]);
                    return;
                }
            }

            if ($this->usuario->update()) {
                http_response_code(201);
                echo json_encode(["message" => "Usuario actualizado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Usuario no actualizado", "actualizar");
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
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);

        if (!empty($cedula)) {
            $this->usuario->cedula = (int) $cedula;

            $checkAdmin = $this->db->prepare("SELECT 1 FROM `Usuario` WHERE cedula = ? AND rol = 'ADMIN' LIMIT 1");
            if ($checkAdmin) {
                $checkAdmin->bind_param("i", $this->usuario->cedula);
                $checkAdmin->execute();
                $adminResult = $checkAdmin->get_result();
                $checkAdmin->close();

                if ($adminResult && $adminResult->num_rows > 0) {
                    http_response_code(403);
                    echo json_encode(["message" => "No se puede borrar un administrador del sistema."]);
                    return;
                }
            }

            if ($this->usuario->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Usuario eliminado exitosamente"]);
            } else {
                $message = $this->getDbErrorMessage("Usuario no eliminado", "eliminar");
                http_response_code(strpos(strtolower($this->db->error ?? ''), 'duplicate') !== false ? 409 : 503);
                echo json_encode(["message" => $message, "detail" => $this->db->error ?? ""]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    public function usuarioActual() {
        if (empty($_SESSION['usuario']['cedula'])) {
            http_response_code(401);
            echo json_encode(["message" => "No hay usuario autenticado"]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "message" => "Usuario autenticado",
            "usuario" => $_SESSION['usuario']
        ]);
    }

    public function updateMiPerfil() {
        if (empty($_SESSION['usuario']['cedula'])) {
            http_response_code(401);
            echo json_encode(["message" => "La sesión expiró. Iniciá sesión nuevamente."]);
            return;
        }

        $data = $this->getPayload();
        $cedula = (int) $_SESSION['usuario']['cedula'];
        $email = $this->getInputValue($data, ["email"]);
        $nombre = $this->getInputValue($data, ["PrNom"]);
        $apellido = $this->getInputValue($data, ["PrApel"]);
        $passActual = $this->getInputValue($data, ["contrasena_actual"]);
        $passNueva = $this->getInputValue($data, ["contrasena_nueva"]);
        $passConfirmacion = $this->getInputValue($data, ["confirmar_contrasena"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50 || $nombre === "" || $apellido === "" || strlen($nombre) > 50 || strlen($apellido) > 50) {
            http_response_code(400);
            echo json_encode(["message" => "Ingresá un correo válido, nombre y apellido de hasta 50 caracteres."]);
            return;
        }

        $cambiarContrasena = $passActual !== "" || $passNueva !== "" || $passConfirmacion !== "";
        if ($cambiarContrasena && ($passActual === "" || strlen($passNueva) < 8 || strlen($passNueva) > 72 || $passNueva !== $passConfirmacion)) {
            http_response_code(400);
            echo json_encode(["message" => "Para cambiar la contraseña ingresá la actual y una nueva de 8 a 72 caracteres, repetida correctamente."]);
            return;
        }

        if ($cambiarContrasena) {
            $check = $this->db->prepare("SELECT pass FROM `Usuario` WHERE cedula = ?");
            if (!$check) {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo verificar la contraseña actual."]);
                return;
            }
            $check->bind_param("i", $cedula);
            $check->execute();
            $result = $check->get_result();
            $cuenta = $result ? $result->fetch_assoc() : null;
            $check->close();
            if (!$cuenta || !password_verify($passActual, $cuenta["pass"])) {
                http_response_code(403);
                echo json_encode(["message" => "La contraseña actual no es correcta."]);
                return;
            }

            $passHash = password_hash($passNueva, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE `Usuario` SET email = ?, PrNom = ?, PrApel = ?, pass = ? WHERE cedula = ?");
            if (!$stmt) {
                http_response_code(503);
                echo json_encode(["message" => "No se pudieron guardar los cambios."]);
                return;
            }
            $stmt->bind_param("ssssi", $email, $nombre, $apellido, $passHash, $cedula);
        } else {
            $stmt = $this->db->prepare("UPDATE `Usuario` SET email = ?, PrNom = ?, PrApel = ? WHERE cedula = ?");
            if (!$stmt) {
                http_response_code(503);
                echo json_encode(["message" => "No se pudieron guardar los cambios."]);
                return;
            }
            $stmt->bind_param("sssi", $email, $nombre, $apellido, $cedula);
        }

        if (!$stmt->execute()) {
            $duplicate = stripos($stmt->error, "duplicate") !== false;
            $stmt->close();
            http_response_code($duplicate ? 409 : 503);
            echo json_encode(["message" => $duplicate ? "Ese correo ya está asociado a otra cuenta." : "No se pudieron guardar los cambios."]);
            return;
        }
        $stmt->close();

        $_SESSION['usuario']['email'] = $email;
        $_SESSION['usuario']['PrNom'] = $nombre;
        $_SESSION['usuario']['PrApel'] = $apellido;
        http_response_code(200);
        echo json_encode(["message" => "Tus datos se actualizaron correctamente.", "usuario" => $_SESSION['usuario']]);
    }

    public function cerrarSesion() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        http_response_code(200);
        echo json_encode(["message" => "Sesión cerrada"]);
    }

    public function login() {
        $data = $this->getPayload();
        $cedula = $this->getInputValue($data, ["CI", "ci", "cedula"]);
        $pass = $this->getInputValue($data, ["contrasena", "pass"]);

        if (trim((string) $cedula) === "" || trim((string) $pass) === "") {
            http_response_code(400);
            echo json_encode([
                "message" => "La cédula y la contraseña son obligatorias."
            ]);
            return;
        }

        $cedulaDigits = preg_replace('/\D+/', '', $cedula);
        if (strlen($cedulaDigits) < 8) {
            http_response_code(400);
            echo json_encode([
                "message" => "La cédula debe tener al menos 8 dígitos."
            ]);
            return;
        }

        if (strlen($pass) < 6) {
            http_response_code(400);
            echo json_encode([
                "message" => "La contraseña debe tener al menos 6 caracteres."
            ]);
            return;
        }

        $this->usuario->cedula = (int) $cedula;
        $this->usuario->pass = $pass;

        $resultado = $this->usuario->login();

        if ($resultado["success"] === true) {
            $_SESSION['usuario'] = [
                "cedula" => (int) $this->usuario->cedula,
                "email" => $resultado["usuario"]["email"] ?? "",
                "PrNom" => $resultado["usuario"]["PrNom"] ?? "",
                "PrApel" => $resultado["usuario"]["PrApel"] ?? "",
                "rol" => $resultado["usuario"]["rol"] ?? "USUARIO"
            ];

            http_response_code(200);
            echo json_encode([
                "message" => $resultado["message"],
                "usuario" => $_SESSION['usuario']
            ]);
        } else {
            $statusCode = ($resultado["message"] === "Contraseña incorrecta") ? 401 : 403;
            http_response_code($statusCode);
            echo json_encode([
                "message" => $resultado["message"]
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

        $allowedAdminRoles = ["ADMIN", "USUARIO", "INSPECTOR", "CHOFER", "PEON", "OPERARIO_ESTABLECIMIENTO"];
        $rolNormalizado = strtoupper($rol);
        if ($rol === "" || !in_array($rolNormalizado, $allowedAdminRoles, true)) {
            http_response_code(400);
            echo json_encode(["message" => "Rol no válido. Elegí un rol permitido."]);
            return;
        }

        $cedulaDigits = preg_replace('/\D+/', '', $cedula);
        if ($cedula === "" || strlen($cedulaDigits) < 8) {
            http_response_code(400);
            echo json_encode(["message" => "La cédula del usuario es obligatoria y debe tener al menos 8 dígitos."]);
            return;
        }

        if ($rolNormalizado === "INSPECTOR" && trim($codigo) === "") {
            http_response_code(400);
            echo json_encode(["message" => "Para asignar el rol de inspector es obligatorio indicar el código del municipio."]);
            return;
        }

        if (in_array($rolNormalizado, ["CHOFER", "PEON"], true) && trim($nom_cuadrilla) === "") {
            http_response_code(400);
            echo json_encode(["message" => "Para asignar este rol es obligatorio indicar la cuadrilla."]);
            return;
        }

        if ($rolNormalizado === "OPERARIO_ESTABLECIMIENTO" && ((int) $id_establcmto <= 0)) {
            http_response_code(400);
            echo json_encode(["message" => "Para este rol debe indicarse un ID de establecimiento válido."]);
            return;
        }

        $this->usuario->cedula = (int) $cedula;
        $this->usuario->rol = $rolNormalizado;
        $this->usuario->codigo = $codigo;
        $this->usuario->nom_cuadrilla = $nom_cuadrilla;
        $this->usuario->id_establcmto = (int) $id_establcmto;

        if ($this->usuario->asignarRol()) {
            http_response_code(200);
            echo json_encode(["message" => "Rol asignado correctamente"]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "No se pudo asignar el rol. Revisá los datos relacionados."]);
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

