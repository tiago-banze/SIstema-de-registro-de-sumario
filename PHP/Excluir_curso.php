
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// ... Configurações de conexão ...
include_once '../PHP/config.php';

if (isset($_GET['d']) && !empty($_GET['d'])) {
  $cursoId = $_GET['d'];
    // Consulta para excluir o curso
    $sql = "DELETE FROM cursoTBL WHERE ID_curso = :cursoId";
    $stmt = $Conexao->prepare($sql);
    $stmt->bindValue(':cursoId', $cursoId, PDO::PARAM_STR);
    echo $cursoId;
    try {
        $stmt->execute();
        header("Location: Lista_Curso.php"); // Redireciona para a página de listagem após excluir
        exit();
    } catch (PDOException $e) {
        echo 'Erro ao excluir o curso: ' . $e->getMessage();
    }
} else {
    echo 'ID do curso não fornecido.';
}
?>

