<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();

include_once '../Paginas/processa.php'
?>



<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CAdastro de curso</title>
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/Cadastro_UC.css">
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
    <form action="../Paginas/processa.php" method="POST">
      <fieldset>
        <legend>
          <h2>Cadastrar Curso</h2>
        </legend>
        <div class="pai">
          <input type="text" name="codigo_curso" required placeholder="Codígo do Cursos" class="tamanhoinput">
          <input type="text" name="nome_curso" required placeholder="Nome do Curso" class="input tamanhoinput">
          <input type="text" name="diretor" required placeholder="Diretor do curso" class="input tamanhoinput">

          <select id="" name="faculdade" required class="class ">
            <option value="" selected disabled>Selecione a Faculdade</option>
            <?php
            // Buscar faculdades cadastradas no banco de dados e preencher as opções do select
            $sql_faculdades = "SELECT * FROM faculdadeTBL";
            $stmt_faculdades = $Conexao->query($sql_faculdades);
            while ($row_faculdade = $stmt_faculdades->fetch(PDO::FETCH_ASSOC)) {
              echo "<option value='" . $row_faculdade['ID_faculdade'] . "'>" . $row_faculdade['nome'] . "</option>";
              // var_dump($row_faculdade);
            }
            ?>
          </select>
          <br>
          <input type="submit" value="Cadastrar" name="cadastrar" class="botao">
        </div>
      </fieldset>
    </form>



</body>

</html>
<?php
// Fechar conexão
$Conexao = null;
?>