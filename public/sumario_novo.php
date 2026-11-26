<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
require __DIR__ . '/../src/agenda.php';
$u = exigir_login('docente');
$periodo = periodo_ativo();

if (!$periodo) {
    render_header('Registar sumário', 'sumario_novo.php');
    echo '<div class="alerta alerta-aviso">Não há nenhum período letivo ativo. Peça à administração para o configurar.</div>';
    render_footer();
    exit;
}

$hoje = date('Y-m-d');
$limiteInferior = date('Y-m-d', strtotime('-7 days'));
$estadosValidos = ['presente', 'falta', 'justificada'];

function atribuicao_do_docente($id, $docenteId, $periodoId): ?array
{
    $st = db()->prepare('SELECT a.id, a.turma_id, a.uc_id, u.nome AS uc FROM atribuicoes a JOIN ucs u ON u.id = a.uc_id WHERE a.id = ? AND a.docente_id = ? AND a.periodo_id = ?');
    $st->execute([$id, $docenteId, $periodoId]);
    return $st->fetch() ?: null;
}

function alunos_da_turma(string $turma): array
{
    $st = db()->prepare('SELECT id, nome FROM estudantes WHERE turma_id = ? AND ativo = 1 ORDER BY nome');
    $st->execute([$turma]);
    return $st->fetchAll();
}

$erro = '';
$ctx = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $ctx = [
        'editar'     => (int) ($_POST['editar'] ?? 0),
        'atribuicao' => (int) ($_POST['atribuicao'] ?? 0),
        'horario'    => (int) ($_POST['horario'] ?? 0),
        'data'       => (string) ($_POST['data'] ?? ''),
        'sumario'    => trim((string) ($_POST['sumario'] ?? '')),
        'duracao'    => (int) ($_POST['duracao'] ?? 0),
        'estados'    => is_array($_POST['estado'] ?? null) ? $_POST['estado'] : [],
    ];
    $atrib = atribuicao_do_docente($ctx['atribuicao'], $u['id'], $periodo['id']);
    $existente = null;
    if ($ctx['editar'] > 0) {
        $st = db()->prepare('SELECT s.* FROM sumarios s JOIN atribuicoes a ON a.id = s.atribuicao_id WHERE s.id = ? AND a.docente_id = ? AND s.estado <> ?');
        $st->execute([$ctx['editar'], $u['id'], 'validado']);
        $existente = $st->fetch() ?: null;
        if ($existente) {
            $ctx['atribuicao'] = (int) $existente['atribuicao_id'];
            $ctx['data'] = $existente['data_aula'];
            $ctx['horario'] = (int) $existente['horario_id'];
            $atrib = atribuicao_do_docente($ctx['atribuicao'], $u['id'], $periodo['id']);
        }
    }
    $dataValida = DateTime::createFromFormat('Y-m-d', $ctx['data']);
    if (!$atrib || ($ctx['editar'] > 0 && !$existente)) {
        $erro = 'Aula ou atribuição inválida.';
    } elseif (!$dataValida || $dataValida->format('Y-m-d') !== $ctx['data']) {
        $erro = 'Data inválida.';
    } elseif (!$existente && ($ctx['data'] < $limiteInferior || $ctx['data'] > $hoje || $ctx['data'] < $periodo['inicio'] || $ctx['data'] > $periodo['fim'])) {
        $erro = 'A data deve estar nos últimos 7 dias e dentro do período letivo.';
    } elseif ($ctx['sumario'] === '' || mb_strlen($ctx['sumario']) > 500) {
        $erro = 'O sumário é obrigatório e pode ter até 500 caracteres.';
    } elseif ($ctx['duracao'] < 1 || $ctx['duracao'] > 6) {
        $erro = 'Escolha uma duração entre 1 e 6 horas.';
    } elseif (!$existente && $ctx['horario'] > 0) {
        $st = db()->prepare('SELECT dia_semana FROM horarios WHERE id = ? AND atribuicao_id = ?');
        $st->execute([$ctx['horario'], $atrib['id']]);
        $dia = $st->fetchColumn();
        if ($dia === false || (int) $dia !== (int) $dataValida->format('N')) {
            $erro = 'Essa aula não consta do horário neste dia.';
        }
    }

    if ($erro === '') {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            if ($existente) {
                $pdo->prepare("UPDATE sumarios SET sumario = ?, duracao_horas = ?, estado = 'registado', nota = NULL, validado_por = NULL, validado_em = NULL WHERE id = ?")
                    ->execute([$ctx['sumario'], $ctx['duracao'], $existente['id']]);
                $sid = (int) $existente['id'];
            } else {
                $pdo->prepare('INSERT INTO sumarios (atribuicao_id, horario_id, data_aula, hora_registo, sumario, duracao_horas) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute([$atrib['id'], $ctx['horario'] > 0 ? $ctx['horario'] : null, $ctx['data'], date('H:i:s'), $ctx['sumario'], $ctx['duracao']]);
                $sid = (int) $pdo->lastInsertId();
            }
            $ins = $pdo->prepare('INSERT INTO presencas (sumario_id, estudante_id, estado) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE estado = VALUES(estado)');
            foreach (alunos_da_turma($atrib['turma_id']) as $a) {
                $v = $ctx['estados'][$a['id']] ?? 'presente';
                $ins->execute([$sid, $a['id'], in_array($v, $estadosValidos, true) ? $v : 'presente']);
            }
            $pdo->commit();
            auditar($existente ? 'editar' : 'criar', 'sumarios', 'id=' . $sid);
            flash('sucesso', $existente ? 'Sumário atualizado e enviado para nova validação.' : 'Sumário registado com sucesso.');
            redirect('sumarios.php?ver=' . $sid);
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($ex->getMessage());
            $erro = ((int) ($ex->errorInfo[1] ?? 0) === 1062) ? 'Esta aula já tem um sumário registado.' : 'Não foi possível guardar o sumário. Tente de novo.';
        }
    }
}

