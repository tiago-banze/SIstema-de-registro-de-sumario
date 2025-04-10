<?php 
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  include_once'../PHP/processarCad_UC.php';
  include_once'../PHP/inactividade.php';
  include_once('../PHP/SemAutenticacao.php');
  protect();
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../EStilo/NavBar.css">
<link rel="stylesheet" href="../EStilo/Cadastro_UC.css">
<link rel="stylesheet" href="../EStilo/universai_user.css">
<link rel="icon" href="../icon/open-book_171322.png">
<style>
  .btn{
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

.btn:hover{
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
      <form action="../PHP/processarCad_UC.php" method="post">
      <fieldset>
          <legend><h2>Cadastrar UC</h2></legend>
          <div class="pai">
              <input type="text" placeholder="Codígo da UC" name="codigo_uc" class="tamanhoinput">
              <input type="text" placeholder="Nome da UC" name="nome_uc" class="input tamanhoinput">

              <select name="curso" id="" class="class ">
                  <option value="" selected disabled>Selecione o Curso</option>
                  <?php

                  // Buscar cursos cadastrados no banco de dados e preencher as opções do select
                  $sql_cursos = "SELECT * FROM cursoTBL"; 
                  $stmt_cursos = $Conexao->query($sql_cursos);
                  while ($row_curso = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row_curso['ID_curso'] . "'>" . $row_curso['nome'] . "</option>";
                 }
                ?>
              </select>
              <input type="number" name="creditos" placeholder="Créditos" class="input tamanhoinput">
            <br>
              <input type="number" name="duração" placeholder="Horas semestrais" class="input tamanhoinput">
            <input type="submit" name="cadastrar" value="Cadastrar" class="btn">
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


