<?php
session_start();
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
        $accion = $_GET["accion"] ?? "";

        if ($accion === "usuarioActual") {
            $controller->usuarioActual();
        } else {
            $controller->read();
        }
        break;
    case "POST":
        $accion = $_GET["accion"] ?? "";

        if ($accion === "cerrarSesion") {
            $controller->cerrarSesion();
        } elseif ($accion == "login") {
            $controller->login();
        } elseif ($accion == "asignarRol") {
            $controller->asignarRol();
        } elseif ($accion == "rechazarUsuario") {
            $controller->rechazarUsuario();
        } elseif ($accion == "crearAdminTecnico") {
            $controller->crearAdminTecnico();
        } elseif ($accion == "usuarioActual") {
            $controller->usuarioActual();
        } else {
            $controller->create();
        }
        break;
    case "PUT":
        if (($_GET["accion"] ?? "") === "miPerfil") {
            $controller->updateMiPerfil();
        } else {
            $controller->update();
        }
        break;
    case "DELETE":
        $controller->delete();
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}
