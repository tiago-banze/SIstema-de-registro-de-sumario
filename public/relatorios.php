<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/agenda.php';
require __DIR__ . '/../src/export.php';
exigir_login('admin');
$periodo = periodo_ativo();

if (!$periodo) {
    render_header('Relatórios', 'relatorios.php');
    pagina_titulo('Relatórios');
    echo '<div class="alerta alerta-aviso">Não há nenhum período letivo ativo. <a href="periodos.php?novo=1">Criar um período</a>.</div>';
    render_footer();
    exit;
}

$faltam = aulas_em_falta($periodo);
$hoje = date('Y-m-d');
$previstas = aulas_previstas($periodo, $periodo['inicio'], $hoje);

// Cumprimento por docente: aulas já passadas que têm sumário
$agora = date('H:i:s');
$porDocente = [];
foreach ($previstas as $a) {
    if ($a['data'] === $hoje && $a['hora_fim'] > $agora) {
        continue;
    }
    $d = &$porDocente[$a['docente_id']];
    $d = $d ?? ['nome' => $a['docente'], 'previstas' => 0, 'registadas' => 0];
    $d['previstas']++;
    if ($a['sumario']) {
        $d['registadas']++;
    }
    unset($d);
}
uasort($porDocente, fn($a, $b) => strcmp($a['nome'], $b['nome']));

if (($_GET['exportar'] ?? '') === 'aulas_em_falta') {
    csv_enviar('aulas_sem_sumario.csv', ['Data', 'Início', 'Turma', 'UC', 'Docente'],
        array_map(fn($a) => [$a['data'], hora_curta($a['hora_inicio']), $a['turma_id'], $a['uc'], $a['docente']], $faltam));
}
if (($_GET['exportar'] ?? '') === 'horas') {
    csv_enviar('horas_lecionadas.csv', ['Turma', 'UC', 'Docente', 'Horas dadas', 'Horas previstas', '%'],
        array_map(fn($l) => [$l['turma_id'], $l['uc'], $l['docente'], $l['horas'], $l['horas_semestrais'], $l['pct']], horas_por_atribuicao($periodo)));
}

render_header('Relatórios', 'relatorios.php');
pagina_titulo('Relatórios', $periodo['nome']);
?>
<section class="cartao">
  <h2>Cumprimento do horário por docente</h2>
  <?php if (!$porDocente): ?><p class="vazio">Ainda não há aulas previstas até hoje.</p><?php else: ?>
  <div class="tabela-wrap"><table class="resp"><thead><tr><th>Docente</th><th>Aulas previstas</th><th>Com sumário</th><th>Cumprimento</th></tr></thead><tbody>
    <?php foreach ($porDocente as $d): $pct = $d['previstas'] ? round($d['registadas'] / $d['previstas'] * 100) : 100; ?>
      <tr><?= td('Docente', e($d['nome'])) . td('Aulas previstas', $d['previstas']) . td('Com sumário', $d['registadas']) . td('Cumprimento', $pct . '%' . progresso($pct, $pct < 70 ? 'erro' : ($pct < 90 ? 'aviso' : ''))) ?></tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</section>

<section class="cartao">
  <div class="pagina-topo"><h2>Aulas sem sumário (<?= count($faltam) ?>)</h2><a class="btn btn-sec btn-peq" href="relatorios.php?exportar=aulas_em_falta">Exportar CSV</a></div>
  <?php if (!$faltam): ?><p class="vazio">Todas as aulas previstas até agora têm sumário.</p><?php else: ?>
  <div class="tabela-wrap"><table class="resp"><thead><tr><th>Data</th><th>Hora</th><th>Turma</th><th>UC</th><th>Docente</th></tr></thead><tbody>
    <?php foreach (array_slice(array_reverse($faltam), 0, 100) as $a): ?>
      <tr><?= td('Data', e(data_pt($a['data']))) . td('Hora', e(hora_curta($a['hora_inicio']))) . td('Turma', e($a['turma_id'])) . td('UC', e($a['uc'])) . td('Docente', e($a['docente'])) ?></tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <?php if (count($faltam) > 100): ?><p class="suave">Mostradas as 100 mais recentes. O CSV tem todas.</p><?php endif; ?>
  <?php endif; ?>
</section>

<section class="cartao">
  <div class="pagina-topo"><h2>Horas lecionadas por disciplina</h2><a class="btn btn-sec btn-peq" href="relatorios.php?exportar=horas">Exportar CSV</a></div>
  <div class="tabela-wrap"><table class="resp"><thead><tr><th>Turma</th><th>UC</th><th>Docente</th><th>Horas</th><th>Progresso</th></tr></thead><tbody>
    <?php foreach (horas_por_atribuicao($periodo) as $l): ?>
      <tr><?= td('Turma', e($l['turma_id'])) . td('UC', e($l['uc'])) . td('Docente', e($l['docente'])) . td('Horas', $l['horas'] . '/' . (int) $l['horas_semestrais'] . ' h') . td('Progresso', (int) $l['pct'] . '%' . progresso($l['pct'])) ?></tr>
    <?php endforeach; ?>
  </tbody></table></div>
</section>
<?php render_footer();
