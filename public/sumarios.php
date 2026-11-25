<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
$u = exigir_login();
$ehAdmin = $u['tipo'] === 'admin';

$de = (string) ($_GET['de'] ?? date('Y-m-d', strtotime('-30 days')));
$ate = (string) ($_GET['ate'] ?? date('Y-m-d'));
$q = trim((string) ($_GET['q'] ?? ''));

$onde = ['s.data_registro BETWEEN ? AND ?'];
$params = [$de, $ate];
if (!$ehAdmin) {
    $onde[] = 's.docente_id = ?';
    $params[] = $u['id'];
}
if ($q !== '') {
    $onde[] = '(s.sumario LIKE ? OR uc.nome LIKE ? OR d.nome LIKE ? OR s.turma_id LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
$st = db()->prepare(
    'SELECT s.id, s.data_registro, s.turma_id, s.sumario, s.duracao_horas, uc.nome AS uc, d.nome AS docente,
            (SELECT COUNT(*) FROM presencas_estudante p WHERE p.sumario_id = s.id AND p.presente = 1) AS presentes,
            (SELECT COUNT(*) FROM presencas_estudante p WHERE p.sumario_id = s.id) AS total
       FROM sumarios s JOIN ucs uc ON uc.id = s.uc_id JOIN docentes d ON d.id = s.docente_id
      WHERE ' . implode(' AND ', $onde) . ' ORDER BY s.data_registro DESC, s.id DESC LIMIT 200'
);
$st->execute($params);
$linhas = $st->fetchAll();

// Detalhe de presenças de um sumário
$detalhe = null;
if (isset($_GET['ver'])) {
    foreach ($linhas as $l) {
        if ((string) $l['id'] === (string) $_GET['ver']) {
            $d = db()->prepare('SELECT e.id, e.nome, p.presente FROM presencas_estudante p JOIN estudantes e ON e.id = p.estudante_id WHERE p.sumario_id = ? ORDER BY e.nome');
            $d->execute([$l['id']]);
            $detalhe = ['sumario' => $l, 'alunos' => $d->fetchAll()];
        }
    }
}

render_header($ehAdmin ? 'Sumários' : 'Meus sumários', 'sumarios.php');
pagina_titulo($ehAdmin ? 'Sumários' : 'Meus sumários', count($linhas) . ' registo(s) no período',
    $ehAdmin ? '' : '<a class="btn" href="sumario_novo.php">Registar sumário</a>');

if ($detalhe) {
    $s = $detalhe['sumario'];
    echo '<section class="cartao"><h2>' . e($s['uc']) . ' <span class="badge">' . e($s['turma_id']) . '</span></h2>';
    echo '<p class="suave">' . e(data_pt($s['data_registro'])) . ' - ' . e($s['docente']) . ' - ' . (int) $s['duracao_horas'] . ' h</p><p>' . e($s['sumario']) . '</p>';
    echo '<div class="tabela-wrap"><table><thead><tr><th>Código</th><th>Estudante</th><th>Presença</th></tr></thead><tbody>';
    foreach ($detalhe['alunos'] as $a) {
        echo '<tr><td>' . e($a['id']) . '</td><td>' . e($a['nome']) . '</td><td>' . badge((bool) $a['presente']) . '</td></tr>';
    }
    echo '</tbody></table></div></section>';
}
?>
<section class="cartao">
  <form class="filtros" method="get" action="sumarios.php">
    <div class="campo"><label for="de">De</label><input id="de" type="date" name="de" value="<?= e($de) ?>"></div>
    <div class="campo"><label for="ate">Até</label><input id="ate" type="date" name="ate" value="<?= e($ate) ?>"></div>
    <div class="campo"><label for="q">Pesquisar</label><input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="UC, docente, turma ou texto"></div>
    <button class="btn btn-sec" type="submit">Filtrar</button>
  </form>
  <?php if (!$linhas): ?>
    <p class="vazio">Nenhum sumário neste período.</p>
  <?php else: ?>
    <div class="tabela-wrap"><table>
      <thead><tr><th>Data</th><th>UC</th><th>Turma</th><?php if ($ehAdmin): ?><th>Docente</th><?php endif; ?><th>Sumário</th><th>Horas</th><th>Presentes</th><th class="col-acoes"></th></tr></thead>
      <tbody>
      <?php foreach ($linhas as $l): ?>
        <tr><td><?= e(data_pt($l['data_registro'])) ?></td><td><?= e($l['uc']) ?></td><td><?= e($l['turma_id']) ?></td>
          <?php if ($ehAdmin): ?><td><?= e($l['docente']) ?></td><?php endif; ?>
          <td class="texto-longo"><?= e($l['sumario']) ?></td><td><?= (int) $l['duracao_horas'] ?></td>
          <td><?= (int) $l['presentes'] ?>/<?= (int) $l['total'] ?></td>
          <td class="col-acoes"><a class="btn btn-peq btn-sec" href="sumarios.php?de=<?= e($de) ?>&amp;ate=<?= e($ate) ?>&amp;q=<?= e(rawurlencode($q)) ?>&amp;ver=<?= (int) $l['id'] ?>">Detalhes</a></td></tr>
      <?php endforeach; ?>
      </tbody></table></div>
  <?php endif; ?>
</section>
<?php render_footer();
