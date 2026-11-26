<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/agenda.php';
$u = exigir_login();
$periodo = periodo_ativo();
$hoje = date('Y-m-d');

function contar(string $sql, array $params = []): int
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return (int) $st->fetchColumn();
}

function linha_aula(array $a, bool $comAcao, bool $mostrarData = false): string
{
    $h = '<div class="aula"><div class="aula-hora"><strong>' . e(hora_curta($a['hora_inicio'])) . '</strong><small>' . e(hora_curta($a['hora_fim'])) . '</small></div>';
    $h .= '<div class="aula-corpo"><strong>' . e($a['uc']) . '</strong>';
    $h .= '<span class="suave">' . ($mostrarData ? e(data_pt($a['data'])) . ' · ' : '') . e($a['turma_id']) . ' · ' . e($a['sala'] ?: 'Sem sala') . '</span></div><div class="aula-acao">';
    if ($a['sumario']) {
        $h .= badge_estado($a['sumario']['estado']);
    } elseif ($comAcao) {
        $h .= '<a class="btn btn-peq" href="sumario_novo.php?horario=' . (int) $a['horario_id'] . '&amp;data=' . e($a['data']) . '">Registar</a>';
    } else {
        $h .= '<span class="badge badge-aviso">Por registar</span>';
    }
    return $h . '</div></div>';
}

function bloco_horas(array $linhas, int $limite = 8): void
{
    echo '<section class="cartao"><h2>Matéria lecionada</h2>';
    if (!$linhas) {
        echo '<p class="vazio">Sem atribuições neste período.</p>';
    }
    foreach (array_slice($linhas, 0, $limite) as $l) {
        echo '<div class="barra-linha"><span class="barra-rotulo">' . e($l['turma_id'] . ' · ' . $l['uc']) . '</span>' . progresso($l['pct']);
        echo '<span class="barra-valor">' . (int) $l['horas'] . '/' . (int) $l['horas_semestrais'] . ' h</span></div>';
    }
    echo '</section>';
}

render_header('Painel', 'dashboard.php');

if (!$periodo) {
    pagina_titulo('Painel');
    echo '<div class="alerta alerta-aviso">Não há nenhum período letivo ativo.' . ($u['tipo'] === 'admin' ? ' <a href="periodos.php?novo=1">Criar um período</a>.' : ' Peça à administração para o configurar.') . '</div>';
    render_footer();
    exit;
}

