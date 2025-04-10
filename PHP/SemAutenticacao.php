
<?php
// // Inicie a sessão
// session_start();

// Verifique se a variável de sessão 'authenticated' não está definida ou é falsa
// if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
//     // O usuário não está autenticado, redirecione-o para a página de login (index.php)
//     header("Location: ../Paginas/index.php"); // Substitua com a URL correta
//     exit();
// }

if (!function_exists("protect")) {
    function protect()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['ID_admin']) || !is_numeric($_SESSION['ID_admin'])) {
            header("Location: ../Paginas/index.php");
        }
    }

    function protectp()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['ID_docente']) || !is_numeric($_SESSION['ID_docente'])) {
            header("Location: ../Paginas/Registro_sumario.php");
        }
    }
}
?>