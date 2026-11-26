<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/agenda.php';
$u = exigir_login();
$periodo = periodo_ativo();

render_header('Horário', 'horario.php');
pagina_titulo('Horário semanal', $periodo ? $periodo['nome'] : '');
if (!$periodo) {
    echo '<div class="alerta alerta-aviso">Não há nenhum período letivo ativo.</div>';
    render_footer();
    exit;
}

$onde = ['a.periodo_id = ?'];
$params = [$periodo['id']];
$turma = '';
$docente = '';
if ($u['tipo'] === 'docente') {
    $onde[] = 'a.docente_id = ?';
    $params[] = $u['id'];
} elseif ($u['tipo'] === 'estudante') {
    $st = db()->prepare('SELECT turma_id FROM estudantes WHERE id = ?');
    $st->execute([$u['id']]);
    $onde[] = 'a.turma_id = ?';
    $params[] = (string) $st->fetchColumn();
} else {
    $turma = (string) ($_GET['turma'] ?? '');
    $docente = (string) ($_GET['docente'] ?? '');
    if ($turma !== '') { $onde[] = 'a.turma_id = ?'; $params[] = $turma; }
    if ($docente !== '') { $onde[] = 'a.docente_id = ?'; $params[] = $docente; }
}

if ($u['tipo'] === 'admin') {
    $turmas = db()->query('SELECT id FROM turmas ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
    $docentes = db()->query('SELECT id, nome FROM docentes ORDER BY nome')->fetchAll(PDO::FETCH_KEY_PAIR);
    echo '<section class="cartao"><form class="filtros" method="get" action="horario.php"><div class="campo"><label for="turma">Turma</label><select id="turma" name="turma"><option value="">Todas</option>';
    foreach ($turmas as $t) {
        echo '<option value="' . e($t) . '"' . ($t === $turma ? ' selected' : '') . '>' . e($t) . '</option>';
    }
    echo '</select></div><div class="campo"><label for="docente">Docente</label><select id="docente" name="docente"><option value="">Todos</option>';
    foreach ($docentes as $id => $nome) {
        echo '<option value="' . e($id) . '"' . ((string) $id === $docente ? ' selected' : '') . '>' . e($nome) . '</option>';
    }
    echo '</select></div><button class="btn btn-sec" type="submit">Filtrar</button></form></section>';
}

$st = db()->prepare(
    'SELECT h.dia_semana, h.hora_inicio, h.hora_fim, h.sala, a.turma_id, u.nome AS uc, d.nome AS docente
       FROM horarios h JOIN atribuicoes a ON a.id = h.atribuicao_id JOIN ucs u ON u.id = a.uc_id JOIN docentes d ON d.id = a.docente_id
      WHERE ' . implode(' AND ', $onde) . ' ORDER BY h.dia_semana, h.hora_inicio'
);
$st->execute($params);
$porDia = [];
foreach ($st->fetchAll() as $h) {
    $porDia[(int) $h['dia_semana']][] = $h;
}
if (!$porDia) {
    echo '<section class="cartao"><p class="vazio">Sem aulas no horário.</p></section>';
}
$hojeDia = (int) date('N');
foreach (dias_semana() as $n => $nome) {
    if (empty($porDia[$n])) {
        continue;
    }
    echo '<section class="cartao' . ($n === $hojeDia ? ' cartao-hoje' : '') . '"><h2>' . e($nome) . ($n === $hojeDia ? ' <span class="badge">Hoje</span>' : '') . '</h2>';
    foreach ($porDia[$n] as $h) {
        echo '<div class="aula"><div class="aula-hora"><strong>' . e(hora_curta($h['hora_inicio'])) . '</strong><small>' . e(hora_curta($h['hora_fim'])) . '</small></div>';
        echo '<div class="aula-corpo"><strong>' . e($h['uc']) . '</strong><span class="suave">' . e($h['turma_id']) . ' · ' . e($h['sala'] ?: 'Sem sala');
        if ($u['tipo'] !== 'docente') {
            echo ' · ' . e($h['docente']);
        }
        echo '</span></div></div>';
    }
    echo '</section>';
}
render_footer();
