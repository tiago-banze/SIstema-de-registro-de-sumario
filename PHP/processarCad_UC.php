<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Processamento</title>
  <link rel="icon" href="../icon/open-book_171322.png">
  <link rel="stylesheet" href="../EStilo/Processa.css">
</head>

<body>
  <?php
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  include_once '../PHP/config.php';

  if (isset($_POST["cadastrar"])) {
    $codigoUC = $_POST['codigo_uc'];
    $nomeUC = $_POST['nome_uc'];
    $curso = $_POST['curso'];
    $creditos = $_POST['creditos'];
    $duracao = $_POST['duração'];
    // $precedente = $_POST['curso_precedencia'];

    try {
      $Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sqlcadeira_uc = "INSERT INTO curso_ucTBL (ID_curso, ID_cadeira)
                          VALUES (:codigo_curso, :codigo_cadeira)";
      $stmtt = $Conexao->prepare($sqlcadeira_uc);
      $stmtt->bindParam(':codigo_curso', $curso);
      $stmtt->bindParam(':codigo_cadeira', $codigoUC);



      $sql = "INSERT INTO cadeiraTBL (ID_cadeira, nome, credito, curso, hora_semestral) 
                  VALUES (:codigo_uc, :nome_uc, :creditos, :curso, :duracao)";
      $stmt = $Conexao->prepare($sql);
      $stmt->bindParam(':codigo_uc', $codigoUC);
      $stmt->bindParam(':nome_uc', $nomeUC);
      $stmt->bindParam(':creditos', $creditos);
      $stmt->bindParam(':curso', $curso);
      $stmt->bindParam(':duracao', $duracao);

      if (($stmt->execute()) && ($stmtt->execute())) {
        echo '<div class="ante_sucesso"><div class="sucesso"><center>UC cadastrada com sucesso!!</center></div></div>';
        header('Refresh:1; url= ../Paginas/menu.php');
      } else {
        echo '<div class="ante_sucesso"><div class="sucesso"><center>Erro ao cadastrar UC.!</center></div></div>';
        header('Refresh:1; url= ../Paginas/Cadastro_UC.php');
      }
    } catch (PDOException $e) {
      echo '<div class="ante_sucesso"><div class="sucesso"><center>Erro na conexão: ' . $e->getMessage() . '</center></div></div>';
      header('Refresh:4; url= ../Paginas/Cadastro_UC.php');
    }
  }
  ?>

</body>

</html>