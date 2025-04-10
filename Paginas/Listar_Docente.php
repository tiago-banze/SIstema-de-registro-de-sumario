<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// ... Configurações de conexão e cabeçalho ...
include_once '../PHP/config.php';
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();

// Consulta para buscar cursos
$sql = "SELECT * FROM docentetbl";
$stmt = $Conexao->prepare($sql);
$stmt->execute();
$docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>listar Docentes</title>
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/universai_user.css">
  <link rel="stylesheet" href="../EStilo/Lista_Estudante.css">
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
          <a href="../Paginas/Listar_estudante.php">Docentes</a>
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
          <option value="" selected disabled>Selecione uma Faculdade</option>
          <option value="">Faculdade</option>
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
    </div>
    <form action="" method="post">
      <fieldset>
        <legend>
          <h2>Sobre Docente</h2>
        </legend>
        <div class="pai">
          <?php if (count($docentes) === 0) { ?>
            <p>Não há docentes cadastrados.</p>
          <?php } else { ?>
            <table>
              <div class="STACY">
                <th class="th">
                  <h3>Código de Docente</h3>
                </th>
                <th class="th th2">
                  <h3>Nome do Docente</h3>
                </th>
                <th class="th th2">
                  <h3>Email</h3>
                </th>
                <th class="th th2">
                  <h3>grau academico</h3>
                </th>
                <th class="th th2">
                  <h3>acção</h3>
                </th>
              </div>
              <?php foreach ($docentes as $docente) { ?>
                <hr>
                <tr>
                  <td><?php echo $docente['ID_docente']; ?></td>
                  <td><?php echo $docente['nome']; ?></td>
                  <td><?php echo $docente['email']; ?></td>
                  <td><?php echo $docente['grau_academico']; ?></td>
                  <td class="accao">
                    <center>
                      <input class="button azul" type="button" value="atualizar">
                      <input class="button vermelho" type="button" value="apagar">
                    </center>
                  </td>

                </tr>
              <?php } ?>
            </table>
          <?php } ?><br><br><b></b>
        </div>
        <!-- <div class="t">
            <input type="submit" value="Finalizar" class="botao">
        </div> -->
      </fieldset>
    </form>
    <a href="../Paginas/Cadastro_Docente.php" class="adicionar"><img src="../icon/adicionar.png" alt="icon" style="width: 50px; height: 50px;"></a>
  </div>

</body>

</html>