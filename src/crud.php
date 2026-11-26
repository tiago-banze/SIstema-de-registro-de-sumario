<?php
const CRUD_POR_PAGINA = 15;

function crud_opcoes(array $campo): array
{
    if (isset($campo['opcoes'])) {
        return $campo['opcoes'];
    }
    $saida = [];
    if (isset($campo['opcoes_sql'])) {
        foreach (db()->query($campo['opcoes_sql'])->fetchAll(PDO::FETCH_NUM) as $linha) {
            $saida[(string) $linha[0]] = (string) $linha[1];
        }
    }
    return $saida;
}

function crud_erro_bd(PDOException $ex): string
{
    $codigo = (int) ($ex->errorInfo[1] ?? 0);
    if ($codigo === 1062) {
        return 'Já existe um registo com esse código, email ou combinação de valores.';
    }
    if ($codigo === 1451) {
        return 'Não é possível apagar: existem registos ligados a este.';
    }
    if ($codigo === 1452) {
        return 'Um dos valores escolhidos não existe.';
    }
    error_log($ex->getMessage());
    return 'Ocorreu um erro ao guardar. Tente de novo.';
}

/** Valida o POST e devolve [dados, erros]. */
function crud_validar(array $e, bool $editando, ?string $pkOriginal): array
{
    $dados = [];
    $erros = [];
    foreach ($e['campos'] as $nome => $c) {
        if ($editando && !empty($c['so_criacao'])) {
            continue;
        }
        $coluna = $c['coluna'] ?? $nome;
        $valor = trim((string) ($_POST[$nome] ?? ''));
        $tipo = $c['tipo'];

        if ($tipo === 'password') {
            if ($valor === '') {
                if (!$editando) {
                    $erros[$nome] = 'Defina uma senha.';
                }
                continue;
            }
            if (strlen($valor) < 8) {
                $erros[$nome] = 'A senha deve ter pelo menos 8 caracteres.';
                continue;
            }
            $dados[$coluna] = password_hash($valor, PASSWORD_DEFAULT);
            if (!empty($c['marca_troca'])) {
                $dados[$c['marca_troca']] = 1;
            }
            continue;
        }
        if ($valor === '') {
            if (!empty($c['obrigatorio'])) {
                $erros[$nome] = 'Campo obrigatório.';
            }
            $dados[$coluna] = null;
            continue;
        }
        if (isset($c['max']) && mb_strlen($valor) > $c['max']) {
            $erros[$nome] = 'Máximo de ' . $c['max'] . ' caracteres.';
        } elseif ($tipo === 'email' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $erros[$nome] = 'Email inválido.';
        } elseif ($tipo === 'number' && !ctype_digit($valor)) {
            $erros[$nome] = 'Use apenas números inteiros.';
        } elseif ($tipo === 'date') {
            $d = DateTime::createFromFormat('Y-m-d', $valor);
            if (!$d || $d->format('Y-m-d') !== $valor) {
                $erros[$nome] = 'Data inválida.';
            }
        } elseif ($tipo === 'time' && !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $valor)) {
            $erros[$nome] = 'Hora inválida.';
        } elseif ($tipo === 'select' && !array_key_exists($valor, crud_opcoes($c))) {
            $erros[$nome] = 'Escolha uma opção válida.';
        }
        $dados[$coluna] = $valor;
    }
    if (!$erros && isset($e['validar'])) {
        $erros = ($e['validar'])($dados, $pkOriginal);
    }
    return [$dados, $erros];
}

function crud_guardar(array $e, string $self, ?string $pkOriginal, array &$erros, array &$antigo): void
{
    $editando = $pkOriginal !== null;
    [$dados, $erros] = crud_validar($e, $editando, $pkOriginal);
    $antigo = $_POST;
    if ($erros) {
        return;
    }
    try {
        if ($editando) {
            $sets = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($dados)));
            db()->prepare("UPDATE `{$e['tabela']}` SET $sets WHERE `{$e['pk']}` = ?")->execute([...array_values($dados), $pkOriginal]);
            auditar('editar', $e['tabela'], $e['pk'] . '=' . $pkOriginal);
            flash('sucesso', ucfirst($e['singular']) . ' atualizado com sucesso.');
        } else {
            $cols = implode(', ', array_map(fn($c) => "`$c`", array_keys($dados)));
            $marcas = implode(', ', array_fill(0, count($dados), '?'));
            db()->prepare("INSERT INTO `{$e['tabela']}` ($cols) VALUES ($marcas)")->execute(array_values($dados));
            auditar('criar', $e['tabela'], (string) ($dados[$e['pk']] ?? db()->lastInsertId()));
            flash('sucesso', ucfirst($e['singular']) . ' registado com sucesso.');
        }
        redirect($self);
    } catch (PDOException $ex) {
        $erros['_geral'] = crud_erro_bd($ex);
    }
}

