<?php
include "config.php";
require __DIR__ . '../../vendor/autoload.php'; // Ajuste o caminho conforme necessário

use PhpOffice\PhpSpreadsheet\IOFactory;

// Função personalizada para limpar o full_link
function cleanFullLink($full_link) {
    // Remova espaços em branco, quebras de linha e caracteres indesejados
    $cleaned_link = preg_replace('/\s+/', '', $full_link);
    return $cleaned_link;
}

if (isset($_POST['submit'])) {
    if (empty($import_error)) {
        $upload_directory = 'public/';

        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0755, true);
        }

        $target_file = $upload_directory . basename($_FILES['excel_file']['name']);

        if (move_uploaded_file($_FILES['excel_file']['tmp_name'], $target_file)) {
            // $import_success = true;
        } else {
            $import_error = "Houve um erro ao mover o arquivo para a pasta pública.";
        }
    }

    // Carregue o arquivo Excel
    $spreadsheet = IOFactory::load($target_file);

    // Obtenha a planilha ativa
    $worksheet = $spreadsheet->getActiveSheet();

    if ($worksheet->getHighestRow() < 2) {
        $import_error = "O arquivo Excel está vazio ou não contém dados.";
    } else {

        // Percorra as linhas da planilha começando da primeira linha (linha que contém o cabeçalho)
        $firstRow = $worksheet->getRowIterator(1)->current();
        // Obtenha o valor da coluna "full_url"
        $full_url = $worksheet->getCell('A1')->getValue(); // Use 'A' para a coluna "full_url"

        if ($full_url !== "full_link") {
            $import_error = "A primeira linha não contém 'full_link' como cabeçalho da coluna.";
        } else {
            // Preparação da declaração SQL
            $stmt = mysqli_prepare($conn, "INSERT INTO url (full_link, shorten_url, nome, clicks) VALUES (?, ?, ?, '0')");

            foreach ($worksheet->getRowIterator(2) as $row) {
                $full_url = $worksheet->getCell('A' . $row->getRowIndex())->getValue();
                $nome = $worksheet->getCell('B' . $row->getRowIndex())->getValue();
                // Limpe o full_link usando a função personalizada
                $full_url = cleanFullLink($full_url);

                // Verifique se a URL é válida
                if (!empty($full_url) && filter_var($full_url, FILTER_VALIDATE_URL)) {
                    $ran_url = substr(md5(microtime()), rand(0, 26), 5);

                    // Verifique se a URL encurtada já existe no banco de dados
                    $sql_check_duplicate = mysqli_query($conn, "SELECT * FROM url WHERE shorten_url = '{$ran_url}'");
                    if (mysqli_num_rows($sql_check_duplicate) > 0) {
                        $import_error = "Algo deu errado. Por favor, gere novamente!";
                    } else {
                        // Verifique se a URL completa já existe no banco de dados
                        $sql_check_full_url = mysqli_query($conn, "SELECT * FROM url WHERE full_link = '{$full_url}'");
                        if (mysqli_num_rows($sql_check_full_url) > 0) {
                            $import_error = "A URL '$full_url' já existe";
                        } else {
                            // Vincule os parâmetros e execute a declaração preparada
                            mysqli_stmt_bind_param($stmt, "sss", $full_url, $ran_url, $nome);
                            if (mysqli_stmt_execute($stmt)) {
                                // Sucesso
                                $import_success = true;
                            } else {
                                $import_error = "Erro ao inserir no banco de dados: " . mysqli_error($conn);
                            }
                        }
                    }
                } else {
                    $import_error = "$full_url - Não é um URL válido!<br>";
                }
            }

            // Feche a declaração preparada
            mysqli_stmt_close($stmt);
        }
    }
}
