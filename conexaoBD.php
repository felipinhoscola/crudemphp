<?php 

class ConexaoBD {
    private $conn;

    public function __construct() {
        
        $hostname = "localhost";
        $bancodedados = "cadastros";
        $usuario = "root";
        $senha = "";

        try {
            $this->conn = new PDO ("mysql:host=$hostname;dbname=$bancodedados", $usuario, $senha);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getConexao() {
        return $this->conn;
    }
}

?>