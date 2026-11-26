<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/agenda.php';
$u = exigir_login('estudante');
$periodo = periodo_ativo();

render_header('As minhas presenças', 'minhas_presencas.php');
pagina_titulo('As minhas presenças', $periodo ? $periodo['nome'] : '');
if (!$periodo) {
    echo '<div class="alerta alerta-aviso">Não há nenhum período letivo ativo.</div>';
    render_footer();
    exit;
}

$uc = (string) ($_GET['uc'] ?? '');
$resumo = assiduidade($periodo, ['estudante_id' => $u['id']]);
$ucs = [];
foreach ($resumo as $l) {
    $ucs[$l['uc']] = $l['uc'];
}

$onde = ['p.estudante_id = ?', 'a.periodo_id = ?', "s.estado <> 'rejeitado'"];
$params = [$u['id'], $periodo['id']];
if ($uc !== '' && isset($ucs[$uc])) {
    $onde[] = 'ucs.nome = ?';
    $params[] = $uc;
}
$st = db()->prepare(
    'SELECT s.data_aula, s.sumario, ucs.nome AS uc, p.estado
       FROM presencas p JOIN sumarios s ON s.id = p.sumario_id JOIN atribuicoes a ON a.id = s.atribuicao_id JOIN ucs ON ucs.id = a.uc_id
      WHERE ' . implode(' AND ', $onde) . ' ORDER BY s.data_aula DESC, s.id DESC LIMIT 300'
);
$st->execute($params);
$linhas = $st->fetchAll();
?>
<section class="cartao">
  <form class="filtros" method="get" action="minhas_presencas.php">
    <div class="campo"><label for="uc">Disciplina</label><select id="uc" name="uc"><option value="">Todas</option>
      <?php foreach ($ucs as $nome): ?><option value="<?= e($nome) ?>"<?= $nome === $uc ? ' selected' : '' ?>><?= e($nome) ?></option><?php endforeach; ?></select></div>
    <button class="btn btn-sec" type="submit">Filtrar</button>
  </form>
  <?php if (!$linhas): ?><p class="vazio">Ainda não há aulas registadas.</p><?php else: ?>
  <div class="tabela-wrap"><table class="resp"><thead><tr><th>Data</th><th>Disciplina</th><th>Conteúdo da aula</th><th>Presença</th></tr></thead><tbody>
    <?php foreach ($linhas as $l): ?>
      <tr><?= td('Data', e(data_pt($l['data_aula']))) . td('Disciplina', e($l['uc'])) . td('Conteúdo', e($l['sumario']), 'texto-longo') . td('Presença', badge_estado($l['estado'])) ?></tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</section>
<?php render_footer();
