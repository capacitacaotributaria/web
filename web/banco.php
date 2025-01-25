<?php
$host = '10.129.76.12'; // Host do MySQL
$db = 'sistfinancas'; // Nome do banco de dados
$user = 'superadmin'; // Usuário MySQL
$pass = 'Sistemas01*'; // Senha MySQL

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>

