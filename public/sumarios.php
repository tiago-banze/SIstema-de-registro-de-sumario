<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/agenda.php';
require __DIR__ . '/../src/export.php';
$u = exigir_login(['admin', 'docente']);
$ehAdmin = $u['tipo'] === 'admin';
$periodo = periodo_ativo();

$SQL_BASE = "FROM sumarios s JOIN atribuicoes a ON a.id = s.atribuicao_id JOIN ucs uc ON uc.id = a.uc_id JOIN docentes d ON d.id = a.docente_id";

/** Carrega um sumário se a pessoa tiver acesso. */
function sumario_acessivel(int $id, array $u, string $base): ?array
{
    $sql = "SELECT s.*, a.turma_id, a.docente_id, uc.nome AS uc, d.nome AS docente $base WHERE s.id = ?";
    $params = [$id];
    if ($u['tipo'] === 'docente') {
        $sql .= ' AND a.docente_id = ?';
        $params[] = $u['id'];
    }
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetch() ?: null;
}

// Ações do administrador sobre um sumário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ehAdmin) {
    csrf_validar();
    $id = (int) ($_POST['id'] ?? 0);
    $acao = (string) ($_POST['acao'] ?? '');
    $s = sumario_acessivel($id, $u, $SQL_BASE);
    if (!$s) {
        flash('erro', 'Sumário não encontrado.');
        redirect('sumarios.php');
    }
    if ($acao === 'validar' || $acao === 'rejeitar') {
        $nota = trim((string) ($_POST['nota'] ?? ''));
        if ($acao === 'rejeitar' && $nota === '') {
            flash('erro', 'Indique o motivo da rejeição.');
        } else {
            db()->prepare('UPDATE sumarios SET estado = ?, nota = ?, validado_por = ?, validado_em = ? WHERE id = ?')
                ->execute([$acao === 'validar' ? 'validado' : 'rejeitado', $nota !== '' ? mb_substr($nota, 0, 255) : null, $u['id'], date('Y-m-d H:i:s'), $id]);
            auditar($acao, 'sumarios', 'id=' . $id);
            flash('sucesso', $acao === 'validar' ? 'Sumário validado.' : 'Sumário rejeitado. O docente pode corrigi-lo.');
        }
    } elseif ($acao === 'presencas') {
        $up = db()->prepare('UPDATE presencas SET estado = ? WHERE sumario_id = ? AND estudante_id = ?');
        foreach ((array) ($_POST['estado'] ?? []) as $idEst => $v) {
            if (in_array($v, ['presente', 'falta', 'justificada'], true)) {
                $up->execute([$v, $id, (int) $idEst]);
            }
        }
        auditar('editar_presencas', 'sumarios', 'id=' . $id);
        flash('sucesso', 'Presenças atualizadas.');
    }
    redirect('sumarios.php?ver=' . $id);
}

$de = (string) ($_GET['de'] ?? ($periodo['inicio'] ?? date('Y-m-d', strtotime('-30 days'))));
$ate = (string) ($_GET['ate'] ?? date('Y-m-d'));
$estado = (string) ($_GET['estado'] ?? '');
$turma = (string) ($_GET['turma'] ?? '');
$q = trim((string) ($_GET['q'] ?? ''));

$onde = ['s.data_aula BETWEEN ? AND ?'];
$params = [$de, $ate];
if (!$ehAdmin) {
    $onde[] = 'a.docente_id = ?';
    $params[] = $u['id'];
}
if (in_array($estado, ['registado', 'validado', 'rejeitado'], true)) {
    $onde[] = 's.estado = ?';
    $params[] = $estado;
}
if ($turma !== '') {
    $onde[] = 'a.turma_id = ?';
    $params[] = $turma;
}
if ($q !== '') {
    $onde[] = '(s.sumario LIKE ? OR uc.nome LIKE ? OR d.nome LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%");
}
$st = db()->prepare(
    "SELECT s.id, s.data_aula, s.sumario, s.duracao_horas, s.estado, a.turma_id, uc.nome AS uc, d.nome AS docente,
            (SELECT COUNT(*) FROM presencas p WHERE p.sumario_id = s.id AND p.estado <> 'falta') AS presentes,
            (SELECT COUNT(*) FROM presencas p WHERE p.sumario_id = s.id) AS total
       $SQL_BASE WHERE " . implode(' AND ', $onde) . ' ORDER BY s.data_aula DESC, s.id DESC LIMIT 300'
);
$st->execute($params);
$linhas = $st->fetchAll();

if (($_GET['exportar'] ?? '') === 'csv') {
    csv_enviar('sumarios.csv', ['Data', 'Turma', 'UC', 'Docente', 'Sumário', 'Horas', 'Estado', 'Presentes', 'Total'],
        array_map(fn($l) => [$l['data_aula'], $l['turma_id'], $l['uc'], $l['docente'], $l['sumario'], $l['duracao_horas'], $l['estado'], $l['presentes'], $l['total']], $linhas));
}

$titulo = $ehAdmin ? 'Sumários' : 'Meus sumários';
render_header($titulo, 'sumarios.php');
pagina_titulo($titulo, count($linhas) . ' registo(s)', $ehAdmin ? '' : '<a class="btn" href="sumario_novo.php">Registar sumário</a>');

