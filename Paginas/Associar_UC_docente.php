<?php 
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  include"../PHP/config.php";
  include_once'../PHP/inactividade.php';
  include_once('../PHP/SemAutenticacao.php');
  protect();
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../EStilo/NavBar.css">
<link rel="stylesheet" href="../EStilo/Associar_UC_curso.css">
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
      <form action="../PHP/ProcessarAssociarUC_Docente.php" method="post">
      <fieldset>
          <legend><h2>Associar UC ao Docente</h2></legend>
          <div class="pai">

              <select name="Estudante" id="" class="class " required>
                  <option value="" selected disabled>Selecione o Docente</option>
                  <?php
                  // Buscar cursos cadastrados no banco de dados e preencher as opções do select
                  $sql_Docente = "SELECT * FROM docenteTBL"; 
                  $stmt_Docente = $Conexao->query($sql_Docente);
                  while ($row_Docente = $stmt_Docente->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row_Docente['ID_docente'] . "'>" . $row_Docente['nome'] . "</option>";
                 }
                ?>
              </select>
              <select name="docente" id="" class="class " required>
                  <option value="" selected disabled>Selecione o Curso</option>
                  <?php
                  // Buscar cursos cadastrados no banco de dados e preencher as opções do select
                  $sql_UC = "SELECT * FROM cadeiraTBL"; 
                  $stmt_UC = $Conexao->query($sql_UC);
                  while ($row_UC = $stmt_UC->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row_UC['ID_cadeira'] . "'>" . $row_UC['nome'] . "</option>";
                 }
                ?>
              </select> <br>
            <input type="submit" name="Associar" value="Associar" class="botao">
          </div>
      </fieldset>
  </form>
  </div>

</body>
</html>

<?php 
   // Fechar conexão
   $Conexao = null;
?>


