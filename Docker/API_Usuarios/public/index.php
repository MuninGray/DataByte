<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once "../src/UsuarioController.php";

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";
$controller = new UsuarioController();

switch ($method) {
    case "GET":
        $controller->read();
        break;
    case "POST":
        $accion = $_GET["accion"] ?? "";

        if ($accion == "login") {
            $controller->login();
        } elseif ($accion == "asignarRol") {
            $controller->asignarRol();
        } elseif ($accion == "rechazarUsuario") {
            $controller->rechazarUsuario();
        } else {
            $controller->create();
        }
        break;
    case "PUT":
        $controller->update();
        break;
    case "DELETE":
        $controller->delete();
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}