function crud_campo_html(string $nome, array $c, string $valor, ?string $erro, bool $editando): string
{
    $id = 'f_' . $nome;
    $req = !empty($c['obrigatorio']) ? ' required' : '';
    $leitura = ($editando && !empty($c['so_criacao'])) ? ' readonly' : '';
    $h = '<div class="campo' . ($erro ? ' com-erro' : '') . '"><label for="' . e($id) . '">' . e($c['label']) . '</label>';
    if ($c['tipo'] === 'select') {
        $h .= '<select id="' . e($id) . '" name="' . e($nome) . '"' . $req . '><option value="">Selecione</option>';
        foreach (crud_opcoes($c) as $k => $rot) {
            $h .= '<option value="' . e($k) . '"' . ((string) $k === $valor ? ' selected' : '') . '>' . e($rot) . '</option>';
        }
        $h .= '</select>';
    } else {
        $tipo = in_array($c['tipo'], ['text', 'email', 'tel', 'number', 'password', 'date', 'time'], true) ? $c['tipo'] : 'text';
        if ($tipo === 'time') {
            $valor = substr($valor, 0, 5);
        }
        $extra = ($tipo === 'number' ? ' min="0" step="1" inputmode="numeric"' : '') . (isset($c['max']) ? ' maxlength="' . (int) $c['max'] . '"' : '');
        $autoc = $tipo === 'password' ? ' autocomplete="new-password"' : '';
        $h .= '<input id="' . e($id) . '" type="' . $tipo . '" name="' . e($nome) . '" value="' . ($tipo === 'password' ? '' : e($valor)) . '"' . $req . $extra . $leitura . $autoc . '>';
        if ($tipo === 'password' && $editando) {
            $h .= '<small class="suave">Deixe em branco para manter a senha atual. Ao definir uma nova, a pessoa terá de a trocar no primeiro acesso.</small>';
        }
    }
    if (!empty($c['ajuda'])) {
        $h .= '<small class="suave">' . e($c['ajuda']) . '</small>';
    }
    if ($erro) {
        $h .= '<small class="erro-texto">' . e($erro) . '</small>';
    }
    return $h . '</div>';
}

