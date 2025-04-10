<?php
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();


// $dados_presenca_mes = array(25, 23, 22, 24, 26, 21);

?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../EStilo/menu.css">
  <link rel="stylesheet" href="">
  <link rel="stylesheet" href="../EStilo/NavBar.css">
  <!-- <link rel="stylesheet" href="../EStilo/universai_user.css"> -->
  <link rel="icon" href="../icon/open-book_171322.png">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="cima"></div>
  <ul class="sidenav">
    <li><a class="active" href="../Paginas/menu.php">Home</a></li>

    <li>
      <div class="dropdown">
        <center><button class="dropbtn">Cadastro</button></center>
        <div class="dropdown-content">
          <center> <a href="../Paginas/Cadastro_Curso.php">Cursos</a>
            <a href="../Paginas/Cadatro_UC.php">UC</a>
            <a href="../Paginas/Cadastro_Estudante.php">Estudantes</a>
            <a href="../Paginas/Cadastro_Docente.php">Docentes</a>
          </center>
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

    </div>

    <div class="destaque">
      <?php
      include_once '../Paginas/destaquesMenu.php';
      ?>

    </div>
    <div style="width: 50%">
      <canvas id="tempoCadeiraDocenteChart"></canvas>
    </div>

    <script>
      // Dados simulados do banco de dados
      const dadosDinamicos = <?php
                              // Simulação de consulta ao banco de dados
                              $conexao = new mysqli("localhost", "nome_de_usuario", "senha", "registro_sumario");
                              if ($conexao->connect_error) {
                                die("Conexão falhou: " . $conexao->connect_error);
                              }

                              $sql = "SELECT nome, hora_semestral FROM cadeiratbl";
                              $result = $conexao->query($sql);

                              $dados = array();
                              if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                  $dados[$row["nome"]] = $row["hora_semestral"];
                                }
                              } else {
                                echo "0 resultados";
                              }
                              echo json_encode($dados);
                              $conexao->close();
                              ?>;

      // Convertendo os dados do PHP em dados JavaScript
      const labels = Object.keys(dadosDinamicos);
      const data = Object.values(dadosDinamicos);

      // Definindo cores vivas para o gráfico
      const backgroundColors = ['#FF5733', '#C70039', '#900C3F', '#581845', '#FFC300', '#DAF7A6'];

      // Configurações do gráfico
      const config = {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Horas Semestrais',
            data: data,
            backgroundColor: backgroundColors,
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Horas'
              }
            }
          }
        }
      };

      // Criação do gráfico de colunas
      var ctx = document.getElementById('tempoCadeiraDocenteChart').getContext('2d');
      var myChart = new Chart(ctx, config);
    </script>
    <!-- Gráfico de barras -->
  </div>

</body>

</html>