if (isset($_GET['ver'])) {
    $s = sumario_acessivel((int) $_GET['ver'], $u, $SQL_BASE);
    if ($s) {
        $pr = db()->prepare('SELECT e.id, e.nome, p.estado FROM presencas p JOIN estudantes e ON e.id = p.estudante_id WHERE p.sumario_id = ? ORDER BY e.nome');
        $pr->execute([$s['id']]);
        $alunos = $pr->fetchAll();
        echo '<section class="cartao"><h2>' . e($s['uc']) . ' <span class="badge">' . e($s['turma_id']) . '</span> ' . badge_estado($s['estado']) . '</h2>';
        echo '<p class="suave">' . e(data_pt($s['data_aula'])) . ' · ' . e($s['docente']) . ' · ' . (int) $s['duracao_horas'] . ' h</p><p>' . e($s['sumario']) . '</p>';
        if ($s['nota']) {
            echo '<div class="alerta alerta-aviso">Nota da administração: ' . e($s['nota']) . '</div>';
        }
        if (!$ehAdmin && $s['estado'] !== 'validado') {
            echo '<p><a class="btn btn-sec" href="sumario_novo.php?editar=' . (int) $s['id'] . '">Editar sumário</a></p>';
        }
        if ($ehAdmin) {
            echo '<form method="post" action="sumarios.php" class="grade-form">' . csrf_campo() . '<input type="hidden" name="id" value="' . (int) $s['id'] . '">';
            echo '<div class="campo largo"><label for="nota">Nota (obrigatória para rejeitar)</label><input id="nota" name="nota" maxlength="255" value="' . e($s['nota']) . '"></div>';
            echo '<div class="acoes-form"><button class="btn" name="acao" value="validar" type="submit">Validar</button>';
            echo '<button class="btn btn-perigo" name="acao" value="rejeitar" type="submit">Rejeitar</button></div></form>';
        }
        echo '<h3>Presenças</h3>';
        if ($ehAdmin) {
            echo '<form method="post" action="sumarios.php">' . csrf_campo() . '<input type="hidden" name="id" value="' . (int) $s['id'] . '"><input type="hidden" name="acao" value="presencas">';
        }
        echo '<div class="tabela-wrap"><table class="resp"><thead><tr><th>Código</th><th>Estudante</th><th>Presença</th></tr></thead><tbody>';
        foreach ($alunos as $a) {
            if ($ehAdmin) {
                $sel = '<select name="estado[' . (int) $a['id'] . ']" aria-label="Presença de ' . e($a['nome']) . '">';
                foreach (['presente' => 'Presente', 'falta' => 'Falta', 'justificada' => 'Justificada'] as $k => $r) {
                    $sel .= '<option value="' . $k . '"' . ($a['estado'] === $k ? ' selected' : '') . '>' . $r . '</option>';
                }
                $celula = $sel . '</select>';
            } else {
                $celula = badge_estado($a['estado']);
            }
            echo '<tr>' . td('Código', e($a['id'])) . td('Estudante', e($a['nome'])) . td('Presença', $celula) . '</tr>';
        }
        echo '</tbody></table></div>';
        if ($ehAdmin && $alunos) {
            echo '<div class="acoes-form"><button class="btn btn-sec" type="submit">Guardar presenças</button></div></form>';
        }
        echo '</section>';
    }
}

$turmas = db()->query('SELECT id FROM turmas ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
?>
<section class="cartao">
  <form class="filtros" method="get" action="sumarios.php">
    <div class="campo"><label for="de">De</label><input id="de" type="date" name="de" value="<?= e($de) ?>"></div>
    <div class="campo"><label for="ate">Até</label><input id="ate" type="date" name="ate" value="<?= e($ate) ?>"></div>
    <div class="campo"><label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option>
      <?php foreach (['registado' => 'Por validar', 'validado' => 'Validado', 'rejeitado' => 'Rejeitado'] as $k => $r): ?><option value="<?= $k ?>"<?= $estado === $k ? ' selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="turma">Turma</label><select id="turma" name="turma"><option value="">Todas</option>
      <?php foreach ($turmas as $t): ?><option value="<?= e($t) ?>"<?= $t === $turma ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="q">Pesquisar</label><input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="UC, docente ou texto"></div>
    <button class="btn btn-sec" type="submit">Filtrar</button>
    <?php if ($ehAdmin): ?><button class="btn btn-sec" type="submit" name="exportar" value="csv">Exportar CSV</button><?php endif; ?>
  </form>
  <?php if (!$linhas): ?>
    <p class="vazio">Nenhum sumário com estes filtros.</p>
  <?php else: ?>
    <div class="tabela-wrap"><table class="resp">
      <thead><tr><th>Data</th><th>UC</th><th>Turma</th><?php if ($ehAdmin): ?><th>Docente</th><?php endif; ?><th>Sumário</th><th>Horas</th><th>Presentes</th><th>Estado</th><th class="col-acoes"></th></tr></thead>
      <tbody>
      <?php foreach ($linhas as $l): ?>
        <tr><?= td('Data', e(data_pt($l['data_aula']))) . td('UC', e($l['uc'])) . td('Turma', e($l['turma_id'])) ?>
          <?= $ehAdmin ? td('Docente', e($l['docente'])) : '' ?>
          <?= td('Sumário', e($l['sumario']), 'texto-longo') . td('Horas', (int) $l['duracao_horas']) . td('Presentes', (int) $l['presentes'] . '/' . (int) $l['total']) . td('Estado', badge_estado($l['estado'])) ?>
          <?= td('Ações', '<a class="btn btn-peq btn-sec" href="sumarios.php?ver=' . (int) $l['id'] . '">Abrir</a>', 'col-acoes') ?></tr>
      <?php endforeach; ?>
      </tbody></table></div>
  <?php endif; ?>
</section>
<?php render_footer();