function crud_pagina(string $chave): void
{
    $entidades = require __DIR__ . '/entidades.php';
    $e = $entidades[$chave];
    $self = $chave . '.php';
    $u = exigir_login('admin');

    $erros = [];
    $antigo = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_validar();
        $acao = $_POST['acao'] ?? '';
        if ($acao === 'excluir') {
            $pk = (string) ($_POST['pk'] ?? '');
            if (!empty($e['proteger_proprio']) && $pk === (string) $u['id']) {
                flash('erro', 'Não pode apagar a sua própria conta.');
            } else {
                try {
                    db()->prepare("DELETE FROM `{$e['tabela']}` WHERE `{$e['pk']}` = ?")->execute([$pk]);
                    auditar('apagar', $e['tabela'], $e['pk'] . '=' . $pk);
                    flash('sucesso', ucfirst($e['singular']) . ' apagado.');
                } catch (PDOException $ex) {
                    flash('erro', crud_erro_bd($ex));
                }
            }
            redirect($self);
        }
        if ($acao === 'guardar') {
            $pk = ($_POST['pk_original'] ?? '') !== '' ? (string) $_POST['pk_original'] : null;
            crud_guardar($e, $self, $pk, $erros, $antigo);
        }
    }

    $editarPk = $_GET['editar'] ?? (($antigo['pk_original'] ?? '') !== '' ? $antigo['pk_original'] : null);
    $mostrarForm = isset($_GET['novo']) || $editarPk !== null || $erros;
    $valores = [];
    if ($editarPk !== null) {
        $st = db()->prepare("SELECT * FROM `{$e['tabela']}` WHERE `{$e['pk']}` = ?");
        $st->execute([(string) $editarPk]);
        $linha = $st->fetch();
        if (!$linha) {
            flash('erro', 'Registo não encontrado.');
            redirect($self);
        }
        $valores = $linha;
    } else {
        foreach ($e['campos'] as $n => $c) {
            $valores[$n] = $c['padrao'] ?? '';
        }
    }
    if ($antigo) {
        $valores = array_merge($valores, $antigo);
    }

    $q = trim((string) ($_GET['q'] ?? ''));
    $pagina = max(1, (int) ($_GET['p'] ?? 1));
    $onde = '';
    $params = [];
    if ($q !== '') {
        $onde = ' WHERE (' . implode(' OR ', array_map(fn($c) => "$c LIKE ?", $e['busca'])) . ')';
        $params = array_fill(0, count($e['busca']), '%' . $q . '%');
    }
    $st = db()->prepare("SELECT COUNT(*) FROM ({$e['consulta']}$onde) t");
    $st->execute($params);
    $total = (int) $st->fetchColumn();
    $paginas = max(1, (int) ceil($total / CRUD_POR_PAGINA));
    $pagina = min($pagina, $paginas);
    $desloc = ($pagina - 1) * CRUD_POR_PAGINA;
    $st = db()->prepare("{$e['consulta']}$onde ORDER BY {$e['ordem']} LIMIT " . CRUD_POR_PAGINA . " OFFSET $desloc");
    $st->execute($params);
    $linhas = $st->fetchAll();

    render_header($e['titulo'], $self);
    pagina_titulo($e['titulo'], $total . ' registo(s)', '<a class="btn" href="' . e($self) . '?novo=1">Novo registo</a>');

    if ($mostrarForm) {
        $editando = $editarPk !== null;
        echo '<section class="cartao"><h2>' . ($editando ? 'Editar ' : 'Novo ') . e($e['singular']) . '</h2>';
        if (isset($erros['_geral'])) {
            echo '<div class="alerta alerta-erro">' . e($erros['_geral']) . '</div>';
        }
        echo '<form method="post" action="' . e($self) . '" class="grade-form">' . csrf_campo();
        echo '<input type="hidden" name="acao" value="guardar">';
        echo '<input type="hidden" name="pk_original" value="' . e($editando ? (string) $editarPk : '') . '">';
        foreach ($e['campos'] as $nome => $c) {
            echo crud_campo_html($nome, $c, (string) ($valores[$nome] ?? ''), $erros[$nome] ?? null, $editando);
        }
        echo '<div class="acoes-form"><button class="btn" type="submit">Guardar</button><a class="btn btn-sec" href="' . e($self) . '">Cancelar</a></div></form></section>';
    }

    echo '<section class="cartao"><form class="busca" method="get" action="' . e($self) . '">';
    echo '<input type="search" name="q" value="' . e($q) . '" placeholder="Pesquisar" aria-label="Pesquisar"><button class="btn btn-sec" type="submit">Pesquisar</button></form>';
    if (!$linhas) {
        echo '<p class="vazio">Nenhum registo encontrado.</p>';
    } else {
        echo '<div class="tabela-wrap"><table class="resp"><thead><tr>';
        foreach ($e['colunas'] as $rot) {
            echo '<th>' . e($rot) . '</th>';
        }
        echo '<th class="col-acoes">Ações</th></tr></thead><tbody>';
        foreach ($linhas as $l) {
            echo '<tr>';
            foreach ($e['colunas'] as $col => $rot) {
                echo td($rot, e($l[$col] ?? ''));
            }
            $pkBruto = (string) $l[$e['pk']];
            $acoes = '<a class="btn btn-peq btn-sec" href="' . e($self) . '?editar=' . rawurlencode($pkBruto) . '">Editar</a>';
            $acoes .= '<form method="post" action="' . e($self) . '" class="em-linha" data-confirmar="Apagar este registo?">' . csrf_campo();
            $acoes .= '<input type="hidden" name="acao" value="excluir"><input type="hidden" name="pk" value="' . e($pkBruto) . '">';
            $acoes .= '<button class="btn btn-peq btn-perigo" type="submit">Apagar</button></form>';
            echo td('Ações', $acoes, 'col-acoes') . '</tr>';
        }
        echo '</tbody></table></div>';
        if ($paginas > 1) {
            $base = $self . '?q=' . rawurlencode($q) . '&p=';
            echo '<nav class="paginacao">';
            echo $pagina > 1 ? '<a class="btn btn-sec btn-peq" href="' . e($base . ($pagina - 1)) . '">Anterior</a>' : '';
            echo '<span class="suave">Página ' . $pagina . ' de ' . $paginas . '</span>';
            echo $pagina < $paginas ? '<a class="btn btn-sec btn-peq" href="' . e($base . ($pagina + 1)) . '">Seguinte</a>' : '';
            echo '</nav>';
        }
    }
    echo '</section>';
    render_footer();
}
