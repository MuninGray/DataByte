<?php

$correo = isset($_POST['correo']) ? $_POST['correo'] : '';
$ci = isset($_POST['CI']) ? $_POST['CI'] : '';
$contrasena = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';
$conf_contrasena = isset($_POST['conf_contraseña']) ? $_POST['conf_contraseña'] : '';

$conn = new mysqli('localhost', 'root', '', 'proyectito');
if ($conn->connect_error) {
	die('Connection failed: ' . $conn->connect_error);
}

// Validar campos y confirmación de contraseña
if (empty($correo) || empty($ci) || empty($contrasena)) {
	http_response_code(400);
	echo json_encode(["message" => "Datos incompletos"]);
	exit;
}

if ($contrasena !== $conf_contrasena) {
	http_response_code(400);
	echo json_encode(["message" => "Las contraseñas no coinciden"]);
	exit;
}

// Use prepared statement to avoid SQL injection
$stmt = $conn->prepare("INSERT INTO usuarios (correo, ci, contrasena) VALUES (?, ?, ?)");
if ($stmt) {
	$stmt->bind_param('sss', $correo, $ci, $contrasena);
	$stmt->execute();
	$stmt->close();
}

$conn->close();