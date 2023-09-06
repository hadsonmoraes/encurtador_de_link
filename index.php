<?php
include "php/config.php";
include "php/upload.php";

$new_url = "";
if (isset($_GET)) {
  foreach ($_GET as $key => $val) {
    $u = mysqli_real_escape_string($conn, $key);
    $new_url = str_replace('/', '', $u);
  }
  $sql = mysqli_query($conn, "SELECT full_link FROM url WHERE shorten_url = '{$new_url}'");
  if (mysqli_num_rows($sql) > 0) {
    $sql2 = mysqli_query($conn, "UPDATE url SET clicks = clicks + 1 WHERE shorten_url = '{$new_url}'");
    if ($sql2) {
      $full_url = mysqli_fetch_assoc($sql);
      header("Location:" . $full_url['full_link']);
      exit;
    }
  }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Encurtador de URL </title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/ico" href="https://cervinixdigital.com.br/images/favicon.png">
  <!-- Iconsout Link for Icons -->
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v3.0.6/css/line.css">
</head>

<body>
  <div id="excel-loading" class="loading-spinner">
    <svg class="spinner" width="50px" height="50px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
      <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
    </svg>
  </div>


  <div class="wrapper">
    <?php
    if (isset($import_success) && $import_success) {
      echo '<div class="success-message"><i class="uil uil-check-circle">Importação do arquivo Excel bem-sucedida.</i></div>';
      header("refresh:3;url=/");
    } elseif (isset($import_error)) {
      echo '<div class="error-message"> <i class="uil uil-exclamation-octagon">' . $import_error . '</i></div>';
      header("refresh:5;url=/");
    }
    ?>

    <form action="#" autocomplete="off">
      <input type="text" spellcheck="false" name="full_url" id="textInput" placeholder="Digite ou cole uma URL" required>
      <i class="url-icon uil uil-link"></i>
      <button>Encurtar</button>
    </form>
    <h4>Ou</h4>
    <form action="upload" method="post" enctype="multipart/form-data">
      <input type="file" name="excel_file" id="fileInput" accept=".xlsx, .xls">
      <i class="file-icon uil uil-file-import"></i>
      <button type="submit" name="submit" id="uploadButton">Enviar Planilha</button>
    </form>

    <?php
    $sql2 = mysqli_query($conn, "SELECT * FROM url ORDER BY id DESC");
    if (mysqli_num_rows($sql2) > 0) {;
    ?>
      <div class="statistics">
        <?php
        $sql3 = mysqli_query($conn, "SELECT COUNT(*) FROM url");
        $res = mysqli_fetch_assoc($sql3);

        $sql4 = mysqli_query($conn, "SELECT clicks FROM url");
        $total = 0;
        while ($count = mysqli_fetch_assoc($sql4)) {
          $total = $count['clicks'] + $total;
        }
        ?>
        <span>Total Links: <span><?php echo end($res) ?></span> & Total Cliques: <span><?php echo $total ?></span></span>
        <a href="php/export.php">Exportar Excel</a>
        <a href="php/delete.php?delete=all">Excluir Todos</a>
      </div>
      <div class="urls-area">
        <div class="title">
          <li>URL Encurtada </li>
          <li>URL Original </li>
          <li>Nome </li>
          <li>Cliques</li>
          <li>Ação</li>
        </div>
        <?php
        while ($row = mysqli_fetch_assoc($sql2)) {
        ?>
          <div class="data">
            <li>
              <a href="<?php echo $row['shorten_url'] ?>" target="_blank">
                <?php
                if (strlen($row['shorten_url']) > 50) {
                  echo substr($row['shorten_url'], 0, 50) . '...';
                } else {
                  echo $domain . $row['shorten_url'];
                }
                ?>
              </a>
            </li>
            <li>
              <?php
              if (strlen($row['full_link']) > 60) {
                echo substr($row['full_link'], 0, 60) . '...';
              } else {
                echo $row['full_link'];
              }
              ?>
            </li>
            <li>
              <?php
              if (strlen($row['nome']) > 60) {
                echo substr($row['nome'], 0, 60) . '...';
              } else {
                echo $row['nome'];
              }
              ?>
            </li>
            <li><?php echo $row['clicks'] ?></li>
            <li><a href="php/delete.php?id=<?php echo $row['shorten_url'] ?>">Excluir</a></li>
          </div>
        <?php
        }
        ?>
      </div>
    <?php
    }
    ?>
  </div>

  <div class="blur-effect"></div>
  <div class="popup-box">
    <div class="info-box">Seu link curto está pronto. Você também pode editar seu link curto agora, mas não poderá editá-lo depois de salvá-lo.</div>
    <form action="#" autocomplete="off">
      <label>Edite seu URL encurtado</label>
      <input type="text" class="shorten-url" spellcheck="false" required>
      <i class="copy-icon uil uil-copy-alt"></i>
      <button>Salvar</button>
    </form>
  </div>

  <script src="script.js"></script>

</body>

</html>