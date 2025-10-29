<?php
class Connection {
    private $host = "localhost";
    private $user = "davidguijarro_davidguijarrofinal";
    private $pass = "Moodleclase1_";
    private $dbname = "davidguijarro_FINAL_GUIJARROCANO_DAVID";

    public function getConnection() {
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }
        $conn->set_charset("utf8");
        return $conn;
    }

    public function closeConnection($conn) {
        $conn->close();
    }
}