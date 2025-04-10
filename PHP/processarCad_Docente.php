<?php
include '../PHP/config.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start(); }
// Check if the form is submitted
if(isset($_POST['cadastrar'])){
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $nome = $_POST['nome'];
    $contacto = $_POST['contacto'];
    $email = $_POST['email'];
    $titulo = $_POST['titulo'];
    $formacao = $_POST['formacao'];
    $nacionalidade = $_POST['nacionalidade'];
    $genero = $_POST['genero'];
    $faculdade=$_POST['faculdade'];
    $senha = $_POST['senha'];
    

    // Check if passwords match
    
        // Encrypt the password
        // $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);

        // Insert data into the database
        $sql = "INSERT INTO docenteTBL (ID_docente, nome, contacto, email, grau_academico, formacao, nacionalidade, genero,ID_faculdade, senha)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
        $stmt = $Conexao->prepare($sql);
        $stmt->execute([$codigo, $nome, $contacto, $email, $titulo, $formacao, $nacionalidade, $genero,$faculdade ,$senha]);

        echo'<div class="ante_sucesso"><div class="sucesso"><center>Docente cadastrado com sucesso!</center></div></div>';
         header('Refresh:1; url= ../Paginas/menu.php');
    
}}
?>
