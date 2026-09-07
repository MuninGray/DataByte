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
        $this->cedula = (int) $this->cedula;
        $this->cedula_admin = (int) ($this->cedula_admin ?? 0);

        if ($this->cedula_admin <= 0) {
            $this->cedula_admin = 1;
            $adminEmail = "admin@databyte.local";
            $adminPrNom = "Admin";
            $adminPrApel = "Sistema";
            $adminPass = password_hash("admin123", PASSWORD_DEFAULT);

            $adminQuery = "INSERT INTO `admin_tecnico` (cedula_admin, email, PrNom, PrApel, pass) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE email = VALUES(email), PrNom = VALUES(PrNom), PrApel = VALUES(PrApel), pass = VALUES(pass)";
            $adminStmt = $this->conn->prepare($adminQuery);
            if (!$adminStmt) {
                return false;
            }

            $adminStmt->bind_param("issss", $this->cedula_admin, $adminEmail, $adminPrNom, $adminPrApel, $adminPass);
            if (!$adminStmt->execute()) {
                $adminStmt->close();
                return false;
            }
            $adminStmt->close();
        }

        $query = "INSERT INTO `" . $this->table_name . "` (cedula, email, pass, estado_habil, PrNom, PrApel, rol, cedula_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $this->email = htmlspecialchars(strip_tags(trim($this->email)));
        $this->pass = password_hash($this->pass, PASSWORD_DEFAULT);
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

        $stmt->bind_param("issssssi", $this->cedula, $this->email, $this->pass, $this->estado_habil, $this->PrNom, $this->PrApel, $this->rol, $this->cedula_admin);

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

        if (!empty($this->pass)) {
            $this->pass = trim($this->pass);
            $this->pass = password_hash($this->pass, PASSWORD_DEFAULT);

            $query = "UPDATE `" . $this->table_name . "` SET email = ?, pass = ?, PrNom = ?, PrApel = ?, rol = ?, estado_habil = ? WHERE cedula = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) return false;
            $stmt->bind_param("ssssssi", $this->email, $this->pass, $this->PrNom, $this->PrApel, $this->rol, $this->estado_habil, $this->cedula);
        } else {
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

        if (!password_verify($this->pass, $usuario["pass"])) {
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

            $checkMunicipio = $this->conn->prepare("SELECT codigo FROM `municipio` WHERE codigo = ?");
            if (!$checkMunicipio) {
                $this->conn->rollback();
                return false;
            }
            $checkMunicipio->bind_param("s", $this->codigo);
            $checkMunicipio->execute();
            $municipioResult = $checkMunicipio->get_result();
            $checkMunicipio->close();

            if ($municipioResult->num_rows === 0) {
                $municipioInsert = $this->conn->prepare("INSERT INTO `municipio` (codigo, nombre) VALUES (?, ?) ON DUPLICATE KEY UPDATE nombre = VALUES(nombre)");
                if (!$municipioInsert) {
                    $this->conn->rollback();
                    return false;
                }
                $nombreMunicipio = "Municipio " . $this->codigo;
                $municipioInsert->bind_param("ss", $this->codigo, $nombreMunicipio);
                if (!$municipioInsert->execute()) {
                    $municipioInsert->close();
                    $this->conn->rollback();
                    return false;
                }
                $municipioInsert->close();
            }

            $insertQuery = "INSERT INTO `inspector_municipal` (cedula, codigo) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE codigo = VALUES(codigo)";
            $insertTypes = "is";
            $insertParams = [$this->cedula, $this->codigo];
        } elseif ($this->rol === "OPERARIO_CUADRILLA") {
            if ($this->nom_cuadrilla === "") {
                $this->conn->rollback();
                return false;
            }
            $checkCuadrilla = $this->conn->prepare("SELECT nom_cuadrilla FROM `cuadrilla` WHERE nom_cuadrilla = ?");
            if (!$checkCuadrilla) {
                $this->conn->rollback();
                return false;
            }
            $checkCuadrilla->bind_param("s", $this->nom_cuadrilla);
            $checkCuadrilla->execute();
            $cuadrillaResult = $checkCuadrilla->get_result();
            $checkCuadrilla->close();
            if ($cuadrillaResult->num_rows === 0) {
                $this->conn->rollback();
                return false;
            }

            $insertQuery = "INSERT INTO `peon` (cedula, nom_cuadrilla) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE nom_cuadrilla = VALUES(nom_cuadrilla)";
            $insertTypes = "is";
            $insertParams = [$this->cedula, $this->nom_cuadrilla];
        } elseif ($this->rol === "CHOFER") {
            if ($this->nom_cuadrilla === "") {
                $this->conn->rollback();
                return false;
            }
            $checkCuadrilla = $this->conn->prepare("SELECT nom_cuadrilla FROM `cuadrilla` WHERE nom_cuadrilla = ?");
            if (!$checkCuadrilla) {
                $this->conn->rollback();
                return false;
            }
            $checkCuadrilla->bind_param("s", $this->nom_cuadrilla);
            $checkCuadrilla->execute();
            $cuadrillaResult = $checkCuadrilla->get_result();
            $checkCuadrilla->close();
            if ($cuadrillaResult->num_rows === 0) {
                $this->conn->rollback();
                return false;
            }

            $insertQuery = "INSERT INTO `chofer` (cedula, nom_cuadrilla) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE nom_cuadrilla = VALUES(nom_cuadrilla)";
            $insertTypes = "is";
            $insertParams = [$this->cedula, $this->nom_cuadrilla];
        } elseif ($this->rol === "OPERARIO_ESTABLECIMIENTO") {
            if ($this->id_establcmto <= 0) {
                $this->conn->rollback();
                return false;
            }
            $checkEst = $this->conn->prepare("SELECT id_establcmto FROM `establecimiento` WHERE id_establcmto = ?");
            if (!$checkEst) {
                $this->conn->rollback();
                return false;
            }
            $checkEst->bind_param("i", $this->id_establcmto);
            $checkEst->execute();
            $estResult = $checkEst->get_result();
            $checkEst->close();
            if ($estResult->num_rows === 0) {
                $this->conn->rollback();
                return false;
            }

            $insertQuery = "INSERT INTO `operario_establcmto` (cedula, id_establcmto) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE id_establcmto = VALUES(id_establcmto)";
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