<?php
require __DIR__ . '/../src/bootstrap.php';

if (usuario() !== null) {
    redirect('dashboard.php');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');
    $resultado = autenticar($email, $senha);
    if ($resultado === 'ok') {
        redirect('dashboard.php');
    }
    $erro = $resultado === 'bloqueado'
        ? 'Demasiadas tentativas. Espere ' . (int) config('bloqueio_minutos', 15) . ' minutos e tente de novo.'
        : 'Email ou senha incorretos.';
}

$semAdmin = (int) db()->query('SELECT COUNT(*) FROM administradores')->fetchColumn() === 0;
$mensagens = flash_ler();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#12263a">
  <title>Entrar | Registo de Sumário</title>
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="pagina-login">
  <div class="login">
    <section class="login-lado">
      <svg viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6.5C10.5 5 8 4.5 4 4.5v13c4 0 6.5.5 8 2 1.5-1.5 4-2 8-2v-13c-4 0-6.5.5-8 2z"/><path d="M12 6.5v13"/></svg>
      <h1>Registo de Sumário</h1>
      <p>Docentes registam as aulas e as presenças. Estudantes acompanham as suas faltas. A administração vê o que foi lecionado e o que ficou por registar.</p>
    </section>
    <section class="login-form">
      <h2>Entrar</h2>
      <p class="suave">Use o email e a senha da sua conta.</p>
      <?php foreach ($mensagens as $m): ?><div class="alerta alerta-<?= e($m['tipo']) ?>"><?= e($m['msg']) ?></div><?php endforeach; ?>
      <?php if ($erro): ?><div class="alerta alerta-erro"><?= e($erro) ?></div><?php endif; ?>
      <form method="post" autocomplete="on">
        <?= csrf_campo() ?>
        <div class="campo"><label for="email">Email</label><input id="email" type="email" name="email" required autofocus autocomplete="username" value="<?= e($_POST['email'] ?? '') ?>"></div>
        <div class="campo"><label for="senha">Senha</label><input id="senha" type="password" name="senha" required autocomplete="current-password"></div>
        <button class="btn btn-bloco" type="submit">Entrar</button>
      </form>
      <?php if ($semAdmin): ?><p class="suave centro"><a href="instalar.php">Primeira utilização? Criar o administrador</a></p><?php endif; ?>
    </section>
  </div>
</body>
</html>
