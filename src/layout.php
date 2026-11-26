<?php
function icone(string $nome): string
{
    static $d = [
        'painel'   => '<path d="M3 11l9-8 9 8v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/>',
        'registar' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
        'lista'    => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
        'agenda'   => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'grafico'  => '<path d="M5 21V10M12 21V4M19 21v-7"/>',
        'visto'    => '<path d="M20 6L9 17l-5-5"/>',
        'menu'     => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'pessoa'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
    ];
    return '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($d[$nome] ?? '') . '</svg>';
}

function menu_itens(string $tipo): array
{
    if ($tipo === 'admin') {
        return [
            ['Painel', 'dashboard.php', null],
            ['Académico', null, [['Cursos', 'cursos.php'], ['Turmas', 'turmas.php'], ['Unidades curriculares', 'ucs.php'], ['Plano curricular', 'plano.php'], ['Faculdades', 'faculdades.php']]],
            ['Pessoas', null, [['Estudantes', 'estudantes.php'], ['Docentes', 'docentes.php'], ['Administradores', 'administradores.php']]],
            ['Planeamento', null, [['Períodos letivos', 'periodos.php'], ['Atribuições', 'atribuicoes.php'], ['Horários', 'horarios.php'], ['Horário semanal', 'horario.php'], ['Feriados', 'feriados.php']]],
            ['Acompanhamento', null, [['Sumários', 'sumarios.php'], ['Presenças', 'presencas.php'], ['Relatórios', 'relatorios.php'], ['Auditoria', 'auditoria.php']]],
            ['A minha conta', 'perfil.php', null],
        ];
    }
    if ($tipo === 'docente') {
        return [['Painel', 'dashboard.php', null], ['Registar sumário', 'sumario_novo.php', null], ['Meus sumários', 'sumarios.php', null], ['Horário', 'horario.php', null], ['A minha conta', 'perfil.php', null]];
    }
    return [['Painel', 'dashboard.php', null], ['As minhas presenças', 'minhas_presencas.php', null], ['Horário', 'horario.php', null], ['A minha conta', 'perfil.php', null]];
}

function barra_inferior(string $tipo): array
{
    if ($tipo === 'admin') {
        return [['Painel', 'dashboard.php', 'painel'], ['Sumários', 'sumarios.php', 'lista'], ['Presenças', 'presencas.php', 'visto'], ['Relatórios', 'relatorios.php', 'grafico']];
    }
    if ($tipo === 'docente') {
        return [['Painel', 'dashboard.php', 'painel'], ['Registar', 'sumario_novo.php', 'registar'], ['Sumários', 'sumarios.php', 'lista'], ['Horário', 'horario.php', 'agenda']];
    }
    return [['Painel', 'dashboard.php', 'painel'], ['Presenças', 'minhas_presencas.php', 'visto'], ['Horário', 'horario.php', 'agenda']];
}

function render_header(string $titulo, string $ativo = ''): void
{
    $u = usuario();
    $logo = '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6.5C10.5 5 8 4.5 4 4.5v13c4 0 6.5.5 8 2 1.5-1.5 4-2 8-2v-13c-4 0-6.5.5-8 2z"/><path d="M12 6.5v13"/></svg>';
    $rotulos = ['admin' => 'Administrador', 'docente' => 'Docente', 'estudante' => 'Estudante'];
    ?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#12263a">
  <title><?= e($titulo) ?> | Registo de Sumário</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect width='24' height='24' rx='6' fill='%230e7c86'/%3E%3Cpath d='M12 7c-1.4-1.3-3.5-1.8-6-1.8v11.6c2.5 0 4.6.5 6 1.8 1.4-1.3 3.5-1.8 6-1.8V5.2c-2.5 0-4.6.5-6 1.8z' fill='none' stroke='white' stroke-width='1.6'/%3E%3C/svg%3E">
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<header class="topbar">
  <button class="menu-btn" type="button" aria-label="Abrir menu" data-menu><?= icone('menu') ?></button>
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
    <div class="usuario-info"><strong><?= e($u['nome']) ?></strong><small><?= e($rotulos[$u['tipo']] ?? '') ?></small></div>
    <form method="post" action="logout.php"><?= csrf_campo() ?><button class="sair" type="submit">Sair</button></form>
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
    $u = usuario();
    echo '</main>';
    if ($u) {
        echo '<nav class="bottomnav" aria-label="Navegação principal">';
        foreach (barra_inferior($u['tipo']) as [$rot, $link, $ic]) {
            $ativo = basename($_SERVER['SCRIPT_NAME']) === $link ? ' ativo' : '';
            echo '<a class="bn-item' . $ativo . '" href="' . e($link) . '">' . icone($ic) . '<span>' . e($rot) . '</span></a>';
        }
        echo '<button class="bn-item" type="button" data-menu>' . icone('menu') . '<span>Menu</span></button></nav>';
    }
    echo '<script src="assets/js/app.js"></script></body></html>';
}

function pagina_titulo(string $titulo, string $subtitulo = '', string $acao = ''): void
{
    echo '<div class="pagina-topo"><div><h1>' . e($titulo) . '</h1>';
    if ($subtitulo !== '') {
        echo '<p class="suave">' . e($subtitulo) . '</p>';
    }
    echo '</div>' . $acao . '</div>';
}

function td(string $rotulo, string $html, string $classe = ''): string
{
    return '<td data-label="' . e($rotulo) . '"' . ($classe !== '' ? ' class="' . e($classe) . '"' : '') . '>' . $html . '</td>';
}

function badge_estado(string $estado): string
{
    $mapa = [
        'presente' => ['badge-ok', 'Presente'], 'falta' => ['badge-erro', 'Falta'], 'justificada' => ['badge-aviso', 'Justificada'],
        'registado' => ['badge-aviso', 'Por validar'], 'validado' => ['badge-ok', 'Validado'], 'rejeitado' => ['badge-erro', 'Rejeitado'],
    ];
    [$classe, $texto] = $mapa[$estado] ?? ['', $estado];
    return '<span class="badge ' . $classe . '">' . e($texto) . '</span>';
}

function progresso($pct, string $nivel = ''): string
{
    $p = max(0, min(100, (int) round((float) $pct)));
    return '<div class="barra"><div class="barra-preench ' . e($nivel) . '" style="width:' . $p . '%"></div></div>';
}
