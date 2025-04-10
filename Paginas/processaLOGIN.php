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
    include_once("../PHP/SemAutenticacao.php");
    protect();
    protectp();
    // $_SESSION['authenticated'] = true;
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    include_once '../PHP/config.php';

    // Verificação das credenciais de login
    if (isset($_POST["submeter"])) {
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];

        // Consulta no banco de dados para administradores
        $sql_admin = "SELECT * FROM administradorTBL WHERE email = :email AND senha = :senha";
        $stmt_admin = $Conexao->prepare($sql_admin);
        $stmt_admin->bindParam(':email', $usuario);
        $stmt_admin->bindParam(':senha', $senha);
        $stmt_admin->execute();

        $resultado_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        if ($resultado_admin) {
            // print_r($resultado_admin);
            $_SESSION['ID_admin'] = $resultado_admin['ID_admin']; // Definindo a variável de sessão
            header("Location: menu.php");
            exit();
        }

        // Consulta no banco de dados para professores
        $sql_professor = "SELECT * FROM docenteTBL WHERE email = :email AND senha = :senha";
        $stmt_professor = $Conexao->prepare($sql_professor);
        $stmt_professor->bindParam(':email', $usuario);
        $stmt_professor->bindParam(':senha', $senha);
        $stmt_professor->execute();

        $resultado_professor = $stmt_professor->fetch(PDO::FETCH_ASSOC);

        if ($resultado_professor) {
            $_SESSION['ID_docente'] = $resultado_professor['ID_docente'];
            // Login de professor com sucesso, redirecionar para página de registro de sumário
            header("Location: Registro_sumario.php");
            exit();
        }

        // Credenciais inválidas para ambos os tipos de usuário, mostrar mensagem de erro
        // echo "Credenciais inválidas.";
        echo '<div class="ante_sucesso"><div class="sucesso"><center>Credenciais inválidas!</center></div></div>';
        header('Refresh:1; url= ../Paginas/index.php');
        // include"../Paginas/index.php";
    }
    ?>
</body>

</html>