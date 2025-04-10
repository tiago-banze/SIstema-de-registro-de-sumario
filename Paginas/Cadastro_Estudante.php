<?php
include_once '../PHP/processarCad_Estudante.php';
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();
?>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro_Docente</title>
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/Cadastro_estudante.css">
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
    <form action="../PHP/processarCad_Estudante.php" method="POST">
      <fieldset>
        <legend>
          <h2>Cadastrar Estudante</h2>
        </legend>
        <div class="pai">
          <div class="left">
            <input type="number" name="codigo" placeholder="Codígo do Estudante" class="tamanhoinput outro" required><br>
            <input type="text" name="nome" placeholder="Nome do Estudante" class="input tamanhoinput" required><br>
            <input type="tel" name="tel" id="" placeholder="Contacto" class="input tamanhoinput" required><br>
            <select name="turma" class="class input tamanhoinput" required>
              <option value="" selected disabled>Selecione a Turma</option>
              <?php
                $queryTurmas = "SELECT * FROM turmatbl";
                $stmtTurmas = $Conexao->query($queryTurmas);
                $stmtTurmas->execute();
                while ($turma = $stmtTurmas->fetch(PDO::FETCH_ASSOC)) {
                  echo "<option value='" . $turma['ID_turma'] . "'>" . $turma['ID_turma'] . "</option>";
                  // $_SESSION['ID_turma'] = $turma['ID_turma'];
                }
              ?>
            </select><br>
            <select name="curso" id="" class="input tamanhoinput" required>
              <?php

              // Buscar cursos cadastrados no banco de dados e preencher as opções do select
              $sql_cursos = "SELECT * FROM cursoTBL";
              $stmt_cursos = $Conexao->query($sql_cursos);
              while ($row_curso = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $row_curso['ID_curso'] . "'>" . $row_curso['nome'] . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="right">
            <input type="text" name="nacionalidade" id="rightinput" placeholder="Nacionalidade" class="tamanhoinput outro" required><br>
            <input type="text" name="provincia" id="rightinput" placeholder="Província" class="input tamanhoinput rightinput" required><br>
            <input type="text" name="distrito" id="rightinput" placeholder="Distrito" class="input tamanhoinput rightinput" required><br>
            <select name="genero" id="rightinput" class="input tamanhoinput rightinput" required>
              <option value="" selected disabled>Genero</option>
              <option value="M">Masculino</option>
              <option value="F">Feminino</option>
            </select><br>
            <input type="email" name="email" id="" placeholder="Email" class="input tamanhoinput" required> <br>
            <!-- <input type="password" name="" id="rightinput" placeholder="Confirmar Senha" class="input tamanhoinput rightinput"> -->
          </div>
          <input type="submit" name="cadastrar" value="Cadastrar" class="botao">
        </div>
      </fieldset>
    </form>
  </div>

</body>

</html>