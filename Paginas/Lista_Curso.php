<?php
session_start();
ob_start();
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// include_once'../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();
// Configurações de conexão com o banco de dados
include_once '../PHP/config.php';

// Modifique esta parte do seu código para recuperar as faculdades do banco de dados
$selectStmtFaculdades = $Conexao->prepare("SELECT * FROM faculdadeTBL");
$selectStmtFaculdades->execute();
$faculdades = $selectStmtFaculdades->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>listar Cursos</title>
  <link rel="stylesheet" href="../EStilo/Listar_UC.css">
  <link rel="stylesheet" href="../EStilo/universai_user.css">
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/Processa.css">
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

    </div>
  </ul>

  <div class="content">
    <div class="barra">
      <div class="selecao">
        <select name="faculdade" id="faculdade-select">
          <option value="" selected disabled>Selecione uma Faculdade</option>
          <?php foreach ($faculdades as $faculdade) { ?>
            <option value="<?php echo $faculdade['ID_faculdade']; ?>"><?php echo $faculdade['nome']; ?></option>
          <?php } ?>
        </select>
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
    </div><br>

    <fieldset>
      <legend>
        <h2>Lista de Cursos</h2>
      </legend>
      <div class="pai">
        <table>
          <div class="STACY" id="lista-cursos">
            <th class="th">
              <h3>Código do Curso</h3>
            </th>
            <th class="th th2">
              <h3>Nome do Curso</h3>
            </th>
            <th class="th th2">
              <h3>Director</h3>
            </th>
            <th class="th th2">
              <h3>Faculdade</h3>
            </th>
            <th class="th th2">
              <h3>Acção</h3>
            </th>
            <hr>
            <?php

            // Cria uma nova conexão PDO
            // $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Configura o modo de erro do PDO para exceções
            // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Função para processar a exclusão de um registro
            if (isset($_POST['excluir_curso'])) {
              $curso_id = $_POST['excluir_curso'];

              // Execute a operação de exclusão
              $deleteStmt = $Conexao->prepare("DELETE FROM cursoTBL WHERE ID_curso = :curso_id");
              $deleteStmt->bindParam(':curso_id', $curso_id, PDO::PARAM_STR);

              if ($deleteStmt->execute()) {
                // Registro excluído com sucesso
                echo '<div class="ante_sucesso"><div class="sucesso"><center>Registro excluído com sucesso!</center></div></div>';
                // Você pode redirecionar o usuário de volta para a página de listagem de cursos aqui
                header("Refresh:1; url=Lista_Curso.php");
              } else {
                // Erro ao excluir o registro
                echo "Erro ao excluir o registro.";
              }
              exit; // Encerre o script após a exclusão
            }

            // Verifica se o registro já existe na tabela "cliente"
            $selectStmt = $Conexao->prepare("SELECT * FROM cursoTBL");
            $selectStmt->execute();

            // print_r($selectStmt->fetch(PDO::FETCH_ASSOC));
            if ($selectStmt) {
              while ($resultadoUC = $selectStmt->fetch(PDO::FETCH_ASSOC)) {
                $curso_id = $resultadoUC['ID_curso'];
                if (isset($_POST['uc-name'])) {
                  $id = $_POST['uc-name'];
                  $_SESSION['id_curso'] = $id;
                  // header("Location: ../Paginas/Editar_Curso.php");
                  echo '<meta http-equiv="refresh" content="1;url=../Paginas/Editar_Curso.php">'; // Use a tag meta para redirecionar
                  // header('Refresh: 1; url=../Paginas/Editar_Curso.php');
                }

                // if ($stmt->execute()) {
                //   echo '<div class="ante_sucesso"><div class="sucesso"><center>Curso atualizado com sucesso!!</center></div></div>';
                //   echo '<meta http-equiv="refresh" content="1;url=../Paginas/Lista_Curso.php">'; // Use a tag meta para redirecionar
                // }
                // <input type="submit" name="id_casa" value='.$id_casa.'>
                echo '
                      <tr>
                        <td>' . $resultadoUC['ID_curso'] . '</td>
                        <td>' . $resultadoUC['nome'] . '</td>
                        <td>' . $resultadoUC['diretor'] . '</td>
                        <td>' . $resultadoUC['ID_faculdade'] . '</td>  
                        <td class="accao">
                      
                          <center>
                            <form action="" method="post">
                            <button type="submit" name="uc-name" value="' . $curso_id . '" class="button azul">Atualizar</button>
                            <button type="submit" name="excluir_curso" value="' . $curso_id . '" class="button vermelho">Excluir</button>
                            </form>
                          </center> 
                        </td>   
                      </tr>  
                      ';
              }
            } else {
              echo "Nenhuma Curso cadastrada";
            }
            ob_end_flush();
            ?>
          </div>
        </table>
      </div>
    </fieldset>
    <a href="../Paginas/Cadastro_Curso.php" class="adicionar"><img src="../icon/adicionar.png" alt="icon" style="width: 50px; height: 50px;"></a>

  </div>


</body>

</html>