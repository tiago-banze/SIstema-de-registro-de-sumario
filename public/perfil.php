<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
$u = exigir_login(null, true);
$tabela = TABELAS_USUARIO[$u['tipo']];
$forcado = !empty($_SESSION['trocar_senha']);
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $atual = (string) ($_POST['atual'] ?? '');
    $nova = (string) ($_POST['nova'] ?? '');
    $conf = (string) ($_POST['confirmar'] ?? '');
    $st = db()->prepare("SELECT senha_hash FROM $tabela WHERE id = ?");
    $st->execute([$u['id']]);
    $hash = (string) $st->fetchColumn();
    if (!password_verify($atual, $hash)) {
        $erros[] = 'A senha atual está incorreta.';
    }
    if (strlen($nova) < 8) {
        $erros[] = 'A nova senha deve ter pelo menos 8 caracteres.';
    } elseif (!preg_match('/[A-Za-z]/', $nova) || !preg_match('/\d/', $nova)) {
        $erros[] = 'A nova senha deve ter letras e números.';
    }
    if ($nova !== $conf) {
        $erros[] = 'A confirmação não coincide com a nova senha.';
    }
    if ($nova === $atual && $nova !== '') {
        $erros[] = 'Escolha uma senha diferente da atual.';
    }
    if (!$erros) {
        db()->prepare("UPDATE $tabela SET senha_hash = ?, trocar_senha = 0 WHERE id = ?")
            ->execute([password_hash($nova, PASSWORD_DEFAULT), $u['id']]);
        $_SESSION['trocar_senha'] = false;
        session_regenerate_id(true);
        auditar('trocar_senha', $tabela);
        flash('sucesso', 'Senha alterada com sucesso.');
        redirect('dashboard.php');
    }
}

$st = db()->prepare("SELECT nome, email FROM $tabela WHERE id = ?");
$st->execute([$u['id']]);
$dados = $st->fetch();

render_header('A minha conta', 'perfil.php');
pagina_titulo('A minha conta');
if ($forcado) {
    echo '<div class="alerta alerta-aviso">Por segurança, defina uma senha nova antes de continuar.</div>';
}
?>
<section class="cartao">
  <h2>Dados</h2>
  <p><strong><?= e($dados['nome']) ?></strong></p>
  <p class="suave"><?= e($dados['email']) ?></p>
</section>
<section class="cartao">
  <h2>Alterar senha</h2>
  <?php foreach ($erros as $m): ?><div class="alerta alerta-erro"><?= e($m) ?></div><?php endforeach; ?>
  <form method="post" class="grade-form">
    <?= csrf_campo() ?>
    <div class="campo largo"><label for="atual">Senha atual</label><input id="atual" type="password" name="atual" required autocomplete="current-password"></div>
    <div class="campo"><label for="nova">Nova senha</label><input id="nova" type="password" name="nova" required minlength="8" autocomplete="new-password"><small class="suave">Pelo menos 8 caracteres, com letras e números.</small></div>
    <div class="campo"><label for="confirmar">Repetir a nova senha</label><input id="confirmar" type="password" name="confirmar" required minlength="8" autocomplete="new-password"></div>
    <div class="acoes-form"><button class="btn" type="submit">Guardar senha</button></div>
  </form>
</section>
<?php render_footer();
