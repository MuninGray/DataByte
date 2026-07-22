<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../config/database.php";
require_once "../src/MunicipioController.php";
require_once "../src/EstablecimientoController.php";
require_once "../src/MaquinariaController.php";
require_once "../src/ContenedorController.php";

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";
$accion = $_GET["accion"] ?? "";

if (empty($accion)) {
    $accion = $_GET["resource"] ?? "";
}

switch ($accion) {
    case "municipios":
        $controller = new MunicipioController();
        break;
    case "establecimientos":
        $controller = new EstablecimientoController();
        break;
    case "maquinarias":
        $controller = new MaquinariaController();
        break;
    case "contenedores":
        $controller = new ContenedorController();
        break;
    default:
        http_response_code(400);
        echo json_encode(["message" => "Acción no válida"]);
        exit;
}

switch ($method) {
    case "GET":
        $controller->read();
        break;
    case "POST":
        $controller->create();
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
