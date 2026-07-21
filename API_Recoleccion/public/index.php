<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../sc/RecoleccionController.php";

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";
$accion = $_GET["accion"] ?? "vehiculos";
$controller = new RecoleccionController();

switch ($method) {
    case "GET":
        if ($accion == "vehiculos") {
            $controller->leerVehiculos();
        } elseif ($accion == "rutas") {
            $controller->leerRutas();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Acción no válida"]);
        }
        break;
    case "POST":
        if ($accion == "vehiculos") {
            $controller->crearVehiculo();
        } elseif ($accion == "rutas") {
            $controller->crearRuta();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Acción no válida"]);
        }
        break;
    case "PUT":
        if ($accion == "vehiculos") {
            $controller->actualizarVehiculo();
        } elseif ($accion == "rutas") {
            $controller->actualizarRuta();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Acción no válida"]);
        }
        break;
    case "DELETE":
        if ($accion == "vehiculos") {
            $controller->eliminarVehiculo();
        } elseif ($accion == "rutas") {
            $controller->eliminarRuta();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Acción no válida"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}
