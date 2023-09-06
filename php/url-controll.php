<?php
include "config.php";

$full_url = mysqli_real_escape_string($conn, $_POST['full_url']);

// Remova espaços em branco do full_url ( full_ link)
$full_url = str_replace(' ', '', $full_url);

if (!empty($full_url) && filter_var($full_url, FILTER_VALIDATE_URL)) {
    // Verifique se a URL já existe no banco de dados
    $sql_check_duplicate = mysqli_query($conn, "SELECT * FROM url WHERE full_link = '{$full_url}'");

    if (mysqli_num_rows($sql_check_duplicate) > 0) {
        // Se a URL já existe, recupere o URL encurtado existente
        $existing_record = mysqli_fetch_assoc($sql_check_duplicate);
        echo $existing_record['shorten_url'];
    } else {
        $ran_url = substr(md5(microtime()), rand(0, 26), 5);
        $sql_insert_url = mysqli_query($conn, "INSERT INTO url (full_link, shorten_url, clicks) 
                                             VALUES ('{$full_url}', '{$ran_url}', '0')");
        if ($sql_insert_url) {
            echo $ran_url;
        }
    }
} else {
    echo "Este não é um URL válido!";
}
