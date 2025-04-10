<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processamento</title>
    <link rel="icon" href="../icon/open-book_171322.png">
    <link rel="stylesheet" href="../EStilo/Processa.css">
</head>

<body>

    <?php

    if (isset($_POST['submit'])) {
        // ... Código de conexão e preparação da consulta ...
        include_once "../PHP/config.php";
        session_start();
        $Turma = $_SESSION['ID_turma'];
        $ID_cadeira = $_SESSION['cadeira'];
        $sumario = $_SESSION['sumario'];
        $duracao = $_SESSION['duracao'];
        $docente = $_SESSION['ID_docente'];
        $curso = $_SESSION['curso'];

        // Consulta SQL de inserção na tabela
        $sqlsumario = "INSERT INTO sumarioTBL (docente, curso, cadeira, sumario, duracao,turma) 
        VALUES (:docente, :curso, :cadeira, :sumario, :tempo,:turma)";

        // Preparar a consulta
        $stm = $Conexao->prepare($sqlsumario);

        // Substituir os marcadores de posição pelos valores reais
        $stm->bindParam(':docente', $docente, PDO::PARAM_STR); // 
        $stm->bindParam(':curso', $curso, PDO::PARAM_STR); //
        $stm->bindParam(':cadeira', $ID_cadeira, PDO::PARAM_STR);
        $stm->bindParam(':sumario', $sumario, PDO::PARAM_STR);
        $stm->bindParam(':tempo', $duracao, PDO::PARAM_STR);
        $stm->bindParam(':turma', $Turma, PDO::PARAM_STR);
        $stm->execute();

        //insercao da presenca do docente ao registrar sumario
        $sqlpresencaDocente = "INSERT INTO presencaTBL (docente,curso,turma,cadeira)
        VALUES (:docente,:curso,:turma,:cadeira)";
        $stmm = $Conexao->prepare($sqlpresencaDocente);
        $stmm->bindParam(':docente', $docente);
        $stmm->bindParam(':curso', $curso);
        $stmm->bindParam(':turma', $Turma);
        $stmm->bindParam(':cadeira', $ID_cadeira);
        $stmm->execute();


        $sql = "SELECT ID_estudante, nome FROM estudanteTBL WHERE turma = :turma";
        $result = $Conexao->prepare($sql);
        $result->bindParam(':turma', $Turma);
        $result->execute();

        if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $ID_estudante = $row["ID_estudante"];
                $presenca = in_array($ID_estudante, $_POST['faltas']) ? 'Presente' : 'Ausente';

                // ... Código de inserção na tabela presenca_estudanteTBL ...
                $sql = "INSERT INTO presenca_estudanteTBL (ID_estudante, ID_cadeira, presenca) VALUES (:ID_estudante, :ID_cadeira, :presenca)";
                $stmt = $Conexao->prepare($sql);
                $stmt->bindParam(':ID_estudante', $ID_estudante);
                $stmt->bindParam(':ID_cadeira', $ID_cadeira); // Use a variável $ID_cadeira aqui
                // Restante do seu código
                $stmt->bindParam(':presenca', $presenca);
                $stmt->execute();
            }
            echo '<div class="ante_sucesso"><div class="sucesso"><center>Sumario registrado com sucesso!</center></div></div>';
            header('Refresh:1; url= ../Paginas/Registro_sumario.php');
        } else {
            echo "";
        }
    }
    ?>

</body>

</html>