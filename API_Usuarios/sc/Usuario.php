<?php

class Usuario {

    private $conn;
    private $table_name = "usuarios";

    public $correo;
    public $ci;
    public $contrasena;
    public $PrNom;
    public $PrApel;
    public $rol;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (correo, ci, contrasena, PrNom, PrApel, rol, estado) VALUES (?, ?, ?, ?, ?, NULL, 'PENDIENTE')";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->correo = htmlspecialchars(strip_tags(trim($this->correo)));
        $this->ci = htmlspecialchars(strip_tags(trim($this->ci)));
        $this->contrasena = htmlspecialchars(strip_tags(trim($this->contrasena)));
        $this->PrNom = htmlspecialchars(strip_tags(trim($this->PrNom ?? "")));
        $this->PrApel = htmlspecialchars(strip_tags(trim($this->PrApel ?? "")));

        $stmt->bind_param("sssss", $this->correo, $this->ci, $this->contrasena, $this->PrNom, $this->PrApel);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT correo, ci, PrNom, PrApel, rol, estado FROM `" . $this->table_name . "`";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        $stmt->close();
        return false;
    }

    public function update() {
        $query = "UPDATE `" . $this->table_name . "` SET correo = ?, ci = ?, contrasena = ?, PrNom = ?, PrApel = ? WHERE ci = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->correo = htmlspecialchars(strip_tags(trim($this->correo)));
        $this->ci = htmlspecialchars(strip_tags(trim($this->ci)));
        $this->contrasena = htmlspecialchars(strip_tags(trim($this->contrasena)));
        $this->PrNom = htmlspecialchars(strip_tags(trim($this->PrNom ?? "")));
        $this->PrApel = htmlspecialchars(strip_tags(trim($this->PrApel ?? "")));

        $stmt->bind_param("ssssss", $this->correo, $this->ci, $this->contrasena, $this->PrNom, $this->PrApel, $this->ci);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE ci = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->ci = htmlspecialchars(strip_tags(trim($this->ci)));
        $stmt->bind_param("s", $this->ci);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function login() {
        $query = "SELECT correo, ci, contrasena, PrNom, PrApel, rol, estado FROM `" . $this->table_name . "` WHERE correo = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $this->correo = htmlspecialchars(strip_tags(trim($this->correo)));
        $this->contrasena = htmlspecialchars(strip_tags(trim($this->contrasena)));

        $stmt->bind_param("s", $this->correo);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            return [
                "success" => false,
                "message" => "Usuario no encontrado",
                "usuario" => null
            ];
        }

        $usuario = $result->fetch_assoc();

        if ($usuario["contrasena"] !== $this->contrasena) {
            return [
                "success" => false,
                "message" => "Contraseña incorrecta",
                "usuario" => null
            ];
        }

        if ($usuario["estado"] === "PENDIENTE") {
            return [
                "success" => false,
                "message" => "La cuenta aún no fue aprobada por un administrador.",
                "usuario" => null
            ];
        }

        if ($usuario["estado"] === "RECHAZADO") {
            return [
                "success" => false,
                "message" => "La solicitud fue rechazada por el administrador.",
                "usuario" => null
            ];
        }

        return [
            "success" => true,
            "message" => "Login correcto",
            "usuario" => [
                "correo" => $usuario["correo"],
                "ci" => $usuario["ci"],
                "PrNom" => $usuario["PrNom"],
                "PrApel" => $usuario["PrApel"],
                "rol" => $usuario["rol"],
                "estado" => $usuario["estado"]
            ]
        ];
    }

    public function asignarRol() {
        $this->ci = htmlspecialchars(strip_tags(trim($this->ci)));
        $this->rol = htmlspecialchars(strip_tags(trim($this->rol)));

        $this->conn->begin_transaction();

        $query = "UPDATE `" . $this->table_name . "` SET rol = ?, estado = 'ACTIVO' WHERE ci = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->conn->rollback();
            return false;
        }

        $stmt->bind_param("ss", $this->rol, $this->ci);

        if (!$stmt->execute()) {
            $stmt->close();
            $this->conn->rollback();
            return false;
        }

        $stmt->close();

        $tabla = null;
        if ($this->rol === "INSPECTOR") {
            $tabla = "Inspector_Municipal";
        } elseif ($this->rol === "OPERARIO_CUADRILLA") {
            $tabla = "Operario_Cuadrilla";
        } elseif ($this->rol === "OPERARIO_ESTABLECIMIENTO") {
            $tabla = "Operario_Establcmto";
        }

        if ($tabla === null) {
            $this->conn->rollback();
            return false;
        }

        $insertQuery = "INSERT INTO `" . $tabla . "` (ci) VALUES (?)";
        $insertStmt = $this->conn->prepare($insertQuery);
        if (!$insertStmt) {
            $this->conn->rollback();
            return false;
        }

        $insertStmt->bind_param("s", $this->ci);

        if (!$insertStmt->execute()) {
            $insertStmt->close();
            $this->conn->rollback();
            return false;
        }

        $insertStmt->close();

        if (!$this->conn->commit()) {
            $this->conn->rollback();
            return false;
        }

        return true;
    }

    public function rechazarUsuario() {
        $query = "UPDATE `" . $this->table_name . "` SET estado = 'RECHAZADO' WHERE ci = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->ci = htmlspecialchars(strip_tags(trim($this->ci)));
        $stmt->bind_param("s", $this->ci);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}