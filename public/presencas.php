<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
exigir_login('admin');

$tipo = ($_GET['tipo'] ?? 'estudantes') === 'docentes' ? 'docentes' : 'estudantes';
$de = (string) ($_GET['de'] ?? date('Y-m-d', strtotime('-30 days')));
$ate = (string) ($_GET['ate'] ?? date('Y-m-d'));
$turma = (string) ($_GET['turma'] ?? '');
$uc = (string) ($_GET['uc'] ?? '');

$turmas = db()->query('SELECT id FROM turmas ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
$ucs = db()->query('SELECT id, nome FROM ucs ORDER BY nome')->fetchAll(PDO::FETCH_KEY_PAIR);

$onde = ['s.data_registro BETWEEN ? AND ?'];
$params = [$de, $ate];
if ($turma !== '') { $onde[] = 's.turma_id = ?'; $params[] = $turma; }
if ($uc !== '')    { $onde[] = 's.uc_id = ?';    $params[] = $uc; }
$filtro = implode(' AND ', $onde);

if ($tipo === 'estudantes') {
    $st = db()->prepare(
        "SELECT s.data_registro, e.id, e.nome, s.turma_id, u.nome AS uc, p.presente
           FROM presencas_estudante p JOIN sumarios s ON s.id = p.sumario_id
           JOIN estudantes e ON e.id = p.estudante_id JOIN ucs u ON u.id = s.uc_id
          WHERE $filtro ORDER BY s.data_registro DESC, e.nome LIMIT 500"
    );
} else {
    $st = db()->prepare(
        "SELECT s.data_registro, d.id, d.nome, s.turma_id, u.nome AS uc, s.duracao_horas
           FROM sumarios s JOIN docentes d ON d.id = s.docente_id JOIN ucs u ON u.id = s.uc_id
          WHERE $filtro ORDER BY s.data_registro DESC, d.nome LIMIT 500"
    );
}
$st->execute($params);
$linhas = $st->fetchAll();

render_header('Presenças', 'presencas.php');
pagina_titulo('Presenças', 'Assiduidade por período');
echo '<div class="abas">';
foreach (['estudantes' => 'Estudantes', 'docentes' => 'Docentes'] as $k => $rot) {
    $qs = http_build_query(['tipo' => $k, 'de' => $de, 'ate' => $ate, 'turma' => $turma, 'uc' => $uc]);
    echo '<a class="aba' . ($k === $tipo ? ' ativa' : '') . '" href="presencas.php?' . e($qs) . '">' . e($rot) . '</a>';
}
echo '</div>';

if ($tipo === 'estudantes' && $linhas) {
    $pres = count(array_filter($linhas, fn($l) => (int) $l['presente'] === 1));
    $tot = count($linhas);
    echo '<div class="grade-cartoes">';
    echo '<div class="stat"><span class="stat-valor">' . $tot . '</span><span class="stat-rotulo">Registos</span></div>';
    echo '<div class="stat"><span class="stat-valor">' . $pres . '</span><span class="stat-rotulo">Presenças</span></div>';
    echo '<div class="stat"><span class="stat-valor">' . ($tot - $pres) . '</span><span class="stat-rotulo">Faltas</span></div>';
    echo '<div class="stat"><span class="stat-valor">' . round($pres / $tot * 100) . '%</span><span class="stat-rotulo">Assiduidade</span></div></div>';
}
?>
<section class="cartao">
  <form class="filtros" method="get" action="presencas.php">
    <input type="hidden" name="tipo" value="<?= e($tipo) ?>">
    <div class="campo"><label for="de">De</label><input id="de" type="date" name="de" value="<?= e($de) ?>"></div>
    <div class="campo"><label for="ate">Até</label><input id="ate" type="date" name="ate" value="<?= e($ate) ?>"></div>
    <div class="campo"><label for="turma">Turma</label><select id="turma" name="turma"><option value="">Todas</option>
      <?php foreach ($turmas as $t): ?><option value="<?= e($t) ?>"<?= $t === $turma ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="uc">UC</label><select id="uc" name="uc"><option value="">Todas</option>
      <?php foreach ($ucs as $id => $nome): ?><option value="<?= e($id) ?>"<?= (string) $id === $uc ? ' selected' : '' ?>><?= e($nome) ?></option><?php endforeach; ?></select></div>
    <button class="btn btn-sec" type="submit">Filtrar</button>
  </form>
  <?php if (!$linhas): ?><p class="vazio">Sem registos neste período.</p><?php else: ?>
  <div class="tabela-wrap"><table>
    <thead><tr><th>Data</th><th>Código</th><th><?= $tipo === 'estudantes' ? 'Estudante' : 'Docente' ?></th><th>Turma</th><th>UC</th><th><?= $tipo === 'estudantes' ? 'Presença' : 'Horas' ?></th></tr></thead>
    <tbody>
    <?php foreach ($linhas as $l): ?>
      <tr><td><?= e(data_pt($l['data_registro'])) ?></td><td><?= e($l['id']) ?></td><td><?= e($l['nome']) ?></td><td><?= e($l['turma_id']) ?></td><td><?= e($l['uc']) ?></td>
        <td><?= $tipo === 'estudantes' ? badge((bool) $l['presente']) : (int) $l['duracao_horas'] . ' h' ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
  <?php endif; ?>
</section>
<?php render_footer();
