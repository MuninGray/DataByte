<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../sc/UsuarioController.php";

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