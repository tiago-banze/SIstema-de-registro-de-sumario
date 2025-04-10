<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Associar_UC_Docente</title>
    <link rel="icon" href="../icon/open-book_171322.png">
    <link rel="stylesheet" href="../EStilo/Processa.css">
</head>
<body>
<?php
  if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
  }
  include_once'../PHP/config.php';


      try {
        // $Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (isset($_POST["Associar"])) {
          $cadeira = $_POST['cadeira'];
          $docente = $_POST['docente'];
          // $precedente = $_POST['curso_precedencia'];
            $Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sqldocente_uc="INSERT INTO docente_ucTBL (ID_docente, ID_cadeira)
                            VALUES (:codigo_docente, :codigo_cadeira)";
             $stmtt = $Conexao->prepare($sqldocente_uc);
             $stmtt->bindParam(':codigo_docente', $docente);
             $stmtt->bindParam(':codigo_cadeira', $cadeira);
            if ($stmtt->execute()) {
                echo'<div class="ante_sucesso"><div class="sucesso"><center>Estudante Associado com sucesso!</center></div></div>';
                header('Refresh:1; url= ../Paginas/menu.php');
          } else {
            echo'<div class="ante_sucesso"><div class="sucesso"><center>Erro ao Associar Estudante ca UC!</center></div></div>';
            header('Refresh:1; url= ../Paginas/Associar_UC_docente.php');
          }}
      } catch (PDOException $e) {
        echo '<div class="ante_sucesso"><div class="sucesso"><center>Erro na conexão: ' . $e->getMessage() . '</center></div></div>';
        header('Refresh:4; url= ../Paginas/Associar_UC_docente.php');
      }
  
?>

</body>
</html>