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
$sql = "SELECT * FROM presenca_estudanteTBL";
$stmt = $Conexao->prepare($sql);
$stmt->execute();
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC); // Corrigido para $cadeiras

?>


<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>listar de presenca dos Estudantes</title>
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <link rel="stylesheet" href="../EStilo/Lista_Estudante.css">
  <!-- <link rel="stylesheet" href="../EStilo/universai_user.css"> -->
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
          <option value="" selected disabled>Selecione a turma</option>
          <option value="">EIT22</option>
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
      <!-- <img src="../icon/ui_user_profile_person_icon_208878.png" style="width: 40px; height:40px" alt=""> -->
    </div>

    <form action="" method="post">
      <fieldset>
        <legend>
          <h2>Lista de presenca de Estudantes</h2>
        </legend>
        <div class="pai">
          <?php if (count($estudantes) === 0) { ?>
            <p>Não há estudantes cadastrados.</p>
          <?php } else { ?>
            <table>
              <div class="STACY">
                <th class="th">
                  <h3>Código de Estudante</h3>
                </th>
                <th class="th th2">
                  <h3>Nome do Estudante</h3>
                </th>
                <th class="th th2">
                  <h3>cadeira</h3>
                </th>
                <th class="th th2">
                  <h3>Turma</h3>
                </th>
                <th class="th th2">
                  <h3>data da aula</h3>
                </th>
                <th class="th th2">
                  <h3>Presenca</h3>
                </th>

              </div>
              <hr>
              <?php
              foreach ($estudantes as $estudante) {
                // $xxx = $estudante['ID_estudante'];

                // $sql_estudante_info = "SELECT nome FROM estudanteTBL WHERE ID_estudante = :id_estudante";
                // $stmt_estudante_info = $Conexao->prepare($sql_estudante_info);
                // $stmt_estudante_info->bindParam(':id_estudante', $xxx);

                // if ($stmt_estudante_info->execute()) {
                //   $estudante_info = $stmt_estudante_info->fetchAll(PDO::FETCH_ASSOC);

                //   if ($estudante_info) {
                //     $nome_estudante = $estudante_info['nome'];
                //     $turma_estudante = $estudante_info['turma'];

                //     // Restante do seu código de exibição da tabela
                //     // Certifique-se de que o acesso aos arrays esteja dentro deste bloco if
                //   } else {
                //     // Se nenhum resultado for encontrado, defina os valores como vazios ou trate conforme necessário
                //     $nome_estudante = "";
                //     $turma_estudante = "";
                //   }
                // } else {
                //   // Se ocorrer um erro na execução da consulta, trate conforme necessário
                //   echo "Erro ao executar a consulta.";
                // }




              ?>


                <tr>
                  <td><?php echo $estudante['ID_estudante']; ?></td>
                  <td><?php  ?></td>
                  <td><?php echo $estudante['ID_cadeira']; ?></td>
                  <td><?php  ?></< /td>
                  <td><?php echo $estudante['data_presenca']; ?></td>
                  <td><?php echo $estudante['presenca']; ?></td>

                </tr>
              <?php } ?>
            </table>
          <?php } ?><br><br><b></b>
        </div>
      </fieldset>
    </form>
  </div>

</body>

</html>