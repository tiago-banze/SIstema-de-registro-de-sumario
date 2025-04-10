<?php
if (isset($_POST['submit'])) {

    //recuperando dados da turma

    include_once "../PHP/config.php";
    session_start();
    // $cursoTurma = $_SESSION['ID_turma'];
    $turmaSelecionada = $_POST['turma'];
    $_SESSION['ID_turma'] = $turmaSelecionada;
    $sqll = "SELECT * FROM turmatbl WHERE ID_turma = :id";
    $stmtt = $Conexao->prepare($sqll);
    $stmtt->bindValue(':id', $turmaSelecionada);
    $stmtt->execute();
    $cursoInf = $stmtt->fetch(PDO::FETCH_ASSOC);

    //recuperando ID do docente para poder pegar o nome dele
    $docente = $_SESSION['ID_docente'];
    $sql_professor = "SELECT * FROM docenteTBL WHERE ID_docente = :id";
    $stmt_professor = $Conexao->prepare($sql_professor);
    $stmt_professor->bindValue(':id', $docente);
    $stmt_professor->execute();
    $docentenInfo = $stmt_professor->fetch(PDO::FETCH_ASSOC);

    $cadeiraSelecionada = $_POST['cadeira'];
    $_SESSION['cadeira'] = $cadeiraSelecionada;
    $sumario = $_POST['sumario'];
    $_SESSION['sumario'] = $sumario;
    $duracao = $_POST['duracao'];
    $_SESSION['duracao'] = $duracao;
    $docente = $docentenInfo['ID_docente'];
    $curso = $cursoInf['curso'];
    $_SESSION['curso'] = $curso;
    echo 'cadeira: ', $cadeiraSelecionada, '/';
    echo 'turma: ', $turmaSelecionada, '/';
    echo  'sumario: ', $sumario, '/';
    echo 'duracao: ', $duracao, '/';
    echo 'curso: ', $curso, '/';
    echo 'docente: ', $docente;

    header("Location: ../Paginas/Faltas.php");
}
// include "../PHP/config.php";
