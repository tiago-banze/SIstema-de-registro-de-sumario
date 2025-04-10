<?php
session_start();
include_once '../PHP/config.php';
// include_once'../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();

$sql = "SELECT * FROM cadeiraTBL";
$stmt = $Conexao->prepare($sql);
$stmt->execute();
$cadeiras = $stmt->fetchAll(PDO::FETCH_ASSOC); // Corrigido para $cadeiras

// Função para processar a exclusão de um registro
if (isset($_POST['excluir_UC'])) {
  $UC_id = $_POST['excluir_UC'];

  // Execute a operação de exclusão
  $deleteStmt = $Conexao->prepare("DELETE FROM cadeiraTBL WHERE ID_cadeira = :UC_id");
  $deleteStmt->bindParam(':UC_id', $UC_id, PDO::PARAM_STR);

  if ($deleteStmt->execute()) {
    // Registro excluído com sucesso
    // echo'<div class="ante_sucesso"><div class="sucesso"><center>Registro excluído com sucesso!</center></div></div>';
    // Você pode redirecionar o usuário de volta para a página de listagem de UC aqui
    header("Refresh:1; url= ../Paginas/Listar_UC.php");
  } else {
    // Erro ao excluir o registro
    echo "Erro ao excluir o registro.";
  }
  exit; // Encerre o script após a exclusão
}


?>
<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>listar Cadeiras</title>
  <link rel="stylesheet" href="../EStilo/Listar_UC.css">
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/universai_user.css">
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


  </ul>

  <div class="content">
    <div class="barra">
      <div class="selecao">
        <select name="" id="">
          <option value="" selected disabled>Selecione cadeiras de determinado curso</option>
          <option value="">Engenharia Informatica e tecnologia</option>
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
        <h2>Lista de cadeiras</h2>
      </legend>

      <div class="pai">
        <table>
          <div class="STACY">
            <th class="th">
              <h3>Código de UC</h3>
            </th>
            <th class="th th2">
              <h3>Nome da UC</h3>
            </th>
            <th class="th th2">
              <h3>Credito</h3>
            </th>
            <th class="th th2">
              <h3>Curso</h3>
            </th>
            <th class="th th2">
              <h3>Acção</h3>
            </th>
          </div>
          <hr>
          <?php
          // Verifica se o registro já existe na tabela "cliente"
          $selectStmt = $Conexao->prepare("SELECT * FROM cadeiraTBL");
          $selectStmt->execute();

          // print_r($selectStmt->fetch(PDO::FETCH_ASSOC));
          if ($selectStmt) {
            while ($resultadouc = $selectStmt->fetch(PDO::FETCH_ASSOC)) {
              $UC_id = $resultadouc['ID_cadeira'];
              if (isset($_POST['Atualizar'])) {
                // echo $curso_id;
                $id = $_POST['Atualizar'];
                // echo $id;
                $_SESSION['ID_cadeira'] = $id;
                header('Location: ../Paginas/Editar_UC.php');
              }
              // <input type="submit" name="id_casa" value='.$id_casa.'>
              echo '
              <tr>
                <td>' . $resultadouc['ID_cadeira'] . '</td>
                <td>' . $resultadouc['nome'] . '</td>
                <td>' . $resultadouc['credito'] . '</td>
                <td>' . $resultadouc['curso'] . '</td>  
                <td class="accao">
                  <center>
                    <form action="" method="post">
                      <button type="submit" name="Atualizar" value="' . $UC_id . '" class="button azul">Atualizar</button>
                      <button type="submit" name="excluir_UC" value="' . $UC_id . '" class="button vermelho">Excluir</button>
                    </form>
                  </center> 
                </td>   
              </tr>  
              ';
            }
          } else {
            echo "Nenhuma UC cadastrada";
          }
          ?>
        </table>
      </div>
    </fieldset>
    <a href="../Paginas/Cadatro_UC.php" class="adicionar"><img src="../icon/adicionar.png" alt="icon" style="width: 50px; height: 50px;"></a>
  </div>
</body>

</html>