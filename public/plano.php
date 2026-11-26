<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
exigir_login('admin');

$cursos = db()->query("SELECT id, CONCAT(id, ' - ', nome) FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_KEY_PAIR);
$ucs = db()->query("SELECT id, CONCAT(id, ' - ', nome) FROM ucs ORDER BY nome")->fetchAll(PDO::FETCH_KEY_PAIR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $curso = (string) ($_POST['curso'] ?? '');
    $uc = (string) ($_POST['uc'] ?? '');
    if (($_POST['acao'] ?? '') === 'adicionar') {
        $ano = (int) ($_POST['ano'] ?? 0);
        $sem = (int) ($_POST['semestre'] ?? 0);
        if (!isset($cursos[$curso]) || !isset($ucs[$uc]) || $ano < 1 || $ano > 6 || $sem < 1 || $sem > 2) {
            flash('erro', 'Escolha valores válidos.');
        } else {
            db()->prepare('INSERT INTO curso_uc (curso_id, uc_id, ano, semestre) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE ano = VALUES(ano), semestre = VALUES(semestre)')
                ->execute([$curso, $uc, $ano, $sem]);
            auditar('plano_adicionar', 'curso_uc', $curso . '/' . $uc);
            flash('sucesso', 'Plano curricular atualizado.');
        }
    } elseif (($_POST['acao'] ?? '') === 'remover') {
        db()->prepare('DELETE FROM curso_uc WHERE curso_id = ? AND uc_id = ?')->execute([$curso, $uc]);
        auditar('plano_remover', 'curso_uc', $curso . '/' . $uc);
        flash('sucesso', 'Disciplina retirada do plano.');
    }
    redirect('plano.php');
}

$filtro = (string) ($_GET['curso'] ?? '');
$params = [];
$onde = '';
if ($filtro !== '' && isset($cursos[$filtro])) {
    $onde = ' WHERE c.id = ?';
    $params[] = $filtro;
}
$st = db()->prepare("SELECT c.id AS curso_id, c.nome AS curso, u.id AS uc_id, u.nome AS uc, p.ano, p.semestre FROM curso_uc p JOIN cursos c ON c.id = p.curso_id JOIN ucs u ON u.id = p.uc_id $onde ORDER BY c.nome, p.ano, p.semestre, u.nome");
$st->execute($params);
$linhas = $st->fetchAll();

render_header('Plano curricular', 'plano.php');
pagina_titulo('Plano curricular', 'Disciplinas de cada curso, por ano e semestre');
?>
<section class="cartao">
  <h2>Adicionar ou atualizar</h2>
  <form method="post" action="plano.php" class="grade-form">
    <?= csrf_campo() ?><input type="hidden" name="acao" value="adicionar">
    <div class="campo"><label for="curso">Curso</label><select id="curso" name="curso" required><option value="">Selecione</option>
      <?php foreach ($cursos as $id => $rot): ?><option value="<?= e($id) ?>"><?= e($rot) ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="uc">Unidade curricular</label><select id="uc" name="uc" required><option value="">Selecione</option>
      <?php foreach ($ucs as $id => $rot): ?><option value="<?= e($id) ?>"><?= e($rot) ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="ano">Ano</label><select id="ano" name="ano" required><?php for ($i = 1; $i <= 6; $i++): ?><option value="<?= $i ?>"><?= $i ?>º ano</option><?php endfor; ?></select></div>
    <div class="campo"><label for="semestre">Semestre</label><select id="semestre" name="semestre" required><option value="1">1º semestre</option><option value="2">2º semestre</option></select></div>
    <div class="acoes-form"><button class="btn" type="submit">Guardar</button></div>
  </form>
</section>
<section class="cartao">
  <form class="filtros" method="get" action="plano.php"><div class="campo"><label for="f">Curso</label><select id="f" name="curso"><option value="">Todos</option>
    <?php foreach ($cursos as $id => $rot): ?><option value="<?= e($id) ?>"<?= (string) $id === $filtro ? ' selected' : '' ?>><?= e($rot) ?></option><?php endforeach; ?></select></div>
    <button class="btn btn-sec" type="submit">Filtrar</button></form>
  <?php if (!$linhas): ?><p class="vazio">Plano vazio.</p><?php else: ?>
  <div class="tabela-wrap"><table class="resp"><thead><tr><th>Curso</th><th>Ano</th><th>Semestre</th><th>Unidade curricular</th><th class="col-acoes">Ações</th></tr></thead><tbody>
    <?php foreach ($linhas as $l):
        $f = '<form method="post" action="plano.php" class="em-linha" data-confirmar="Retirar esta disciplina do plano?">' . csrf_campo()
           . '<input type="hidden" name="acao" value="remover"><input type="hidden" name="curso" value="' . e($l['curso_id']) . '"><input type="hidden" name="uc" value="' . e($l['uc_id']) . '">'
           . '<button class="btn btn-peq btn-perigo" type="submit">Retirar</button></form>'; ?>
      <tr><?= td('Curso', e($l['curso'])) . td('Ano', (int) $l['ano'] . 'º') . td('Semestre', (int) $l['semestre'] . 'º') . td('Unidade curricular', e($l['uc_id'] . ' - ' . $l['uc'])) . td('Ações', $f, 'col-acoes') ?></tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</section>
<?php render_footer();
