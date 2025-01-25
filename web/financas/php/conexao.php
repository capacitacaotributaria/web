<?php
$host = "financadb.mysql.uhserver.com";
$user = "superadmin";
$password = "Sistemas02*";
$database = "financadb";

// Criar a conexão
$conn = new mysqli($host, $user, $password, $database);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: (" . $conn->connect_errno . ") " . $conn->connect_error);
}

// Definir o conjunto de caracteres para UTF-8 (opcional, mas recomendável)
$conn->set_charset("utf8mb4");

?>
