<?php
// Configurações de conexão
$host = "10.129.76.12";
$user = "zeoliveirars";
$password = "Sistemas01*";
$database = "sistfinancas";

// Estabelecendo conexão
$conn = new mysqli($host, $user, $password, $database);

// Verifica se há erros na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
