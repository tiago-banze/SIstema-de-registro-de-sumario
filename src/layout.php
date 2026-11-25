<?php
declare(strict_types=1);

function menu_itens(string $tipo): array
{
    if ($tipo === 'admin') {
        return [
            ['Painel', 'dashboard.php', null],
            ['Cadastros', null, [
                ['Cursos', 'cursos.php'], ['Turmas', 'turmas.php'], ['Unidades curriculares', 'ucs.php'],
                ['Estudantes', 'estudantes.php'], ['Docentes', 'docentes.php'], ['Faculdades', 'faculdades.php'],
            ]],
            ['Associações', 'associacoes.php', null],
            ['Sumários', 'sumarios.php', null],
            ['Presenças', 'presencas.php', null],
        ];
    }
    return [
        ['Painel', 'dashboard.php', null],
        ['Registar sumário', 'sumario_novo.php', null],
        ['Meus sumários', 'sumarios.php', null],
    ];
}

function render_header(string $titulo, string $ativo = ''): void
{
    $u = usuario();
    $logo = '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6.5C10.5 5 8 4.5 4 4.5v13c4 0 6.5.5 8 2 1.5-1.5 4-2 8-2v-13c-4 0-6.5.5-8 2z"/><path d="M12 6.5v13"/></svg>';
    ?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?> | Registo de Sumário</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect width='24' height='24' rx='6' fill='%230e7c86'/%3E%3Cpath d='M12 7c-1.4-1.3-3.5-1.8-6-1.8v11.6c2.5 0 4.6.5 6 1.8 1.4-1.3 3.5-1.8 6-1.8V5.2c-2.5 0-4.6.5-6 1.8z' fill='none' stroke='white' stroke-width='1.6'/%3E%3C/svg%3E">
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<header class="topbar">
  <button class="menu-btn" type="button" aria-label="Abrir menu" data-menu>&#9776;</button>
  <span class="topbar-titulo"><?= e($titulo) ?></span>
</header>
<aside class="sidebar" id="sidebar">
  <a class="marca" href="dashboard.php"><?= $logo ?><span>Registo de<br>Sumário</span></a>
  <nav>
    <?php foreach (menu_itens($u['tipo']) as [$rotulo, $link, $filhos]): ?>
      <?php if ($filhos === null): ?>
        <a class="nav-link<?= $ativo === $link ? ' ativo' : '' ?>" href="<?= e($link) ?>"><?= e($rotulo) ?></a>
      <?php else: ?>
        <div class="nav-grupo"><?= e($rotulo) ?></div>
        <?php foreach ($filhos as [$r, $l]): ?>
          <a class="nav-link sub<?= $ativo === $l ? ' ativo' : '' ?>" href="<?= e($l) ?>"><?= e($r) ?></a>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>
  <div class="usuario">
    <div class="avatar"><?= e(mb_strtoupper(mb_substr($u['nome'], 0, 1))) ?></div>
    <div class="usuario-info"><strong><?= e($u['nome']) ?></strong><small><?= $u['tipo'] === 'admin' ? 'Administrador' : 'Docente' ?></small></div>
    <form method="post" action="logout.php"><?= csrf_campo() ?><button class="sair" type="submit" title="Terminar sessão">Sair</button></form>
  </div>
</aside>
<div class="sidebar-fundo" data-menu></div>
<main class="conteudo">
  <?php foreach (flash_ler() as $f): ?>
    <div class="alerta alerta-<?= e($f['tipo']) ?>" role="status"><?= e($f['msg']) ?></div>
  <?php endforeach; ?>
<?php
}

function render_footer(): void
{
    ?>
</main>
<script src="assets/js/app.js"></script>
</body>
</html>
<?php
}

function badge(bool $ok, string $sim = 'Presente', string $nao = 'Ausente'): string
{
    return '<span class="badge ' . ($ok ? 'badge-ok' : 'badge-erro') . '">' . e($ok ? $sim : $nao) . '</span>';
}

function pagina_titulo(string $titulo, string $subtitulo = '', string $acao = ''): void
{
    echo '<div class="pagina-topo"><div><h1>' . e($titulo) . '</h1>';
    if ($subtitulo !== '') {
        echo '<p class="suave">' . e($subtitulo) . '</p>';
    }
    echo '</div>' . $acao . '</div>';
}