// Contexto vindo do endereço (GET)
if ($ctx === null) {
    $data = (string) ($_GET['data'] ?? $hoje);
    if (isset($_GET['editar'])) {
        $st = db()->prepare('SELECT s.* FROM sumarios s JOIN atribuicoes a ON a.id = s.atribuicao_id WHERE s.id = ? AND a.docente_id = ? AND s.estado <> ?');
        $st->execute([(int) $_GET['editar'], $u['id'], 'validado']);
        if ($s = $st->fetch()) {
            $pr = db()->prepare('SELECT estudante_id, estado FROM presencas WHERE sumario_id = ?');
            $pr->execute([$s['id']]);
            $ctx = ['editar' => (int) $s['id'], 'atribuicao' => (int) $s['atribuicao_id'], 'horario' => (int) $s['horario_id'], 'data' => $s['data_aula'],
                    'sumario' => $s['sumario'], 'duracao' => (int) $s['duracao_horas'], 'estados' => $pr->fetchAll(PDO::FETCH_KEY_PAIR)];
        } else {
            flash('erro', 'Este sumário não pode ser editado.');
            redirect('sumarios.php');
        }
    } elseif (isset($_GET['horario'])) {
        $st = db()->prepare('SELECT h.id, h.atribuicao_id, h.hora_inicio, h.hora_fim FROM horarios h JOIN atribuicoes a ON a.id = h.atribuicao_id WHERE h.id = ? AND a.docente_id = ? AND a.periodo_id = ?');
        $st->execute([(int) $_GET['horario'], $u['id'], $periodo['id']]);
        if ($h = $st->fetch()) {
            $dur = max(1, (int) round((strtotime($h['hora_fim']) - strtotime($h['hora_inicio'])) / 3600));
            $ctx = ['editar' => 0, 'atribuicao' => (int) $h['atribuicao_id'], 'horario' => (int) $h['id'], 'data' => $data, 'sumario' => '', 'duracao' => $dur, 'estados' => []];
        }
    } elseif (isset($_GET['extra']) && (int) ($_GET['atribuicao'] ?? 0) > 0 && atribuicao_do_docente((int) $_GET['atribuicao'], $u['id'], $periodo['id'])) {
        $ctx = ['editar' => 0, 'atribuicao' => (int) $_GET['atribuicao'], 'horario' => 0, 'data' => $data, 'sumario' => '', 'duracao' => 2, 'estados' => []];
    }
}

render_header($ctx && $ctx['editar'] ? 'Editar sumário' : 'Registar sumário', 'sumario_novo.php');

