<?php
include "../PHP/config.php";
include_once('../PHP/SemAutenticacao.php');
protect();
include_once '../PHP/inactividade.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start(); // Iniciar sessão apenas se não estiver ativa
}


if (isset($_GET['search'])) {
  $search = $_GET['search'];

  $selectStmt = $Conexao->prepare("SELECT * FROM estudanteTBL WHERE ID_estudante=:id");
  $selectStmt->bindParam(':id', $search);
  $selectStmt->execute();
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../EStilo/search.css">
  <link rel="stylesheet" href="../EStilo/menu.css">
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/universai_user.css">
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
        <button class="dropbtn">Listar</button>
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
        <button class="dropbtn">Faltas</button>
        <div class="dropdown-content">
          <a href="#">Estudantes</a>
          <a href="#">Docentes</a>
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
      <img src="../icon/ui_user_profile_person_icon_208878.png" style="width: 40px; height:40px" alt="">
    </div>


    <div class="container"><br><br>
      <h1>Listagem de Estudante</h1>
      <div class="docente-list">
        <!-- Replace this section with PHP to loop through and display docente data -->
        <div class="docente-card">
          <?php
          if (!empty($selectStmt)) {
            echo "<h2>Resultados da pesquisa:</h2>";
            foreach ($selectStmt as $student) { ?>
              <h2> <?php echo $student['nome']; ?> </h2>
              <p><strong>ID:</strong> <?php echo $student['ID_estudante'] ?></p><br>
              <p><strong>Contacto:</strong> <?php echo $student['contacto'] ?></p><br>
              <p><strong>Email:</strong> <?php echo $student['email'] ?></p><br>
              <p><strong>Nacionalidade:</strong> <?php echo $student['nacionalidade'] ?></p><br>
              <p><strong>Destrito:</strong> <?php echo $student['destrito'] ?></p><br>
              <p><strong>Género:</strong> <?php echo $student['genero'] ?></p><br>
              <p><strong>Curso:</strong> <?php echo $student['curso'] ?></p>
              <button class="delete-button" data-id="ID123456">Delete</button>
          <?php }
          } elseif (isset($_GET['search'])) {
            echo "Nenhum resultado encontrado.";
          }
          ?>
        </div>
        <!-- End of PHP loop -->
      </div>
    </div>
  </div>
  </div>

</body>

</html>