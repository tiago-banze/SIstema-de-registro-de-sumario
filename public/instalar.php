<?php
require __DIR__ . '/../src/bootstrap.php';

// Só funciona enquanto não existir nenhum administrador.
if ((int) db()->query('SELECT COUNT(*) FROM administradores')->fetchColumn() > 0) {
    redirect('index.php');
}

$erros = [];
$nome = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $nome = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');
    if ($nome === '') { $erros[] = 'Indique o nome.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $erros[] = 'Email inválido.'; }
    if (strlen($senha) < 8) { $erros[] = 'A senha deve ter pelo menos 8 caracteres.'; }
    if (!$erros) {
        db()->prepare('INSERT INTO administradores (nome, email, senha_hash) VALUES (?, ?, ?)')
            ->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
        flash('sucesso', 'Administrador criado. Já pode entrar.');
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Primeira utilização | Registo de Sumário</title>
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="pagina-login">
  <div class="login login-simples">
    <section class="login-form">
      <h2>Criar o administrador</h2>
      <p class="suave">Esta página só aparece enquanto não existir nenhum administrador.</p>
      <?php foreach ($erros as $m): ?><div class="alerta alerta-erro"><?= e($m) ?></div><?php endforeach; ?>
      <form method="post">
        <?= csrf_campo() ?>
        <div class="campo"><label for="nome">Nome</label><input id="nome" name="nome" required value="<?= e($nome) ?>"></div>
        <div class="campo"><label for="email">Email</label><input id="email" type="email" name="email" required value="<?= e($email) ?>"></div>
        <div class="campo"><label for="senha">Senha (mínimo 8 caracteres)</label><input id="senha" type="password" name="senha" required minlength="8" autocomplete="new-password"></div>
        <button class="btn btn-bloco" type="submit">Criar administrador</button>
      </form>
    </section>
  </div>
</body>
</html>
