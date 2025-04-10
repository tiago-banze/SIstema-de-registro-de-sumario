<?php
session_start();
include_once '../PHP/config.php';
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();
// include_once'../PHP/processarCad_UC.php';

if (isset($_SESSION['ID_cadeira'])) {
  $id_UC = $_SESSION['ID_cadeira'];
  // echo $id_uc;
}
// ... Configurações de conexão e cabeçalho ...
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include_once '../Paginas/processa.php';

if (isset($_POST['Atualizar'])) {
  $novoNome = $_POST['nome_uc'];
  $UCID = $_POST['codigo_uc'];
  $curso = $_POST['curso'];
  $credito = $_POST['creditos'];

  $sql = "UPDATE cadeiraTBL SET ID_cadeira=:novo_id, nome = :novo_nome,credito=:credito, curso=:nova_curso WHERE ID_cadeira = :UC_id";
  $stmt = $Conexao->prepare($sql);
  $stmt->bindParam(':novo_id', $UCID);
  $stmt->bindParam(':novo_nome', $novoNome);
  $stmt->bindParam(':nova_curso', $curso);
  $stmt->bindParam(':credito', $credito);
  $stmt->bindParam(':UC_id', $id_UC);
  if ($stmt->execute()) {
    echo '<div class="ante_sucesso"><div class="sucesso"><center>UC atualizado com sucesso!!</center></div></div>';
    header('Refresh:1; url=../Paginas/Lista_UC.php');
  }
}


?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/Cadastro_UC.css">
  <link rel="stylesheet" href="../EStilo/universai_user.css">
  <link rel="stylesheet" href="../EStilo/Processa.css">
  <link rel="icon" href="../icon/open-book_171322.png">
  <style>
    .btn {
      position: absolute;
      margin-top: 47px;
      margin-left: 300px;
      width: 200px;
      height: 50px;
      font-size: 25px;
      border-radius: 10px;
      background-color: black;
      color: white;
    }

    .btn:hover {
      color: black;
      background-color: rgb(190, 188, 188);
      box-shadow: 1px 1px 1px 1px rgb(74, 72, 72);
      border: 2px solid black;
      transition: 0.3s;
      cursor: pointer;
    }
  </style>
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
    //  $Conexao = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configura o modo de erro do PDO para exceções
    //  $Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o registro já existe na tabela "cliente"
    $selectStmt = $Conexao->prepare("SELECT * FROM cadeiraTBL WHERE ID_cadeira = :id_UC");
    $selectStmt->bindValue(':id_UC', $id_UC);

    if ($selectStmt->execute()) {
      $resultado = $selectStmt->fetch(PDO::FETCH_ASSOC);
      // print_r($resultado);

      echo '
        <form method="POST" action="">
          <fieldset>
          <legend><h2>Atualizar UC</h2></legend>
          <div class="pai">
            <input type="text" name="codigo_uc" required placeholder="Codígo da UC" class="tamanhoinput" value="' . $resultado['ID_cadeira'] . '">
            <input type="text" name="nome_uc" required placeholder="Nome da UC" class="input tamanhoinput" value="' . $resultado['nome'] . '" >
            <select name="curso" id="" class="class ">
            <option value="" selected disabled>Selecione o Curso</option>';
      // Buscar faculdades cadastradas no banco de dados e preencher as opções do select
      $sql_cursos = "SELECT * FROM cursoTBL";
      $stmt_cursos = $Conexao->query($sql_cursos);
      while ($row_curso = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . $row_curso['ID_curso'] . "'>" . $row_curso['nome'] . "</option>";
      }
      echo ' 
            </select> 
            <input type="number" name="creditos" required placeholder="Creditos" class="input tamanhoinput" value="' . $resultado['credito'] . '">
              <br>
              <input type="submit" name="Atualizar" value="Atualizar" class="botao">
            </div>
          </fieldset>
        </form>
       ';
      if (isset($_POST['Atualizar'])) {
        $new_name = $_POST['nome_uc'];
        $new_curses = $_POST['curso'];
        $new_credit = $_POST['creditos'];
        // echo $new_name , $new_curses , $new_credit;
      }
    }
    ?>
  </div>

</body>

</html>

<?php
// $curso = null; 
include_once '../PHP/config.php';
// include_once '../Paginas/processa.php';

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

      $sql = "UPDATE cadeiraTBL SET nome = :novo_nome,credito=:credito, curso=:nova_curso WHERE ID_cadeira = :UC_id";
      $stmt = $Conexao->prepare($sql);
      $stmt->bindParam(':novo_nome', $novoNome);
      $stmt->bindParam(':nova_curso', $curso);
      $stmt->bindParam(':credito', $credito);
      $stmt->bindParam(':UC_id',  $UCID);

      if ($stmt->execute()) {
        $mensagem = '<div class="ante_sucesso"><div class="sucesso"><center>UC atualizado com sucesso!!</center></div></div>';
        echo $mensagem;
        header('Refresh:1; url= ../Paginas/Listar_UC.php'); // Redireciona após a atualização
      } else {
        $mensagem = "Erro ao atualizar a UC.";
      }
    }
  } else {
    $mensagem = "Nenhuma UC encontrado para atualização.";
  }
} else {
  $mensagem = "ID da UC não especificado.";
}
// Fechar conexão
$Conexao = null;
?>