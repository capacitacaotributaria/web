<?php
// db.php
$servername = "leadsdb.mysql.uhserver.com";
$username = "superadmin1";
$password = "Sistemas01*";
$dbname = "leadsdb";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>