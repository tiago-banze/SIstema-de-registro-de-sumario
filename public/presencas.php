<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/agenda.php';
require __DIR__ . '/../src/export.php';
exigir_login('admin');
$periodo = periodo_ativo();

$aba = ($_GET['aba'] ?? 'resumo') === 'registos' ? 'registos' : 'resumo';
$turma = (string) ($_GET['turma'] ?? '');
$uc = (string) ($_GET['uc'] ?? '');
$soRisco = !empty($_GET['so_risco']);
$de = (string) ($_GET['de'] ?? ($periodo['inicio'] ?? date('Y-m-d', strtotime('-30 days'))));
$ate = (string) ($_GET['ate'] ?? date('Y-m-d'));
$estado = (string) ($_GET['estado'] ?? '');

$turmas = db()->query('SELECT id FROM turmas ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
$ucs = db()->query('SELECT id, nome FROM ucs ORDER BY nome')->fetchAll(PDO::FETCH_KEY_PAIR);

$resumo = [];
$registos = [];
if ($periodo && $aba === 'resumo') {
    $resumo = assiduidade($periodo, ['turma_id' => $turma, 'uc_id' => $uc]);
    if ($soRisco) {
        $resumo = array_values(array_filter($resumo, fn($l) => $l['risco']));
    }
}
if ($aba === 'registos') {
    $onde = ['s.data_aula BETWEEN ? AND ?', "s.estado <> 'rejeitado'"];
    $params = [$de, $ate];
    if ($turma !== '') { $onde[] = 'e.turma_id = ?'; $params[] = $turma; }
    if ($uc !== '') { $onde[] = 'u.id = ?'; $params[] = $uc; }
    if (in_array($estado, ['presente', 'falta', 'justificada'], true)) { $onde[] = 'p.estado = ?'; $params[] = $estado; }
    $st = db()->prepare(
        'SELECT s.data_aula, e.id, e.nome, e.turma_id, u.nome AS uc, p.estado
           FROM presencas p JOIN sumarios s ON s.id = p.sumario_id JOIN atribuicoes a ON a.id = s.atribuicao_id
           JOIN ucs u ON u.id = a.uc_id JOIN estudantes e ON e.id = p.estudante_id
          WHERE ' . implode(' AND ', $onde) . ' ORDER BY s.data_aula DESC, e.nome LIMIT 500'
    );
    $st->execute($params);
    $registos = $st->fetchAll();
}

if (($_GET['exportar'] ?? '') === 'csv') {
    if ($aba === 'resumo') {
        csv_enviar('assiduidade.csv', ['Código', 'Estudante', 'Turma', 'UC', 'Aulas', 'Presenças', 'Faltas', 'Justificadas', 'Assiduidade %', 'Em risco'],
            array_map(fn($l) => [$l['estudante_id'], $l['nome'], $l['turma_id'], $l['uc'], $l['total'], $l['presentes'], $l['faltas'], $l['justificadas'], $l['pct'], $l['risco'] ? 'Sim' : 'Não'], $resumo));
    }
    csv_enviar('presencas.csv', ['Data', 'Código', 'Estudante', 'Turma', 'UC', 'Estado'],
        array_map(fn($l) => [$l['data_aula'], $l['id'], $l['nome'], $l['turma_id'], $l['uc'], $l['estado']], $registos));
}

render_header('Presenças', 'presencas.php');
pagina_titulo('Presenças', $periodo ? $periodo['nome'] : 'Sem período ativo');
echo '<div class="abas">';
foreach (['resumo' => 'Resumo por estudante', 'registos' => 'Registos'] as $k => $rot) {
    echo '<a class="aba' . ($k === $aba ? ' ativa' : '') . '" href="presencas.php?aba=' . $k . '">' . e($rot) . '</a>';
}
echo '</div>';
?>
<section class="cartao">
  <form class="filtros" method="get" action="presencas.php">
    <input type="hidden" name="aba" value="<?= e($aba) ?>">
    <?php if ($aba === 'registos'): ?>
      <div class="campo"><label for="de">De</label><input id="de" type="date" name="de" value="<?= e($de) ?>"></div>
      <div class="campo"><label for="ate">Até</label><input id="ate" type="date" name="ate" value="<?= e($ate) ?>"></div>
      <div class="campo"><label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option>
        <?php foreach (['presente' => 'Presente', 'falta' => 'Falta', 'justificada' => 'Justificada'] as $k => $r): ?><option value="<?= $k ?>"<?= $estado === $k ? ' selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select></div>
    <?php endif; ?>
    <div class="campo"><label for="turma">Turma</label><select id="turma" name="turma"><option value="">Todas</option>
      <?php foreach ($turmas as $t): ?><option value="<?= e($t) ?>"<?= $t === $turma ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="uc">Unidade curricular</label><select id="uc" name="uc"><option value="">Todas</option>
      <?php foreach ($ucs as $id => $nome): ?><option value="<?= e($id) ?>"<?= (string) $id === $uc ? ' selected' : '' ?>><?= e($nome) ?></option><?php endforeach; ?></select></div>
    <?php if ($aba === 'resumo'): ?>
      <label class="check-linha"><input class="check" type="checkbox" name="so_risco" value="1"<?= $soRisco ? ' checked' : '' ?>> Só em risco</label>
    <?php endif; ?>
    <button class="btn btn-sec" type="submit">Filtrar</button>
    <button class="btn btn-sec" type="submit" name="exportar" value="csv">Exportar CSV</button>
  </form>

  <?php if ($aba === 'resumo'): ?>
    <p class="suave">Em risco: <?= (int) config('limite_faltas_percentual', 25) ?>% ou mais de faltas injustificadas, depois de <?= (int) config('minimo_aulas_para_risco', 4) ?> aulas.</p>
    <?php if (!$resumo): ?><p class="vazio">Sem dados para mostrar.</p><?php else: ?>
    <div class="tabela-wrap"><table class="resp"><thead><tr><th>Estudante</th><th>Turma</th><th>UC</th><th>Aulas</th><th>Faltas</th><th>Just.</th><th>Assiduidade</th></tr></thead><tbody>
      <?php foreach ($resumo as $l): ?>
        <tr><?= td('Estudante', e($l['nome'])) . td('Turma', e($l['turma_id'])) . td('UC', e($l['uc'])) . td('Aulas', $l['total']) . td('Faltas', $l['faltas']) . td('Just.', $l['justificadas'])
            . td('Assiduidade', $l['pct'] . '% ' . ($l['risco'] ? '<span class="badge badge-erro">Em risco</span>' : '')) ?></tr>
      <?php endforeach; ?>
    </tbody></table></div>
    <?php endif; ?>
  <?php else: ?>
    <?php if (!$registos): ?><p class="vazio">Sem registos neste período.</p><?php else: ?>
    <div class="tabela-wrap"><table class="resp"><thead><tr><th>Data</th><th>Código</th><th>Estudante</th><th>Turma</th><th>UC</th><th>Presença</th></tr></thead><tbody>
      <?php foreach ($registos as $l): ?>
        <tr><?= td('Data', e(data_pt($l['data_aula']))) . td('Código', e($l['id'])) . td('Estudante', e($l['nome'])) . td('Turma', e($l['turma_id'])) . td('UC', e($l['uc'])) . td('Presença', badge_estado($l['estado'])) ?></tr>
      <?php endforeach; ?>
    </tbody></table></div>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php render_footer();
