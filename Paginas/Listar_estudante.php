<?php

ob_start();

// if (session_status() == PHP_SESSION_NONE) {
//   session_start();
// }
// ... Configurações de conexão e cabeçalho ...
include_once '../PHP/config.php';
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();

//ob_start();

?>


<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>listar Estudante</title>
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/Lista_Estudante.css">
  <link rel="stylesheet" href="../EStilo/universai_user.css">
  <link rel="icon" href="../icon/open-book_171322.png">

</head>

<body>
  <div class="cima"></div>
  <ul class="sidenav">
    <li><a class="active" href="../Paginas/menu.php">Home</a></li>

    <li>
      <div class="dropdown">
        <button class="dropbtn">Cadastro</button>
        <div class="dropdown-content">
          <a href="../Paginas/Cadastro_Curso.php">Cursos</a>
          <a href="../Paginas/Cadatro_UC.php">UC</a>
          <!-- <a href="../Paginas/Associar_UC_curso.php">Associar_UC ao Curso</a> -->
          <a href="../Paginas/Cadastro_Estudante.php">Estudantes</a>
          <a href="../Paginas/Cadastro_Docente.php">Docentes</a>
        </div>
      </div>
    </li>

    <li>
      <div class="dropdown">
        <button class="dropbtn">Associar</button>
        <div class="dropdown-content">
          <a href="../Paginas/Associar_Estudante_UC.php">Cadeira ao Estudante</a>
          <a href="../Paginas/Associar_UC_docente.php">Cadeira ao Docente</a>
          <a href="../Paginas/Associar_UC_curso.php">Cadeira ao Curso</a>
        </div>
      </div>
    </li>

    <li>
      <div class="dropdown">
        <button class="dropbtn">Gerenciar</button>
        <div class="dropdown-content">
          <a href="../Paginas/Lista_Curso.php">Cursos</a>
          <a href="../Paginas/Listar_UC.php">UC</a>
          <a href="../Paginas/Listar_estudante.php">Estudantes</a>
          <a href="../Paginas/Listar_Docente.php">Docentes</a>
        </div>
      </div>
    </li>

    <li>
      <div class="dropdown">
        <button class="dropbtn">Presencas</button>
        <div class="dropdown-content">
          <a href="../Paginas/presencaEstudante.php">Estudantes</a>
          <a href="../Paginas/presencaDocente.php">Docentes</a>
        </div>
      </div>
    </li>

    <li>
      <div class="dropdown">
        <!-- <button class="dropbtn"><a href="../Paginas/sair.php">SAir</a><button>  -->
        <a href="../Paginas/sair.php" class="adicionar"><img src="../icon/sair.png" alt="icon" style="width: 50px; height: 50px; margin-left: 97px;"></a>
      </div>
    </li>

  </ul>

  <div class="content">
    <div class="barra">
      <div class="selecao">
        <form action="">
          <select name="faculdade" onchange="showFaculdade(this.value)">
            <option value="" selected disabled>Selecione uma faculdade</option>
            <?php
            // Query para buscar os dados das faculdades
            $sql = "SELECT * FROM faculdadeTBL";
            $stmt = $Conexao->prepare($sql);
            $stmt->execute();
            $faculdades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Iteração sobre os resultados e criação das opções do menu suspenso
            foreach ($faculdades as $faculdade) {
              echo '<option value="' . $faculdade['ID_faculdade'] . '">' . $faculdade['nome'] . '</option>';
            }
            ?>
          </select>
        </form>
      </div>
      <div class="pesquisa">
        <form action="../Paginas/search.php" method="GET">
          <p>
            <span><input name="search" placeholder="Pesquisar" type="text"></span>
            <span><button type="submit">Buscar</button></span>
          </p>
        </form>
      </div>
      <h3>
        <?php
        include "../Paginas/data_admin.php";
        ?>
      </h3>
      <!-- <img src="../icon/ui_user_profile_person_icon_208878.png" style="width: 40px; height:40px" alt=""> -->
    </div>
    <div id="faculdadeInfo">
      <fieldset>
        <legend>
          <h2>Sobre Estudante</h2>
        </legend>
        <div class="pai">

          <table>
            <div class="STACY">
              <th class="th">
                <h3>Código de Estudante</h3>
              </th>
              <th class="th th2">
                <h3>Nome do Estudante</h3>
              </th>
              <th class="th th2">
                <h3>contacto</h3>
              </th>
              <th class="th th2">
                <h3>Email</h3>
              </th>
              <th class="th th2">
                <h3>curso</h3>
              </th>
              <th class="th th2">
                <h3>acção</h3>
              </th>
            </div>
            <hr>

            <?php

            if (isset($_POST['excluir'])) {
              $idEstudante = $_POST['excluir'];

              // Execute a operação de exclusão
              $deleteEstudante = $Conexao->prepare("DELETE FROM estudanteTBL WHERE ID_estudante = :idEstudante");
              $deleteEstudante->bindParam(':idEstudante', $idEstudante, PDO::PARAM_INT);

              if ($deleteEstudante->execute()) {
                // Registro excluído com sucesso
                echo '<div class="ante_sucesso"><div class="sucesso"><center>Registro excluído com sucesso!</center></div></div>';

                // Você pode redirecionar o usuário de volta para a página de listagem de cursos aqui
                header("Refresh:1; url= ../Paginas/Listar_estudante.php");
                ob_end_flush(); // Envia o buffer de saída
                exit(); // Encerre o script após a exclusão
              } else {
                // Erro ao excluir o registro
                echo "Erro ao excluir o registro.";
              }
              exit(); // Encerre o script após a exclusão
            }

            $sql = "SELECT * FROM estudanteTBL";
            $stmt = $Conexao->prepare($sql);
            $stmt->execute();

            if ($stmt) {
              while ($estudante = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $idEstudante = $estudante['ID_estudante'];
                if (isset($_POST['es-name'])) {
                  $ID = $_POST['es-name'];
                  $_SESSION['ID_estudante'] = $ID;
                  header("Location: ../Paginas/Editar_Estudante.php");
                }


                echo '
                  <tr>
                    <td>' . $estudante['ID_estudante'] . '</td>
                    <td>' . $estudante['nome'] . '</td>
                    <td>' . $estudante['contacto'] . '</td>
                    <td>' . $estudante['email'] . '</td>
                    <td>' . $estudante['curso'] . '</td>
                    <td class="accao">
                      <form action="" method="post">
                        <center>
                          <button type="submit" name="es-name" value=' . $idEstudante .  ' class="button azul">Atualizar</button>
                          <button type="submit" name="excluir" value=' . $idEstudante . ' class="button vermelho">Excluir</button>
                        </center>
                      </form>
                    </td>
                  </tr>
                  ';
              }
            }

            ob_end_flush(); // Envia o buffer de saída
            ?>
          </table>
          <br><br><b></b>
        </div>
      </fieldset>
    </div>
    <a href="../Paginas/Cadastro_Estudante.php" class="adicionar"><img src="../icon/adicionar.png" alt="icon" style="width: 50px; height: 50px;"></a>
  </div>

</body>

</html>

<?php

?>