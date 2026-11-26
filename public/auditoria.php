<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
exigir_login('admin');

$q = trim((string) ($_GET['q'] ?? ''));
$params = [];
$onde = '';
if ($q !== '') {
    $onde = ' WHERE (acao LIKE ? OR entidade LIKE ? OR detalhe LIKE ? OR usuario_id LIKE ?)';
    $params = array_fill(0, 4, "%$q%");
}
$st = db()->prepare("SELECT quando, usuario_tipo, usuario_id, acao, entidade, detalhe, ip FROM auditoria $onde ORDER BY id DESC LIMIT 300");
$st->execute($params);
$linhas = $st->fetchAll();

render_header('Auditoria', 'auditoria.php');
pagina_titulo('Auditoria', 'Últimas 300 ações registadas');
?>
<section class="cartao">
  <form class="busca" method="get" action="auditoria.php">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar ação, tabela ou utilizador" aria-label="Pesquisar"><button class="btn btn-sec" type="submit">Pesquisar</button>
  </form>
  <?php if (!$linhas): ?><p class="vazio">Sem registos.</p><?php else: ?>
  <div class="tabela-wrap"><table class="resp"><thead><tr><th>Quando</th><th>Quem</th><th>Ação</th><th>Onde</th><th>Detalhe</th><th>IP</th></tr></thead><tbody>
    <?php foreach ($linhas as $l): ?>
      <tr><?= td('Quando', e(date('d/m/Y H:i', strtotime($l['quando'])))) . td('Quem', e(trim(($l['usuario_tipo'] ?? '') . ' ' . ($l['usuario_id'] ?? '')))) . td('Ação', e($l['acao'])) . td('Onde', e($l['entidade'])) . td('Detalhe', e($l['detalhe'])) . td('IP', e($l['ip'])) ?></tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</section>
<?php render_footer();
