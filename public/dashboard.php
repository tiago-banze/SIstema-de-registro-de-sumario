<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
$u = exigir_login();

function contar(string $sql, array $params = []): int
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return (int) $st->fetchColumn();
}

render_header('Painel', 'dashboard.php');

if ($u['tipo'] === 'admin') {
    $desde = date('Y-m-d', strtotime('-30 days'));
    $cartoes = [
        ['Estudantes', contar('SELECT COUNT(*) FROM estudantes'), 'estudantes.php'],
        ['Docentes', contar('SELECT COUNT(*) FROM docentes'), 'docentes.php'],
        ['Cursos', contar('SELECT COUNT(*) FROM cursos'), 'cursos.php'],
        ['Sumários (30 dias)', contar('SELECT COUNT(*) FROM sumarios WHERE data_registro >= ?', [$desde]), 'sumarios.php'],
    ];
    pagina_titulo('Painel', 'Visão geral do sistema');
    echo '<div class="grade-cartoes">';
    foreach ($cartoes as [$rot, $n, $link]) {
        echo '<a class="stat" href="' . e($link) . '"><span class="stat-valor">' . $n . '</span><span class="stat-rotulo">' . e($rot) . '</span></a>';
    }
    echo '</div><div class="duas-colunas">';

    $recentes = db()->query(
        'SELECT s.data_registro, s.sumario, s.duracao_horas, u.nome AS uc, d.nome AS docente, s.turma_id
           FROM sumarios s JOIN ucs u ON u.id = s.uc_id JOIN docentes d ON d.id = s.docente_id
          ORDER BY s.data_registro DESC, s.id DESC LIMIT 5'
    )->fetchAll();
    echo '<section class="cartao"><h2>Últimos sumários</h2>';
    if (!$recentes) {
        echo '<p class="vazio">Ainda não há sumários registados.</p>';
    }
    foreach ($recentes as $r) {
        echo '<div class="item-lista"><strong>' . e($r['uc']) . '</strong> <span class="badge">' . e($r['turma_id']) . '</span>';
        echo '<p>' . e($r['sumario']) . '</p><small class="suave">' . e($r['docente']) . ' - ' . e(data_pt($r['data_registro'])) . ' - ' . (int) $r['duracao_horas'] . ' h</small></div>';
    }
    echo '</section>';

    $horas = db()->query('SELECT nome, horas_semestrais FROM ucs ORDER BY horas_semestrais DESC, nome LIMIT 8')->fetchAll();
    $maximo = max(array_merge([1], array_map('intval', array_column($horas, 'horas_semestrais'))));
    echo '<section class="cartao"><h2>Horas semestrais por UC</h2>';
    if (!$horas) {
        echo '<p class="vazio">Sem unidades curriculares.</p>';
    }
    foreach ($horas as $h) {
        $larg = round(((int) $h['horas_semestrais']) / $maximo * 100);
        echo '<div class="barra-linha"><span class="barra-rotulo">' . e($h['nome']) . '</span>';
        echo '<div class="barra"><div class="barra-preench" style="width:' . $larg . '%"></div></div>';
        echo '<span class="barra-valor">' . (int) $h['horas_semestrais'] . ' h</span></div>';
    }
    echo '</section></div>';
} else {
    pagina_titulo('Olá, ' . $u['nome'], 'O que quer fazer hoje?', '<a class="btn" href="sumario_novo.php">Registar sumário</a>');
    $st = db()->prepare(
        'SELECT s.data_registro, s.sumario, s.turma_id, u.nome AS uc FROM sumarios s JOIN ucs u ON u.id = s.uc_id
          WHERE s.docente_id = ? ORDER BY s.data_registro DESC, s.id DESC LIMIT 5'
    );
    $st->execute([$u['id']]);
    echo '<section class="cartao"><h2>Os meus últimos sumários</h2>';
    $linhas = $st->fetchAll();
    if (!$linhas) {
        echo '<p class="vazio">Ainda não registou nenhum sumário.</p>';
    }
    foreach ($linhas as $r) {
        echo '<div class="item-lista"><strong>' . e($r['uc']) . '</strong> <span class="badge">' . e($r['turma_id']) . '</span>';
        echo '<p>' . e($r['sumario']) . '</p><small class="suave">' . e(data_pt($r['data_registro'])) . '</small></div>';
    }
    echo '</section>';
}
render_footer();