if ($ctx === null) {
    $data = (string) ($_GET['data'] ?? $hoje);
    $dv = DateTime::createFromFormat('Y-m-d', $data);
    if (!$dv || $dv->format('Y-m-d') !== $data || $data > $hoje || $data < $limiteInferior) {
        $data = $hoje;
    }
    pagina_titulo('Registar sumário', 'Escolha a aula que deu');
    echo '<section class="cartao"><form class="filtros" method="get" action="sumario_novo.php"><div class="campo"><label for="data">Dia da aula</label>';
    echo '<input id="data" type="date" name="data" value="' . e($data) . '" min="' . e($limiteInferior) . '" max="' . e($hoje) . '"></div><button class="btn btn-sec" type="submit">Ver aulas</button></form>';
    $aulas = aulas_previstas($periodo, $data, $data, ['docente_id' => $u['id']]);
    if (!$aulas) {
        echo '<p class="vazio">Não tem aulas previstas para ' . e(data_pt($data)) . '.</p>';
    }
    foreach ($aulas as $a) {
        echo '<div class="aula"><div class="aula-hora"><strong>' . e(hora_curta($a['hora_inicio'])) . '</strong><small>' . e(hora_curta($a['hora_fim'])) . '</small></div>';
        echo '<div class="aula-corpo"><strong>' . e($a['uc']) . '</strong><span class="suave">' . e($a['turma_id']) . ' · ' . e($a['sala'] ?: 'Sem sala') . '</span></div><div class="aula-acao">';
        if ($a['sumario']) {
            echo badge_estado($a['sumario']['estado']);
            if ($a['sumario']['estado'] !== 'validado') {
                echo ' <a class="btn btn-peq btn-sec" href="sumario_novo.php?editar=' . (int) $a['sumario']['id'] . '">Editar</a>';
            }
        } else {
            echo '<a class="btn btn-peq" href="sumario_novo.php?horario=' . (int) $a['horario_id'] . '&amp;data=' . e($data) . '">Registar</a>';
        }
        echo '</div></div>';
    }
    echo '</section>';

    $st = db()->prepare("SELECT a.id, CONCAT(a.turma_id, ' | ', u.nome) FROM atribuicoes a JOIN ucs u ON u.id = a.uc_id WHERE a.docente_id = ? AND a.periodo_id = ? ORDER BY a.turma_id, u.nome");
    $st->execute([$u['id'], $periodo['id']]);
    $minhas = $st->fetchAll(PDO::FETCH_KEY_PAIR);
    echo '<section class="cartao"><h2>Aula fora do horário</h2><p class="suave">Aulas de reposição ou extras, que não constam do horário.</p>';
    if (!$minhas) {
        echo '<p class="vazio">Não tem disciplinas atribuídas neste período.</p>';
    } else {
        echo '<form class="filtros" method="get" action="sumario_novo.php"><input type="hidden" name="extra" value="1"><input type="hidden" name="data" value="' . e($data) . '">';
        echo '<div class="campo"><label for="atribuicao">Disciplina</label><select id="atribuicao" name="atribuicao" required><option value="">Selecione</option>';
        foreach ($minhas as $id => $rot) {
            echo '<option value="' . e($id) . '">' . e($rot) . '</option>';
        }
        echo '</select></div><button class="btn btn-sec" type="submit">Continuar</button></form>';
    }
    echo '</section>';
    render_footer();
    exit;
}

// Formulário do sumário
$atrib = atribuicao_do_docente($ctx['atribuicao'], $u['id'], $periodo['id']);
if (!$atrib) {
    flash('erro', 'Aula inválida.');
    redirect('sumario_novo.php');
}
$alunos = alunos_da_turma($atrib['turma_id']);
pagina_titulo($ctx['editar'] ? 'Editar sumário' : 'Registar sumário', $atrib['uc'] . ' · ' . $atrib['turma_id'] . ' · ' . data_pt($ctx['data']));
if ($erro) {
    echo '<div class="alerta alerta-erro">' . e($erro) . '</div>';
}
?>
<form method="post" action="sumario_novo.php">
  <?= csrf_campo() ?>
  <input type="hidden" name="editar" value="<?= (int) $ctx['editar'] ?>">
  <input type="hidden" name="atribuicao" value="<?= (int) $ctx['atribuicao'] ?>">
  <input type="hidden" name="horario" value="<?= (int) $ctx['horario'] ?>">
  <input type="hidden" name="data" value="<?= e($ctx['data']) ?>">
  <section class="cartao">
    <div class="grade-form">
      <div class="campo largo"><label for="sumario">O que foi lecionado</label>
        <textarea id="sumario" name="sumario" rows="4" maxlength="500" required placeholder="Descreva os conteúdos desta aula"><?= e($ctx['sumario']) ?></textarea></div>
      <div class="campo"><label for="duracao">Duração</label>
        <select id="duracao" name="duracao" required>
          <?php for ($h = 1; $h <= 6; $h++): ?><option value="<?= $h ?>"<?= (int) $ctx['duracao'] === $h ? ' selected' : '' ?>><?= $h ?> hora<?= $h > 1 ? 's' : '' ?></option><?php endfor; ?>
        </select></div>
    </div>
  </section>
  <section class="cartao">
    <h2>Presenças (<?= count($alunos) ?> estudantes)</h2>
    <?php if (!$alunos): ?>
      <p class="vazio">Esta turma ainda não tem estudantes ativos. Pode guardar o sumário na mesma.</p>
    <?php else: ?>
      <div class="acoes-form acoes-marcar">
        <button class="btn btn-sec btn-peq" type="button" data-marcar="presente">Todos presentes</button>
        <button class="btn btn-sec btn-peq" type="button" data-marcar="falta">Todos com falta</button>
      </div>
      <?php foreach ($alunos as $a): $atual = $ctx['estados'][$a['id']] ?? 'presente'; ?>
        <div class="aluno">
          <span class="aluno-nome"><?= e($a['nome']) ?></span>
          <div class="seg" role="radiogroup" aria-label="Presença de <?= e($a['nome']) ?>">
            <?php foreach (['presente' => 'Presente', 'falta' => 'Falta', 'justificada' => 'Just.'] as $valor => $rot): ?>
              <label class="seg-op seg-<?= $valor ?>"><input type="radio" name="estado[<?= e($a['id']) ?>]" value="<?= $valor ?>"<?= $atual === $valor ? ' checked' : '' ?>><span><?= $rot ?></span></label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
  <div class="barra-fixa">
    <button class="btn" type="submit"><?= $ctx['editar'] ? 'Guardar alterações' : 'Guardar sumário' ?></button>
    <a class="btn btn-sec" href="sumario_novo.php">Cancelar</a>
  </div>
</form>
<?php render_footer();
