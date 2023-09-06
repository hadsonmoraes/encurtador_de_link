<?php
require __DIR__ . '../../vendor/autoload.php'; // Ajuste o caminho conforme necessário
include 'config.php'; // Inclua seu arquivo de configuração do banco de dados

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Consulta SQL para recuperar os dados do banco de dados
$sql = "SELECT shorten_url, nome FROM url"; // Supondo que sua tabela se chame 'url'

$result = mysqli_query($conn, $sql);

if (!$result) {
  die("Erro na consulta: " . mysqli_error($conn));
}

// Criar uma instância da classe Spreadsheet
$spreadsheet = new Spreadsheet();

// Criar uma planilha ativa
$worksheet = $spreadsheet->getActiveSheet();

// Definir os cabeçalhos das colunas
$worksheet->setCellValue('A1', 'shorten_url');
$worksheet->setCellValue('B1', 'nome');

// Inicializar a linha atual
$row = 2;

// Loop através dos dados do banco de dados e inseri-los na planilha
while ($row_data = mysqli_fetch_assoc($result)) {
  $base = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  $quantidade = 14;
  $string = substr($base, 0, -1 * $quantidade);
  $combined_url = $string . $row_data['shorten_url'];

  $worksheet->setCellValue('A' . $row, $combined_url);
  $worksheet->setCellValue('B' . $row, $row_data['nome']);
  $row++;
}

// Definir o cabeçalho do arquivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="dados_url.xlsx"');
header('Cache-Control: max-age=0');

// Criar um objeto Writer para o formato Xlsx
$writer = new Xlsx($spreadsheet);

// Salvar o arquivo Excel na saída (no navegador)
$writer->save('php://output');

// Fechar a conexão com o banco de dados, se necessário
mysqli_close($conn);
