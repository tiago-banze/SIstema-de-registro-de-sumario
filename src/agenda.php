<?php
function dias_semana(): array
{
    return [1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado', 7 => 'Domingo'];
}

function feriados_entre(string $de, string $ate): array
{
    $st = db()->prepare('SELECT data FROM feriados WHERE data BETWEEN ? AND ?');
    $st->execute([$de, $ate]);
    return array_fill_keys($st->fetchAll(PDO::FETCH_COLUMN), true);
}

function sumarios_registados($periodoId, string $de, string $ate): array
{
    $st = db()->prepare(
        'SELECT s.id, s.horario_id, s.data_aula, s.estado FROM sumarios s JOIN atribuicoes a ON a.id = s.atribuicao_id
          WHERE a.periodo_id = ? AND s.horario_id IS NOT NULL AND s.data_aula BETWEEN ? AND ?'
    );
    $st->execute([$periodoId, $de, $ate]);
    $mapa = [];
    foreach ($st->fetchAll() as $r) {
        $mapa[$r['horario_id'] . '|' . $r['data_aula']] = ['id' => $r['id'], 'estado' => $r['estado']];
    }
    return $mapa;
}

/** Aulas previstas pelo horário entre duas datas. Filtros: docente_id, turma_id, atribuicao_id. */
function aulas_previstas(array $periodo, string $de, string $ate, array $filtro = []): array
{
    $de = max($de, $periodo['inicio']);
    $ate = min($ate, $periodo['fim']);
    if ($de > $ate) {
        return [];
    }
    $onde = ['a.periodo_id = ?'];
    $params = [$periodo['id']];
    foreach (['docente_id' => 'a.docente_id', 'turma_id' => 'a.turma_id', 'atribuicao_id' => 'a.id'] as $chave => $coluna) {
        if (!empty($filtro[$chave])) {
            $onde[] = "$coluna = ?";
            $params[] = $filtro[$chave];
        }
    }
    $st = db()->prepare(
        'SELECT h.id AS horario_id, h.dia_semana, h.hora_inicio, h.hora_fim, h.sala, a.id AS atribuicao_id, a.turma_id, a.docente_id,
                u.id AS uc_id, u.nome AS uc, d.nome AS docente
           FROM horarios h JOIN atribuicoes a ON a.id = h.atribuicao_id JOIN ucs u ON u.id = a.uc_id JOIN docentes d ON d.id = a.docente_id
          WHERE ' . implode(' AND ', $onde)
    );
    $st->execute($params);
    $porDia = [];
    foreach ($st->fetchAll() as $h) {
        $porDia[(int) $h['dia_semana']][] = $h;
    }
    $feriados = feriados_entre($de, $ate);
    $registados = sumarios_registados($periodo['id'], $de, $ate);
    $saida = [];
    $dia = new DateTimeImmutable($de);
    $fim = new DateTimeImmutable($ate);
    for (; $dia <= $fim; $dia = $dia->modify('+1 day')) {
        $data = $dia->format('Y-m-d');
        $dow = (int) $dia->format('N');
        if (isset($feriados[$data]) || empty($porDia[$dow])) {
            continue;
        }
        foreach ($porDia[$dow] as $h) {
            $h['data'] = $data;
            $h['sumario'] = $registados[$h['horario_id'] . '|' . $data] ?? null;
            $saida[] = $h;
        }
    }
    usort($saida, fn($a, $b) => [$a['data'], $a['hora_inicio']] <=> [$b['data'], $b['hora_inicio']]);
    return $saida;
}

/** Aulas que já deviam ter acontecido e não têm sumário. */
function aulas_em_falta(array $periodo, array $filtro = []): array
{
    $hoje = date('Y-m-d');
    $agora = date('H:i:s');
    $todas = aulas_previstas($periodo, $periodo['inicio'], $hoje, $filtro);
    return array_values(array_filter($todas, fn($a) => $a['sumario'] === null && ($a['data'] < $hoje || $a['hora_fim'] <= $agora)));
}

function horas_por_atribuicao(array $periodo, array $filtro = []): array
{
    $onde = ['a.periodo_id = ?'];
    $params = [$periodo['id']];
    if (!empty($filtro['docente_id'])) {
        $onde[] = 'a.docente_id = ?';
        $params[] = $filtro['docente_id'];
    }
    $st = db()->prepare(
        "SELECT a.id, a.turma_id, u.nome AS uc, u.horas_semestrais, d.nome AS docente,
                COALESCE(SUM(CASE WHEN s.estado <> 'rejeitado' THEN s.duracao_horas END), 0) AS horas
           FROM atribuicoes a JOIN ucs u ON u.id = a.uc_id JOIN docentes d ON d.id = a.docente_id
           LEFT JOIN sumarios s ON s.atribuicao_id = a.id
          WHERE " . implode(' AND ', $onde) . '
          GROUP BY a.id, a.turma_id, u.nome, u.horas_semestrais, d.nome ORDER BY a.turma_id, u.nome'
    );
    $st->execute($params);
    $linhas = $st->fetchAll();
    foreach ($linhas as &$l) {
        $l['horas'] = (int) $l['horas'];
        $l['pct'] = $l['horas_semestrais'] > 0 ? min(100, round($l['horas'] / $l['horas_semestrais'] * 100)) : 0;
    }
    return $linhas;
}

function em_risco(int $total, int $faltas): bool
{
    return $total >= (int) config('minimo_aulas_para_risco', 4)
        && $faltas * 100 >= $total * (int) config('limite_faltas_percentual', 25);
}

/** Assiduidade por estudante e UC no período. Filtros: estudante_id, turma_id, uc_id. */
function assiduidade(array $periodo, array $filtro = []): array
{
    $onde = ['a.periodo_id = ?'];
    $params = [$periodo['id']];
    foreach (['estudante_id' => 'e.id', 'turma_id' => 'e.turma_id', 'uc_id' => 'u.id'] as $chave => $coluna) {
        if (!empty($filtro[$chave])) {
            $onde[] = "$coluna = ?";
            $params[] = $filtro[$chave];
        }
    }
    $st = db()->prepare(
        "SELECT e.id AS estudante_id, e.nome, e.turma_id, u.nome AS uc, COUNT(p.id) AS total,
                SUM(p.estado = 'presente') AS presentes, SUM(p.estado = 'falta') AS faltas, SUM(p.estado = 'justificada') AS justificadas
           FROM presencas p JOIN sumarios s ON s.id = p.sumario_id AND s.estado <> 'rejeitado'
           JOIN atribuicoes a ON a.id = s.atribuicao_id JOIN ucs u ON u.id = a.uc_id JOIN estudantes e ON e.id = p.estudante_id
          WHERE " . implode(' AND ', $onde) . '
          GROUP BY e.id, e.nome, e.turma_id, a.id, u.nome ORDER BY e.nome, u.nome'
    );
    $st->execute($params);
    $linhas = $st->fetchAll();
    foreach ($linhas as &$l) {
        $l['total'] = (int) $l['total'];
        $l['presentes'] = (int) $l['presentes'];
        $l['faltas'] = (int) $l['faltas'];
        $l['justificadas'] = (int) $l['justificadas'];
        $l['pct'] = $l['total'] > 0 ? round(($l['total'] - $l['faltas']) / $l['total'] * 100) : 100;
        $l['risco'] = em_risco($l['total'], $l['faltas']);
    }
    return $linhas;
}
