
<?php

//ambiente free
$servername = 'mysql-produtos-produtos2025.g.aivencloud.com';
$username = 'avnadmin';
$password = 'AVNS_-eeyuxhLTGd4vyCFJ_O';
$dbname = 'dbproduto';
$port = 10242;

// Create connection
$con = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

?>