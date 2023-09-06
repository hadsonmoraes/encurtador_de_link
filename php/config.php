<?php

$domain =  "localhost/encurtador/";
$host = "localhost";
$user = "root";
$pass = "";
$db = "encurt";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    echo "Erro ao conectar no banco" . mysqli_connect_error();
}
