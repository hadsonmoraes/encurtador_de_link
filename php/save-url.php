<?php
include "config.php";
$og_url = mysqli_real_escape_string($conn, $_POST['shorten_url']);
$shorten_url = str_replace(' ', '', $og_url);
$hidden_url = mysqli_real_escape_string($conn, $_POST['hidden_url']);

if (!empty($shorten_url)) {
    if (preg_match("/\//i", $shorten_url)) {
        $explodeURL = explode('/', $shorten_url);
        $shortURL = end($explodeURL);
        if ($shortURL != "") {
            $sql = mysqli_query($conn, "SELECT shorten_url FROM url WHERE shorten_url = '{$shortURL}' && shorten_url != '{$hidden_url}'");
            if (mysqli_num_rows($sql) == 0) {
                $sql2 = mysqli_query($conn, "UPDATE url SET shorten_url = '{$shortURL}' WHERE shorten_url = '{$hidden_url}'");
                if ($sql2) {
                    echo "success";
                } else {
                    echo "Erro - Falha ao atualizar o link!";
                }
            } else {
                echo "O URL curto que você inseriu já existe. Por favor insira outro!";
            }
        } else {
            echo "Obrigatório - Você deve inserir um URL curto!";
        }
    } else {
        echo "URL inválido - Você não pode editar o nome de domínio!";
    }
} else {
    echo "Erro - Você precisa inserir um URL curto!";
}
