<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
$u = exigir_login('docente');

$turmas = db()->query('SELECT id FROM turmas ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
$st = db()->prepare('SELECT u.id, u.nome FROM ucs u JOIN docente_uc du ON du.uc_id = u.id WHERE du.docente_id = ? ORDER BY u.nome');
$st->execute([$u['id']]);
$ucs = $st->fetchAll(PDO::FETCH_KEY_PAIR);
if (!$ucs) { // sem associações definidas: mostra todas as UCs
    $ucs = db()->query('SELECT id, nome FROM ucs ORDER BY nome')->fetchAll(PDO::FETCH_KEY_PAIR);
}

$erro = '';
$acao = $_POST['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    if ($acao === 'passo1') {
        $turma = (string) ($_POST['turma'] ?? '');
        $uc = (string) ($_POST['uc'] ?? '');
        $texto = trim((string) ($_POST['sumario'] ?? ''));
        $horas = (int) ($_POST['duracao'] ?? 0);
        if (!in_array($turma, $turmas, true) || !isset($ucs[$uc])) {
            $erro = 'Escolha uma turma e uma unidade curricular válidas.';
        } elseif ($texto === '' || mb_strlen($texto) > 500) {
            $erro = 'O sumário é obrigatório e pode ter até 500 caracteres.';
        } elseif ($horas < 1 || $horas > 4) {
            $erro = 'Escolha uma duração entre 1 e 4 horas.';
        } else {
            $_SESSION['rascunho_sumario'] = ['turma' => $turma, 'uc' => $uc, 'sumario' => $texto, 'horas' => $horas];
            redirect('sumario_novo.php?passo=2');
        }
    }

    if ($acao === 'passo2') {
        $r = $_SESSION['rascunho_sumario'] ?? null;
        if (!$r) {
            redirect('sumario_novo.php');
        }
        $st = db()->prepare('SELECT id FROM estudantes WHERE turma_id = ?');
        $st->execute([$r['turma']]);
        $alunos = $st->fetchAll(PDO::FETCH_COLUMN);
        $presentes = array_map('intval', (array) ($_POST['presentes'] ?? []));

        $pdo = db();
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO sumarios (docente_id, turma_id, uc_id, data_registro, sumario, duracao_horas) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$u['id'], $r['turma'], $r['uc'], date('Y-m-d'), $r['sumario'], $r['horas']]);
            $sumarioId = (int) $pdo->lastInsertId();
            $ins = $pdo->prepare('INSERT INTO presencas_estudante (sumario_id, estudante_id, presente) VALUES (?, ?, ?)');
            foreach ($alunos as $idAluno) {
                $ins->execute([$sumarioId, $idAluno, in_array((int) $idAluno, $presentes, true) ? 1 : 0]);
            }
            $pdo->commit();
        } catch (PDOException $ex) {
            $pdo->rollBack();
            error_log($ex->getMessage());
            flash('erro', 'Não foi possível guardar o sumário. Tente de novo.');
            redirect('sumario_novo.php?passo=2');
        }
        unset($_SESSION['rascunho_sumario']);
        flash('sucesso', 'Sumário registado com sucesso.');
        redirect('sumarios.php');
    }
}

$passo = ($_GET['passo'] ?? '1') === '2' && !empty($_SESSION['rascunho_sumario']) ? 2 : 1;
render_header('Registar sumário', 'sumario_novo.php');
pagina_titulo('Registar sumário', date('d/m/Y'));

if ($passo === 1) {
    $r = $_SESSION['rascunho_sumario'] ?? ['turma' => '', 'uc' => '', 'sumario' => '', 'horas' => ''];
    if ($erro) { echo '<div class="alerta alerta-erro">' . e($erro) . '</div>'; }
    ?>
    <section class="cartao">
      <div class="passos"><span class="passo ativo">1. Aula</span><span class="passo">2. Presenças</span></div>
      <form method="post" class="grade-form">
        <?= csrf_campo() ?><input type="hidden" name="acao" value="passo1">
        <div class="campo"><label for="turma">Turma</label>
          <select id="turma" name="turma" required><option value="">Selecione</option>
            <?php foreach ($turmas as $t): ?><option value="<?= e($t) ?>"<?= $t === $r['turma'] ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
          </select></div>
        <div class="campo"><label for="uc">Unidade curricular</label>
          <select id="uc" name="uc" required><option value="">Selecione</option>
            <?php foreach ($ucs as $id => $nome): ?><option value="<?= e($id) ?>"<?= (string) $id === $r['uc'] ? ' selected' : '' ?>><?= e($nome) ?></option><?php endforeach; ?>
          </select></div>
        <div class="campo largo"><label for="sumario">Sumário</label>
          <textarea id="sumario" name="sumario" rows="3" maxlength="500" required><?= e($r['sumario']) ?></textarea></div>
        <div class="campo"><label for="duracao">Duração</label>
          <select id="duracao" name="duracao" required><option value="">Selecione</option>
            <?php for ($h = 1; $h <= 4; $h++): ?><option value="<?= $h ?>"<?= (int) $r['horas'] === $h ? ' selected' : '' ?>><?= $h ?> hora<?= $h > 1 ? 's' : '' ?></option><?php endfor; ?>
          </select></div>
        <div class="acoes-form"><button class="btn" type="submit">Seguinte</button></div>
      </form>
    </section>
    <?php
} else {
    $r = $_SESSION['rascunho_sumario'];
    $st = db()->prepare('SELECT id, nome FROM estudantes WHERE turma_id = ? ORDER BY nome');
    $st->execute([$r['turma']]);
    $alunos = $st->fetchAll();
    ?>
    <section class="cartao">
      <div class="passos"><span class="passo feito">1. Aula</span><span class="passo ativo">2. Presenças</span></div>
      <p><strong><?= e($ucs[$r['uc']] ?? $r['uc']) ?></strong> <span class="badge"><?= e($r['turma']) ?></span> <span class="suave"><?= (int) $r['horas'] ?> h</span></p>
      <p class="suave"><?= e($r['sumario']) ?></p>
      <form method="post">
        <?= csrf_campo() ?><input type="hidden" name="acao" value="passo2">
        <?php if (!$alunos): ?>
          <p class="vazio">Esta turma ainda não tem estudantes. Pode guardar o sumário mesmo assim.</p>
        <?php else: ?>
          <p class="suave">Todos vêm marcados como presentes. Desmarque quem faltou.</p>
          <div class="tabela-wrap"><table><thead><tr><th>Código</th><th>Nome</th><th class="col-acoes">Presente</th></tr></thead><tbody>
            <?php foreach ($alunos as $a): ?>
              <tr><td><?= e($a['id']) ?></td><td><?= e($a['nome']) ?></td>
                <td class="col-acoes"><input class="check" type="checkbox" name="presentes[]" value="<?= e($a['id']) ?>" checked aria-label="Presente: <?= e($a['nome']) ?>"></td></tr>
            <?php endforeach; ?>
          </tbody></table></div>
        <?php endif; ?>
        <div class="acoes-form"><button class="btn" type="submit">Guardar sumário</button> <a class="btn btn-sec" href="sumario_novo.php">Voltar</a></div>
      </form>
    </section>
    <?php
}
render_footer();
