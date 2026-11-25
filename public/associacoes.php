<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/layout.php';
exigir_login('admin');

// Tabelas e colunas vêm desta lista fixa (nunca do utilizador).
$tipos = [
    'curso' => ['aba' => 'Curso e UC', 'rotulo' => 'Curso', 'pivot' => 'curso_uc', 'col' => 'curso_id', 'tabela' => 'cursos'],
    'docente' => ['aba' => 'Docente e UC', 'rotulo' => 'Docente', 'pivot' => 'docente_uc', 'col' => 'docente_id', 'tabela' => 'docentes'],
    'estudante' => ['aba' => 'Estudante e UC', 'rotulo' => 'Estudante', 'pivot' => 'estudante_uc', 'col' => 'estudante_id', 'tabela' => 'estudantes'],
];
$chave = (string) ($_GET['tipo'] ?? 'curso');
if (!isset($tipos[$chave])) {
    $chave = 'curso';
}
$t = $tipos[$chave];
$self = 'associacoes.php?tipo=' . $chave;

$donos = db()->query("SELECT id, CONCAT(id, ' - ', nome) FROM {$t['tabela']} ORDER BY nome")->fetchAll(PDO::FETCH_KEY_PAIR);
$ucs = db()->query("SELECT id, CONCAT(id, ' - ', nome) FROM ucs ORDER BY nome")->fetchAll(PDO::FETCH_KEY_PAIR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $dono = (string) ($_POST['dono'] ?? '');
    $uc = (string) ($_POST['uc'] ?? '');
    if (($_POST['acao'] ?? '') === 'adicionar') {
        if (!isset($donos[$dono]) || !isset($ucs[$uc])) {
            flash('erro', 'Escolha valores válidos.');
        } else {
            $st = db()->prepare("INSERT IGNORE INTO {$t['pivot']} ({$t['col']}, uc_id) VALUES (?, ?)");
            $st->execute([$dono, $uc]);
            flash($st->rowCount() ? 'sucesso' : 'aviso', $st->rowCount() ? 'Associação criada.' : 'Essa associação já existe.');
        }
    } elseif (($_POST['acao'] ?? '') === 'remover') {
        db()->prepare("DELETE FROM {$t['pivot']} WHERE {$t['col']} = ? AND uc_id = ?")->execute([$dono, $uc]);
        flash('sucesso', 'Associação removida.');
    }
    redirect($self);
}

$q = trim((string) ($_GET['q'] ?? ''));
$params = [];
$onde = '';
if ($q !== '') {
    $onde = ' WHERE (d.nome LIKE ? OR u.nome LIKE ? OR u.id LIKE ?)';
    $params = ["%$q%", "%$q%", "%$q%"];
}
$st = db()->prepare(
    "SELECT d.id AS dono_id, d.nome AS dono, u.id AS uc_id, u.nome AS uc
       FROM {$t['pivot']} p JOIN {$t['tabela']} d ON d.id = p.{$t['col']} JOIN ucs u ON u.id = p.uc_id
       $onde ORDER BY d.nome, u.nome LIMIT 300"
);
$st->execute($params);
$linhas = $st->fetchAll();

render_header('Associações', 'associacoes.php');
pagina_titulo('Associações', 'Ligue unidades curriculares a cursos, docentes e estudantes');
echo '<div class="abas">';
foreach ($tipos as $k => $a) {
    echo '<a class="aba' . ($k === $chave ? ' ativa' : '') . '" href="associacoes.php?tipo=' . e($k) . '">' . e($a['aba']) . '</a>';
}
echo '</div>';
?>
<section class="cartao">
  <h2>Nova associação</h2>
  <form method="post" action="<?= e($self) ?>" class="grade-form">
    <?= csrf_campo() ?><input type="hidden" name="acao" value="adicionar">
    <div class="campo"><label for="dono"><?= e($t['rotulo']) ?></label>
      <select id="dono" name="dono" required><option value="">Selecione</option>
        <?php foreach ($donos as $id => $rot): ?><option value="<?= e($id) ?>"><?= e($rot) ?></option><?php endforeach; ?></select></div>
    <div class="campo"><label for="uc">Unidade curricular</label>
      <select id="uc" name="uc" required><option value="">Selecione</option>
        <?php foreach ($ucs as $id => $rot): ?><option value="<?= e($id) ?>"><?= e($rot) ?></option><?php endforeach; ?></select></div>
    <div class="acoes-form"><button class="btn" type="submit">Associar</button></div>
  </form>
</section>
<section class="cartao">
  <form class="busca" method="get" action="associacoes.php">
    <input type="hidden" name="tipo" value="<?= e($chave) ?>">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar"><button class="btn btn-sec" type="submit">Pesquisar</button>
  </form>
  <?php if (!$linhas): ?><p class="vazio">Sem associações.</p><?php else: ?>
  <div class="tabela-wrap"><table><thead><tr><th><?= e($t['rotulo']) ?></th><th>Unidade curricular</th><th class="col-acoes">Ações</th></tr></thead><tbody>
    <?php foreach ($linhas as $l): ?>
      <tr><td><?= e($l['dono']) ?></td><td><?= e($l['uc_id'] . ' - ' . $l['uc']) ?></td>
        <td class="col-acoes"><form method="post" action="<?= e($self) ?>" class="em-linha" data-confirmar="Remover esta associação?">
          <?= csrf_campo() ?><input type="hidden" name="acao" value="remover">
          <input type="hidden" name="dono" value="<?= e($l['dono_id']) ?>"><input type="hidden" name="uc" value="<?= e($l['uc_id']) ?>">
          <button class="btn btn-peq btn-perigo" type="submit">Remover</button></form></td></tr>
    <?php endforeach; ?>
  </tbody></table></div>
  <?php endif; ?>
</section>
<?php render_footer();
