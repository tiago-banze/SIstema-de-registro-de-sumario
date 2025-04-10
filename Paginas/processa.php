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
    include_once '../PHP/config.php';
    try {
        if (isset($_POST["cadastrar"])) {
                // var_dump($_POST);
                $codigoCurso = $_POST['codigo_curso'];
                $nomeCurso = $_POST['nome_curso'];
                $diretor = $_POST['diretor'];
                $faculdade = $_POST['faculdade'];
            
                // Inserir os dados no banco de dados
                $sql = "INSERT INTO cursoTBL (ID_curso, nome, diretor,ID_faculdade) VALUES (:codigo_curso, :nome_curso,:diretor, :faculdade)";
                $stmt = $Conexao->prepare($sql);
                $stmt->bindParam(':codigo_curso', $codigoCurso);
                $stmt->bindParam(':nome_curso', $nomeCurso);
                $stmt->bindParam(':diretor', $diretor);
                $stmt->bindParam(':faculdade', $faculdade);
            
                if ($stmt->execute()) {
                    echo'<div class="ante_sucesso"><div class="sucesso"><center>Curso cadastrado com sucesso!</center></div></div>';
                    header('Refresh:1; url= Lista_Curso.php');
                    // echo "Curso cadastrado com sucesso!";
                } else {
                    echo'<div class="ante_sucesso"><div class="sucesso"><center>ERRO ao cadastrado  Curso!</center></div></div>';
                    header('Refresh:4; url= Cadastro_Curso.php');
                }
            }
                
        } catch (PDOException $e) {
        echo'<div class="ante_sucesso"><div class="sucesso"><center>"Erro na conexão: "'. $e->getMessage(); '</center></div></div>';
        header('Refresh:4; url= Cadastro_Curso.php');
    } 

    // <!-- ......................................................... -->

    // ... Configurações de conexão e cabeçalho ...

?>

</body>
</html>