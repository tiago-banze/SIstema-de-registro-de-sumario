<?php
// Inclua as configurações de conexão aqui
session_start();
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();

$servername = "localhost"; // ou o endereço do seu servidor MySQL
$dbname = "registro_sumario"; // o nome do seu banco de dados
$username = "root"; // o nome de usuário do seu banco de dados
$password = ""; // a senha do seu banco de dados

try {
  $Conexao = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Erro de conexão: " . $e->getMessage();
}

if (isset($_SESSION['id_curso'])) {
  $id_uc = $_SESSION['id_curso'];
}

include_once '../Paginas/processa.php'; // Inclua o processamento aqui

if (isset($_POST['Atualizar'])) {
  $novoNome = $_POST['novo_nome'];
  $cursoID = $_POST['curso_id'];
  $diretor = $_POST['novo_diretor'];
  $faculdade = $_POST['nova_faculdade'];

  $sql = "UPDATE cursoTBL SET ID_curso=:novo_id, nome = :novo_nome, diretor=:novo_diretor, ID_faculdade=:nova_faculdade WHERE ID_curso = :curso_id";
  $stmt = $Conexao->prepare($sql);
  $stmt->bindParam(':novo_id', $cursoID);
  $stmt->bindParam(':novo_nome', $novoNome);
  $stmt->bindParam(':novo_diretor', $diretor);
  $stmt->bindParam(':nova_faculdade', $faculdade);
  $stmt->bindParam(':curso_id', $id_uc);
  if ($stmt->execute()) {
    echo '<div class="ante_sucesso"><div class="sucesso"><center>Curso atualizado com sucesso!!</center></div></div>';
    echo '<meta http-equiv="refresh" content="1;url=../Paginas/Lista_Curso.php">';
    exit;
  }
}
?>




<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    ul,
    a {
      font-size: 25px;
    }
  </style>
  <link rel="stylesheet" href="../EStilo/Cadastro_UC.css">
  <link rel="stylesheet" href="../EStilo/NavBar.css">
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
    <?php
    // Cria uma nova conexão PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configura o modo de erro do PDO para exceções
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o registro já existe na tabela "cliente"
    $selectStmt = $conn->prepare("SELECT * FROM cursoTBL WHERE ID_curso = :id_curso");
    $selectStmt->bindValue(':id_curso', $id_uc);

    if ($selectStmt->execute()) {
      $resultado = $selectStmt->fetch(PDO::FETCH_ASSOC);
      // print_r($resultado);

      echo '
        <form method="POST" action="">
          <fieldset>
          <legend><h2>Cadastrar Curso</h2></legend>
          <div class="pai">
            <input type="text" name="curso_id" required placeholder="Codígo do Cursos" class="tamanhoinput" value="' . $resultado['ID_curso'] . '">
            <input type="text" name="novo_nome" required placeholder="Nome do Curso" class="input tamanhoinput" value="' . $resultado['nome'] . '" >
            <input type="text" name="novo_diretor" required placeholder="Diretor do curso" class="input tamanhoinput" value="' . $resultado['diretor'] . '">
            <select id="" name="nova_faculdade" required class="class ">
                <option value="" selected >Selecione nova Faculdade</option>';
      // Buscar faculdades cadastradas no banco de dados e preencher as opções do select
      $sql_faculdades = "SELECT * FROM faculdadeTBL";
      $stmt_faculdades = $Conexao->query($sql_faculdades);
      while ($row_faculdade = $stmt_faculdades->fetch(PDO::FETCH_ASSOC)) {
        echo ' <option value="' . $row_faculdade['ID_faculdade'] . '"> ' . $row_faculdade['nome'] . '</option>';
        // var_dump($row_faculdade);
      }
      echo ' 
              </select> 
              <br>
              <input type="submit" name="Atualizar" value="Atualizar" class="botao">
            </div>
          </fieldset>
        </form>
       ';
      if (isset($_POST['Atualizar'])) {
        $new_name = $_POST['novo_nome'];
        $new_director = $_POST['novo_diretor'];
        $new_faculdade = $_POST['nova_faculdade'];
        echo $new_name, $new_director, $new_faculdade;
      }
    }
    ?>


  </div>


</body>

</html>
<?php
// $curso = null; 

include_once '../Paginas/processa.php';

$mensagem = ""; // Inicializa a variável de mensagem vazia

if (isset($_GET['id'])) {
  $cursoID = $_GET['id'];

  $sql = "SELECT * FROM cursoTBL WHERE ID_curso = :id";
  $stmt = $Conexao->prepare($sql);
  $stmt->bindParam(':id', $cursoID);
  $stmt->execute();
  $curso = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($curso) {
    if (isset($_POST['Atualizar'])) {
      $novoNome = $_POST['novo_nome'];
      $diretor = $_POST['diretor'];
      $faculdade = $_POST['ID_faculdade'];

      $sql = "UPDATE cursoTBL SET nome = :novo_nome, diretor = :novo_diretor, ID_faculdade = :nova_faculdade WHERE ID_curso = :curso_id";
      $stmt = $Conexao->prepare($sql);
      $stmt->bindParam(':novo_nome', $novoNome);
      $stmt->bindParam(':novo_diretor', $diretor);
      $stmt->bindParam(':nova_faculdade', $faculdade);
      $stmt->bindParam(':curso_id', $cursoID);

      if ($stmt->execute()) {
        $mensagem = "Curso atualizado com sucesso!";
        header('Refresh: 1; url=../Paginas/Lista_Curso.php'); // Redireciona após a atualização
      } else {
        $mensagem = "Erro ao atualizar o curso.";
      }
    }
  } else {
    $mensagem = "Nenhum curso encontrado para atualização.";
  }
} else {
  $mensagem = "ID do curso não especificado.";
}
// Fechar conexão
$Conexao = null;
?>