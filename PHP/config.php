<?php
    $dbHost = "localhost"; // Correção aqui
    $dbUsername = "root";
    $dbSenha = "";
    $dbName = "REGISTRO_SUMARIO";
    $porta = 3306;
    try {
    // Conexão com a porta
    $Conexao = new PDO("mysql:host=$dbHost;port=$porta;dbname=$dbName", $dbUsername, $dbSenha); 
    // Conexão sem a porta
   // $conexao = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbSenha); 
    $Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
?>
