<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../EStilo/login.css">
    <link rel="icon" href="../icon/open-book_171322.png">
</head>
<body>
    <div class="pai">
        <div class="left">
            <center>
                <h1>Bem Vindo!</h1>
            </center>
            <img src="../icon/open-book_171322.png" alt="">
        </div>
        <div class="right">
            <center>
                <h1>User Authentication</h1>
            </center>
            <form action="../Paginas/processaLOGIN.php" method="POST">
                <input type="email" name="usuario" id="" placeholder="Usuario" class="autenticao" required><br>
                <input type="password" name="senha" id="" placeholder="Senha" class="autenticao" required><br>
                <center>
                    <p>Esqueceu a senha? <a href="">Clique aqui</a></p>
                </center>
                <input type="submit" id="submit" name="submeter" onclick="mudar_cor()" value="Entrar" class="botao">
            </form>
        </div>
    </div>
    <!-- <script src="/Paginas/interatividade.js"></script> -->
</body>
</html>