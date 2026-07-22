<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../config/database.php";
require_once "../src/RutaController.php";
require_once "../src/VehiculoController.php";
require_once "../src/CuadrillaController.php";
require_once "../src/IncidenciaController.php";
require_once "../src/DescargaController.php";

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";
$accion = $_GET["accion"] ?? "";

$controller = null;

switch ($accion) {
    case "rutas":
        $controller = new RutaController();
        break;
    case "vehiculos":
        $controller = new VehiculoController();
        break;
    case "cuadrillas":
        $controller = new CuadrillaController();
        break;
    case "incidencias":
        $controller = new IncidenciaController();
        break;
    case "descargas":
        $controller = new DescargaController();
        break;
    case "asignarRuta":
        $controller = new RutaController();
        break;
    case "obtenerAsignaciones":
        $controller = new RutaController();
        break;
    default:
        http_response_code(400);
        echo json_encode(["message" => "Acción no válida"]);
        exit;
}

switch ($method) {
    case "GET":
        if ($accion == "vehiculos") {
            $controller->read();
        } elseif ($accion == "rutas") {
            $controller->read();
        } elseif ($accion == "cuadrillas") {
            $controller->read();
        } elseif ($accion == "incidencias") {
            $controller->read();
        } elseif ($accion == "descargas") {
            $controller->read();
        } elseif ($accion == "obtenerAsignaciones") {
            $controller->obtenerAsignaciones();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Acción no válida"]);
        }
        break;
    case "POST":
        if ($accion == "vehiculos") {
            $controller->create();
        } elseif ($accion == "rutas") {
            $controller->create();
        } elseif ($accion == "cuadrillas") {
            $controller->create();
        } elseif ($accion == "incidencias") {
            $controller->create();
        } elseif ($accion == "descargas") {
            $controller->create();
        } elseif ($accion == "asignarRuta") {
            $controller->asignarRuta();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Acción no válida"]);
        }
        break;
    case "PUT":
        if ($accion == "vehiculos") {
            $controller->update();
        } elseif ($accion == "rutas") {
            $controller->update();
        } elseif ($accion == "cuadrillas") {
            $controller->update();
        } elseif ($accion == "incidencias") {
            $controller->update();
        } elseif ($accion == "descargas") {
            $controller->update();
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Acción no válida"]);
        }
        break;
    case "DELETE":
        if ($accion == "vehiculos") {
            $controller->delete();
        } elseif ($accion == "rutas") {
            $controller->delete();
        } elseif ($accion == "cuadrillas") {
            $controller->delete();
        } elseif ($accion == "incidencias") {
            $controller->delete();
        } elseif ($accion == "descargas") {
            $controller->delete();
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
