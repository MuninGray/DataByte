<?php

$correo = isset($_POST['correo']) ? $_POST['correo'] : '';
$ci = isset($_POST['CI']) ? $_POST['CI'] : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
$conf_contrasena = isset($_POST['conf_contrasena']) ? $_POST['conf_contrasena'] : '';

$conn = new mysqli('localhost', 'root', '', 'proyectito');
if ($conn->connect_error) {
	die('Connection failed: ' . $conn->connect_error);
}

// Validar campos y confirmacion de contrasena
if (empty($correo) || empty($ci) || empty($contrasena)) {
	http_response_code(400);
	echo json_encode(["message" => "Datos incompletos"]);
	exit;
}

if ($contrasena !== $conf_contrasena) {
	http_response_code(400);
	echo json_encode(["message" => "Las contrasenas no coinciden"]);
	exit;
}

$stmt = $conn->prepare("INSERT INTO usuarios (correo, ci, contrasena) VALUES (?, ?, ?)");
if ($stmt) {
	$stmt->bind_param('sss', $correo, $ci, $contrasena);
	$stmt->execute();
	$stmt->close();
}

$conn->close();