<?php
include_once '../PHP/config.php';
// include_once('../PHP/SemAutenticacao.php');
// protectp();
// include_once'../PHP/inactividade.php';
session_start();

try {
    if (isset($_SESSION['ID_docente'])) {
        //recuperando ID do docente para poder pegar o nome dele
        $docente = $_SESSION['ID_docente'];
        $sql_professor = "SELECT * FROM docenteTBL WHERE ID_docente = :id";
        $stmt_professor = $Conexao->prepare($sql_professor);
        $stmt_professor->bindValue(':id', $docente);
        $stmt_professor->execute();
        $docentenInfo = $stmt_professor->fetch(PDO::FETCH_ASSOC);

        if ($docentenInfo) {
            if (isset($_POST['submit'])) {

                $cursoTurma = $_SESSION['ID_turma'];
                $sqll = "SELECT * FROM turmatbl WHERE ID_turma = :id";
                $stmtt = $Conexao->prepare($sqll);
                $stmtt->bindValue(':id', $cursoTurma);
                $stmtt->execute();
                $cursoInf = $stmtt->fetch(PDO::FETCH_ASSOC);

                $cadeiraSelecionada = $_POST['cadeira'];
                $turmaSelecionada = $_POST['turma'];
                $sumario = $_POST['sumario'];
                $duracao = $_POST['duracao'];
                $docente = $docentenInfo['ID_docente'];
                $curso = $cursoInf['curso'];
                // $turmaSelecionada = $_POST['turma_id'];

                // Consulta SQL de inserção na tabela
                $sql = "INSERT INTO sumarioTBL (docente, curso, cadeira, sumario, duracao,turma) 
                VALUES (:docente, :curso, :cadeira, :sumario, :tempo,:turma)";

                // Preparar a consulta
                $stm = $Conexao->prepare($sql);

                // Substituir os marcadores de posição pelos valores reais
                $stm->bindParam(':docente', $docente, PDO::PARAM_STR); // 
                $stm->bindParam(':curso', $curso, PDO::PARAM_STR); //
                $stm->bindParam(':cadeira', $cadeiraSelecionada, PDO::PARAM_STR);
                $stm->bindParam(':sumario', $sumario, PDO::PARAM_STR);
                $stm->bindParam(':tempo', $duracao, PDO::PARAM_STR);
                $stm->bindParam(':turma', $turmaSelecionada, PDO::PARAM_STR);

                // Executar a consulta
                if ($stm->execute()) {
                    header('Refresh: 1; url=../Paginas/Faltas.php');
                } else {
                    echo "Erro ao inserir o sumário.";
                }
            }
        } else {
        }
    } else {
        echo "<p>Usuário não autenticado.</p>";
    }
} catch (PDOException $e) {
    // Em caso de erro, capture a exceção e exiba uma mensagem de erro
    echo "Erro: " . $e->getMessage();
}

$dataAtual = date('Y-m-d');
