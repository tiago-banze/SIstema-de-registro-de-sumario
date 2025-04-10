<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// ... Configurações de conexão e cabeçalho ...
include_once '../PHP/config.php';
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();

// Consulta para buscar cadeiras
$sql = "SELECT * FROM presencaTBL";
$stmt = $Conexao->prepare($sql);
$stmt->execute();
$docente = $stmt->fetchAll(PDO::FETCH_ASSOC); // Corrigido para $cadeiras


?>


<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>listar presenca de docentes</title>
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/Lista_Estudante.css">
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
      <!-- <div class="selecao">
        <select name="" id="">
          <option value="" selected disabled>Selecione a uma data especifica</option>
          <option value="">10/10/2023</option>
        </select>
      </div> -->
      <div class="pesquisa">
        <form action="" method="GET">
          <p>
            <span><input name="search" placeholder="Pesquisar" type="text"></span>
            <span><button type="submit">Buscar</button></span>
          </p>
        </form>
      </div>
      <!-- <img src="../icon/ui_user_profile_person_icon_208878.png" style="width: 40px; height:40px" alt=""> -->
    </div>

    <form action="" method="post">
      <fieldset>
        <legend>
          <h2>Lista de presenca dos Docentes</h2>
        </legend>
        <div class="pai">
          <?php if (count($docente) === 0) { ?>
            <p>Não há estudantes cadastrados.</p>
          <?php } else { ?>
            <table>
              <div class="STACY">
                <th class="th">
                  <h3>Docente</h3>
                </th>
                <th class="th th2">
                  <h3>Curso</h3>
                </th>
                <th class="th th2">
                  <h3>turma</h3>
                </th>
                <th class="th th2">
                  <h3>data de presenca</h3>
                </th>
              </div>
              <hr>
              <?php foreach ($docente as $docentes) {
                // Consulta para buscar nome do docente
                $sql_docente = "SELECT nome FROM docenteTBL WHERE ID_docente = :id_docente";
                $stmt_docente = $Conexao->prepare($sql_docente);
                $stmt_docente->bindValue(':id_docente', $docentes['docente']); // Substitua com a chave primária correta do docente
                $stmt_docente->execute();
                $nome_docente = $stmt_docente->fetchColumn(); // Supondo que 'nome' é o nome da coluna na tabela

              ?>
                <tr>
                  <td><?php echo $nome_docente; ?></td>
                  <td><?php echo $docentes['curso']; ?></td>
                  <td><?php echo $docentes['turma']; ?></td>
                  <td><?php echo $docentes['data_presenca']; ?></< /td>
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
  </div>

</body>

</html>