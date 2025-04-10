
<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once '../PHP/config.php';

if (isset($_POST['cadastrar'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $codigo = $_POST['codigo'];
        $nomeEstudante = $_POST['nome'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        $curso = $_POST['curso'];
        $nacionalidade = $_POST['nacionalidade'];
        $provincia = $_POST['provincia'];
        $distrito = $_POST['distrito'];
        $genero = $_POST['genero'];
        $turma = $_POST['turma'];



        try {
             // Verifique se o e-mail já existe no banco de dados
            $sql_check_email = "SELECT * FROM estudanteTBL WHERE email = :email";
            $stmt_check_email = $Conexao->prepare($sql_check_email);
            $stmt_check_email->bindParam(':email', $email);
            $stmt_check_email->execute();

            if ($stmt_check_email->rowCount() > 0) {
                // Se o e-mail já existe, emita uma mensagem de erro ou realize alguma outra ação apropriada
                echo '<div class="ante_sucesso"><div class="sucesso"><center>Este e-mail já está em uso. Por favor, insira um e-mail diferente.!</center></div></div>';
                header('Refresh:1; url= ../Paginas/Listar_estudante.php');
                
            } else {

            $sql = "INSERT INTO estudanteTBL (ID_estudante,nome,contacto, email, nacionalidade, provincia,destrito,genero, curso,turma ) 
                VALUES (:ID_estudante,:nome_estudante,:contacto, :email, :nacionalidade, :provincia, :distrito,:genero, :curso, :turma)";
            $stmt = $Conexao->prepare($sql);
            $stmt->bindParam(':ID_estudante', $codigo);
            $stmt->bindParam(':nome_estudante', $nomeEstudante);
            $stmt->bindParam(':contacto', $tel);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':nacionalidade', $nacionalidade);
            $stmt->bindParam(':provincia', $provincia);
            $stmt->bindParam(':distrito', $distrito);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':curso', $curso);
            $stmt->bindParam(':turma', $turma);

            if ($stmt->execute()) {
                echo '<div class="ante_sucesso"><div class="sucesso"><center>Estudante cadastrado com sucesso!</center></div></div>';
                header('Refresh:1; url= ../Paginas/Cadastro_Estudante.php');
            } else {
                echo '<div class="ante_sucesso"><div class="sucesso"><center>Erro ao cadastrar estudante.</center></div></div>';
                header('Refresh:1; url= ../Paginas/Cadastro_Estudante.php');
            }}
        } catch (PDOException $e) {
            echo '<div class="ante_sucesso"><div class="sucesso"><center>"Erro na conexão: "' . $e->getMessage();
            '</center></div></div>';
            header('Refresh:4; url= ../Paginas/Cadastro_Estudante.php');
        }
    }
}?>


