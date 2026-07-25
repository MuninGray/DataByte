<?php

class Usuario {

    private $conn;
    private $table_name = "usuario";

    public $cedula;
    public $email;
    public $pass;
    public $estado_habil;
    public $PrNom;
    public $PrApel;
    public $rol;
    public $cedula_admin;
    public $codigo;
    public $nom_cuadrilla;
    public $id_establcmto;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO `" . $this->table_name . "` (cedula, email, pass, estado_habil, PrNom, PrApel, rol, cedula_admin) VALUES (?, ?, ?, ?, ?, ?, ?, NULL)";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->cedula = (int) $this->cedula;
        $this->email = htmlspecialchars(strip_tags(trim($this->email)));
        $this->pass = htmlspecialchars(strip_tags(trim($this->pass)));
        $this->PrNom = htmlspecialchars(strip_tags(trim($this->PrNom ?? "")));
        $this->PrApel = htmlspecialchars(strip_tags(trim($this->PrApel ?? "")));
        $this->rol = htmlspecialchars(strip_tags(trim($this->rol ?? "usuario")));
        $this->estado_habil = htmlspecialchars(strip_tags(trim($this->estado_habil ?? "pendiente")));
        if ($this->rol === "") {
            $this->rol = "usuario";
        }
        if ($this->estado_habil === "") {
            $this->estado_habil = "pendiente";
        }

        $stmt->bind_param("issssss", $this->cedula, $this->email, $this->pass, $this->estado_habil, $this->PrNom, $this->PrApel, $this->rol);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT cedula, email, PrNom, PrApel, rol, estado_habil, cedula_admin FROM `" . $this->table_name . "`";
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
        $this->email = htmlspecialchars(strip_tags(trim($this->email)));
        $this->pass = htmlspecialchars(strip_tags(trim($this->pass ?? "")));
        $this->PrNom = htmlspecialchars(strip_tags(trim($this->PrNom ?? "")));
        $this->PrApel = htmlspecialchars(strip_tags(trim($this->PrApel ?? "")));
        $this->rol = htmlspecialchars(strip_tags(trim($this->rol ?? "usuario")));
        $this->estado_habil = htmlspecialchars(strip_tags(trim($this->estado_habil ?? "pendiente")));
        $this->cedula = (int) $this->cedula;

        if ($this->rol === "") {
            $this->rol = "usuario";
        }
        if ($this->estado_habil === "") {
            $this->estado_habil = "pendiente";
        }

        if ($this->pass !== "") {
            // Se envió una contraseña nueva: se actualiza también
            $query = "UPDATE `" . $this->table_name . "` SET email = ?, pass = ?, PrNom = ?, PrApel = ?, rol = ?, estado_habil = ? WHERE cedula = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) return false;
            $stmt->bind_param("ssssssi", $this->email, $this->pass, $this->PrNom, $this->PrApel, $this->rol, $this->estado_habil, $this->cedula);
        } else {
            // No se envió contraseña: se conserva la que ya tenía el usuario
            $query = "UPDATE `" . $this->table_name . "` SET email = ?, PrNom = ?, PrApel = ?, rol = ?, estado_habil = ? WHERE cedula = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) return false;
            $stmt->bind_param("sssssi", $this->email, $this->PrNom, $this->PrApel, $this->rol, $this->estado_habil, $this->cedula);
        }

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM `" . $this->table_name . "` WHERE cedula = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->cedula = (int) $this->cedula;
        $stmt->bind_param("i", $this->cedula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }

    public function login() {
        $query = "SELECT cedula, email, pass, PrNom, PrApel, rol, estado_habil FROM `" . $this->table_name . "` WHERE cedula = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) return false;

        $this->cedula = (int) $this->cedula;
        $this->pass = htmlspecialchars(strip_tags(trim($this->pass)));

        $stmt->bind_param("i", $this->cedula);
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

        if ($usuario["pass"] !== $this->pass) {
            return [
                "success" => false,
                "message" => "Contraseña incorrecta",
                "usuario" => null
            ];
        }

        if ($usuario["estado_habil"] === "pendiente") {
            return [
                "success" => false,
                "message" => "La cuenta aún no fue aprobada por un administrador.",
                "usuario" => null
            ];
        }

        if ($usuario["estado_habil"] === "rechazado") {
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
                "cedula" => $usuario["cedula"],
                "email" => $usuario["email"],
                "PrNom" => $usuario["PrNom"],
                "PrApel" => $usuario["PrApel"],
                "rol" => $usuario["rol"],
                "estado_habil" => $usuario["estado_habil"]
            ]
        ];
    }

    public function asignarRol() {
        $this->cedula = (int) $this->cedula;
        $this->rol = strtoupper(htmlspecialchars(strip_tags(trim($this->rol ?? ""))));
        $this->codigo = htmlspecialchars(strip_tags(trim($this->codigo ?? "")));
        $this->nom_cuadrilla = htmlspecialchars(strip_tags(trim($this->nom_cuadrilla ?? "")));
        $this->id_establcmto = (int) ($this->id_establcmto ?? 0);

        $this->conn->begin_transaction();

        $query = "UPDATE `" . $this->table_name . "` SET rol = ?, estado_habil = 'aprobado' WHERE cedula = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->conn->rollback();
            return false;
        }

        $stmt->bind_param("si", $this->rol, $this->cedula);

        if (!$stmt->execute()) {
            $stmt->close();
            $this->conn->rollback();
            return false;
        }

        $stmt->close();

        $insertQuery = null;
        $insertParams = [];
        $insertTypes = "";

        if ($this->rol === "INSPECTOR") {
            if ($this->codigo === "") {
                $this->conn->rollback();
                return false;
            }
            $insertQuery = "INSERT INTO `inspector_municipal` (cedula, codigo) VALUES (?, ?)";
            $insertTypes = "is";
            $insertParams = [$this->cedula, $this->codigo];
        } elseif ($this->rol === "OPERARIO_CUADRILLA") {
            if ($this->nom_cuadrilla === "") {
                $this->conn->rollback();
                return false;
            }
            $insertQuery = "INSERT INTO `operario_cuadrilla` (cedula, nom_cuadrilla) VALUES (?, ?)";
            $insertTypes = "is";
            $insertParams = [$this->cedula, $this->nom_cuadrilla];
        } elseif ($this->rol === "OPERARIO_ESTABLECIMIENTO") {
            if ($this->id_establcmto <= 0) {
                $this->conn->rollback();
                return false;
            }
            $insertQuery = "INSERT INTO `operario_establcmto` (cedula, id_establcmto) VALUES (?, ?)";
            $insertTypes = "ii";
            $insertParams = [$this->cedula, $this->id_establcmto];
        }

        if ($insertQuery !== null) {
            $insertStmt = $this->conn->prepare($insertQuery);
            if (!$insertStmt) {
                $this->conn->rollback();
                return false;
            }

            if ($insertTypes === "is") {
                $insertStmt->bind_param($insertTypes, $insertParams[0], $insertParams[1]);
            } else {
                $insertStmt->bind_param($insertTypes, $insertParams[0], $insertParams[1]);
            }

            if (!$insertStmt->execute()) {
                $insertStmt->close();
                $this->conn->rollback();
                return false;
            }

            $insertStmt->close();
        }

        if (!$this->conn->commit()) {
            $this->conn->rollback();
            return false;
        }

        return true;
    }

    public function rechazarUsuario() {
        $query = "UPDATE `" . $this->table_name . "` SET estado_habil = 'rechazado' WHERE cedula = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->cedula = (int) $this->cedula;
        $stmt->bind_param("i", $this->cedula);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        return false;
    }
}