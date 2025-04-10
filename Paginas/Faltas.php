<?php
include_once "../PHP/config.php";
session_start();
$Turma = $_SESSION['ID_turma']
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faltas</title>
    <link rel="stylesheet" href="../EStilo/Faltas.css">
    <link rel="icon" href="../icon/open-book_171322.png">
</head>

<body>
    <form action="../PHP/processarFaltas.php" method="post">
        <fieldset>
            <legend>
                <h2>Controle de Faltas</h2>
            </legend>
            <div class="turma">
                <h3>
                    <center>turma <?php echo $Turma; ?></center>
                </h3>
            </div>
            <div class="pai">
                <table>
                    <div class="STACY">
                        <th class="th">
                            <h3>Código de Estudante</h3>
                        </th>
                        <th class="th th2">
                            <h3>Nome do Estudante</h3>
                        </th>
                        <th class="th th2">
                            <h3>Faltas</h3>
                        </th>
                    </div>
                    <hr>
                    <?php
                    // Consulta para buscar os estudantes
                    $sql = "SELECT ID_estudante, nome FROM estudanteTBL WHERE turma = :turma";
                    $result = $Conexao->prepare($sql);
                    $result->bindParam(':turma', $Turma);
                    $result->execute();

                    if ($result->rowCount() > 0) {
                        // Saída de dados de cada linha
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . $row["ID_estudante"] . "</td>";
                            echo "<td name='nome'>" . $row["nome"] . " </td>";
                            echo '<td class="right"><input type="checkbox" name="faltas[]" value="' . $row["ID_estudante"] . '" ></td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "";
                    }
                    ?>
                </table>
                <br><br><b></b>
            </div>
            <div class="t">
                <input type="submit" value="Finalizar" class="botao" name="submit">
                <a style="text-decoration: none; color:" id="Voltar" href="../Paginas/Registro_sumario.php">Voltar</a>
            </div>
        </fieldset>
    </form>
</body>

</html>