if ($u['tipo'] === 'admin') {
    $faltam = aulas_em_falta($periodo);
    $assid = assiduidade($periodo);
    $totalReg = array_sum(array_column($assid, 'total'));
    $totalFaltas = array_sum(array_column($assid, 'faltas'));
    $taxa = $totalReg > 0 ? round(($totalReg - $totalFaltas) / $totalReg * 100) : 100;
    $risco = count(array_filter($assid, fn($l) => $l['risco']));
    $porValidar = contar("SELECT COUNT(*) FROM sumarios s JOIN atribuicoes a ON a.id = s.atribuicao_id WHERE a.periodo_id = ? AND s.estado = 'registado'", [$periodo['id']]);

    pagina_titulo('Painel', $periodo['nome'] . ' (' . data_pt($periodo['inicio']) . ' a ' . data_pt($periodo['fim']) . ')');
    $cartoes = [
        ['Sumários por validar', $porValidar, 'sumarios.php?estado=registado'],
        ['Aulas sem sumário', count($faltam), 'relatorios.php'],
        ['Assiduidade geral', $taxa . '%', 'presencas.php'],
        ['Estudantes em risco', $risco, 'presencas.php?so_risco=1'],
        ['Estudantes ativos', contar('SELECT COUNT(*) FROM estudantes WHERE ativo = 1'), 'estudantes.php'],
        ['Docentes ativos', contar('SELECT COUNT(*) FROM docentes WHERE ativo = 1'), 'docentes.php'],
    ];
    echo '<div class="grade-cartoes">';
    foreach ($cartoes as [$rot, $n, $link]) {
        echo '<a class="stat" href="' . e($link) . '"><span class="stat-valor">' . e($n) . '</span><span class="stat-rotulo">' . e($rot) . '</span></a>';
    }
    echo '</div><div class="duas-colunas"><div>';

    echo '<section class="cartao"><h2>Aulas sem sumário</h2>';
    if (!$faltam) {
        echo '<p class="vazio">Todas as aulas previstas têm sumário.</p>';
    }
    foreach (array_slice(array_reverse($faltam), 0, 6) as $a) {
        echo '<div class="item-lista"><strong>' . e($a['uc']) . '</strong> <span class="badge">' . e($a['turma_id']) . '</span><br><span class="suave">' . e($a['docente']) . ' · ' . e(data_pt($a['data'])) . ' às ' . e(hora_curta($a['hora_inicio'])) . '</span></div>';
    }
    echo '</section>';

    $st = db()->prepare(
        "SELECT s.id, s.data_aula, s.sumario, u.nome AS uc, d.nome AS docente, a.turma_id
           FROM sumarios s JOIN atribuicoes a ON a.id = s.atribuicao_id JOIN ucs u ON u.id = a.uc_id JOIN docentes d ON d.id = a.docente_id
          WHERE a.periodo_id = ? AND s.estado = 'registado' ORDER BY s.data_aula DESC, s.id DESC LIMIT 5"
    );
    $st->execute([$periodo['id']]);
    echo '</div><div><section class="cartao"><h2>Por validar</h2>';
    $pend = $st->fetchAll();
    if (!$pend) {
        echo '<p class="vazio">Não há sumários à espera de validação.</p>';
    }
    foreach ($pend as $r) {
        echo '<div class="item-lista"><a href="sumarios.php?ver=' . (int) $r['id'] . '"><strong>' . e($r['uc']) . '</strong></a> <span class="badge">' . e($r['turma_id']) . '</span>';
        echo '<p>' . e($r['sumario']) . '</p><small class="suave">' . e($r['docente']) . ' · ' . e(data_pt($r['data_aula'])) . '</small></div>';
    }
    echo '</section></div></div>';

    $horas = horas_por_atribuicao($periodo);
    usort($horas, fn($a, $b) => $a['pct'] <=> $b['pct']);
    bloco_horas($horas);
} elseif ($u['tipo'] === 'docente') {
    $hojeAulas = aulas_previstas($periodo, $hoje, $hoje, ['docente_id' => $u['id']]);
    $faltam = aulas_em_falta($periodo, ['docente_id' => $u['id']]);
    pagina_titulo('Olá, ' . $u['nome'], date('d/m/Y'), '<a class="btn" href="sumario_novo.php">Registar sumário</a>');

    echo '<section class="cartao"><h2>Aulas de hoje</h2>';
    if (!$hojeAulas) {
        echo '<p class="vazio">Não tem aulas previstas para hoje.</p>';
    }
    foreach ($hojeAulas as $a) {
        echo linha_aula($a, true);
    }
    echo '</section>';

    $pendentes = $faltam;
    if ($pendentes) {
        echo '<section class="cartao"><h2>Aulas por registar (' . count($pendentes) . ')</h2>';
        foreach (array_slice(array_reverse($pendentes), 0, 6) as $a) {
            echo linha_aula($a, $a['data'] >= date('Y-m-d', strtotime('-7 days')), true);
        }
        echo '<p class="suave">O registo só é permitido nos últimos 7 dias. Para aulas mais antigas fale com a administração.</p></section>';
    }
    bloco_horas(horas_por_atribuicao($periodo, ['docente_id' => $u['id']]));
} else {
    $st = db()->prepare('SELECT turma_id FROM estudantes WHERE id = ?');
    $st->execute([$u['id']]);
    $turma = (string) $st->fetchColumn();
    pagina_titulo('Olá, ' . $u['nome'], 'Turma ' . $turma);

    $hojeAulas = aulas_previstas($periodo, $hoje, $hoje, ['turma_id' => $turma]);
    echo '<section class="cartao"><h2>Aulas de hoje</h2>';
    if (!$hojeAulas) {
        echo '<p class="vazio">Não há aulas previstas para hoje.</p>';
    }
    foreach ($hojeAulas as $a) {
        echo linha_aula($a, false);
    }
    echo '</section>';

    $assid = assiduidade($periodo, ['estudante_id' => $u['id']]);
    echo '<section class="cartao"><h2>As minhas presenças por disciplina</h2>';
    if (!$assid) {
        echo '<p class="vazio">Ainda não há aulas registadas neste período.</p>';
    }
    foreach ($assid as $l) {
        $nivel = $l['risco'] ? 'erro' : ($l['pct'] < 85 ? 'aviso' : '');
        echo '<div class="item-lista"><div class="linha-dupla"><strong>' . e($l['uc']) . '</strong><span>' . (int) $l['pct'] . '%</span></div>' . progresso($l['pct'], $nivel);
        echo '<small class="suave">' . $l['faltas'] . ' falta(s) em ' . $l['total'] . ' aula(s)' . ($l['justificadas'] ? ', ' . $l['justificadas'] . ' justificada(s)' : '') . '</small>';
        if ($l['risco']) {
            echo ' <span class="badge badge-erro">Em risco por faltas</span>';
        }
        echo '</div>';
    }
    echo '</section>';
}
render_footer();
