<?php
include_once '../PHP/config.php'; // Configuração do banco de dados


if (isset($_SESSION['ID_admin'])) {
    $idDoAdministrador = $_SESSION['ID_admin'];

    $sql = "SELECT nome FROM administradorTBL WHERE ID_admin = :id";
    $stmt = $Conexao->prepare($sql);
    $stmt->bindValue(':id', $idDoAdministrador);
    $stmt->execute();
    $adminInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($adminInfo) {
        $_SESSION['nome'] = $adminInfo['nome'];
        echo "<h4><strong>{$adminInfo['nome']}<strong></h4>";
        // echo "<p>Hoje é dia " . date("d/m/Y") . "</p>";
    } else {
        echo "<p>Informações do administrador não encontradas.</p>";
    }
} else {
    echo "<p>Usuário não autenticado.</p>";
}
