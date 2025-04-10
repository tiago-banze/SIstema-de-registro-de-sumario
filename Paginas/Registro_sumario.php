<?php
include '../PHP/config.php';


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo_Sumário</title>
    <link rel="stylesheet" href="../EStilo/Registo_sumario.css">
    <link rel="icon" href="../icon/open-book_171322.png">
</head>

<body>
    <form action="../PHP/ProcessaSumario.php" method="post">
        <fieldset>
            <legend>
                <h2>Registo de Sumário</h2>
            </legend>
            <div class="pai">
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
                <select name="cadeira" class=" class input tamanhoinput" required>
                    <option value="" selected disabled>Selecione a Cadeira</option>
                    <?php
                    $queryCadeiras = "SELECT * FROM cadeiraTBL";
                    $stmtCadeiras = $Conexao->query($queryCadeiras);
                    while ($cadeira = $stmtCadeiras->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $cadeira['ID_cadeira'] . "'>" . $cadeira['nome'] . "</option>";
                    }
                    ?>
                </select> <br>
                <input type="text" name="sumario" placeholder="Sumário" class="input tamanhoinput" required><br>
                <select name="duracao" class="class input tamanhoinput" required>
                    <option value="" selected disabled>Selecione a duração de tempo</option>
                    <option value="1 hora">1 hora</option>
                    <option value="2 horas">2 horas</option>
                    <option value="3 horas">3 horas</option>
                    <option value="4 horas">4 horas</option>
                </select><br>
                <input type="submit" name="submit" value="Proximo" class="botao">
            </div>
        </fieldset>
    </form>
    <fieldset class="fieldset">
        <div class="data">
            <center>
                <h2><?php echo date('d \d\e F \d\e Y'); ?></h2>
            </center>
        </div>
    </fieldset>
</body>

</html>
<?php
// include_once'../PHP/ProcessaSumario.php';
?>