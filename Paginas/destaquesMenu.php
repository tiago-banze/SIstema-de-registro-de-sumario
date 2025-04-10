<?php
include_once '../PHP/config.php'; // Configuração do banco de dados

// Consulta para obter os sumários dos últimos três dias
$dataAtual = date("Y-m-d");
$dataTresDiasAtras = date("Y-m-d", strtotime("-3 days"));
$sql = "SELECT * FROM sumarioTBL WHERE data_registro BETWEEN :dataTresDiasAtras AND :dataAtual"; // Corrigido para 'data_registro'
$stmt = $Conexao->prepare($sql);
$stmt->bindValue(':dataTresDiasAtras', $dataTresDiasAtras);
$stmt->bindValue(':dataAtual', $dataAtual);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='user2'>";
    echo "<h3>Cadeira: {$row['cadeira']}</h3>";
    echo "<p>Sumário: {$row['sumario']}</p>";
    echo "<p>Data: {$row['data_registro']}</p>";
    echo "</div>";
}
