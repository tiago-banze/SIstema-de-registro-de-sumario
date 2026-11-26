<?php
define('BASE_PATH', dirname(__DIR__));
const TABELAS_USUARIO = ['admin' => 'administradores', 'docente' => 'docentes', 'estudante' => 'estudantes'];

$arquivoConfig = BASE_PATH . '/config/config.php';
if (!is_file($arquivoConfig)) {
    http_response_code(500);
    exit('Falta o ficheiro config/config.php. Copie config/config.example.php e ajuste os dados.');
}
$GLOBALS['config'] = require $arquivoConfig;
date_default_timezone_set($GLOBALS['config']['timezone'] ?? 'Africa/Maputo');

function config(string $chave, $padrao = null)
{
    return $GLOBALS['config'][$chave] ?? $padrao;
}

/* Cabeçalhos de segurança */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
if ($https) {
    header('Strict-Transport-Security: max-age=15552000');
}

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
session_start();

/* Base de dados */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = $GLOBALS['config']['db'];
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['nome']);
        try {
            $pdo = new PDO($dsn, $c['usuario'], $c['senha'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            exit('Não foi possível ligar à base de dados. Verifique config/config.php e se o MySQL está ligado.');
        }
    }
    return $pdo;
}

/* Utilitários */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'msg' => $mensagem];
}

function flash_ler(): array
{
    $itens = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $itens;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_validar(): void
{
    $enviado = (string) ($_POST['csrf'] ?? '');
    if ($enviado === '' || !hash_equals(csrf_token(), $enviado)) {
        http_response_code(419);
        exit('Pedido inválido ou expirado. Volte atrás e tente de novo.');
    }
}

function data_pt($data): string
{
    $t = strtotime((string) $data);
    return $t ? date('d/m/Y', $t) : (string) $data;
}

function hora_curta($hora): string
{
    return substr((string) $hora, 0, 5);
}

function ip_cliente(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

function auditar(string $acao, string $entidade = '', string $detalhe = ''): void
{
    try {
        $u = usuario();
        db()->prepare('INSERT INTO auditoria (quando, usuario_tipo, usuario_id, acao, entidade, detalhe, ip) VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([date('Y-m-d H:i:s'), $u['tipo'] ?? null, $u['id'] ?? null, $acao, $entidade, mb_substr($detalhe, 0, 255), ip_cliente()]);
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
    }
}

function periodo_ativo(): ?array
{
    static $p = false;
    if ($p === false) {
        $p = db()->query('SELECT * FROM periodos WHERE ativo = 1 ORDER BY inicio DESC LIMIT 1')->fetch() ?: null;
    }
    return $p;
}

/* Autenticação */
function usuario(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function encerrar_sessao(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

function exigir_login($tipo = null, bool $permitirTroca = false): array
{
    $u = usuario();
    $limite = ((int) config('sessao_minutos', 30)) * 60;
    if ($u !== null && time() - ($_SESSION['ultima_atividade'] ?? time()) > $limite) {
        encerrar_sessao();
        flash('aviso', 'A sessão expirou por inatividade. Entre novamente.');
        $u = null;
    }
    if ($u === null) {
        redirect('index.php');
    }
    if ($tipo !== null && !in_array($u['tipo'], (array) $tipo, true)) {
        http_response_code(403);
        exit('Acesso negado.');
    }
    if (!$permitirTroca && !empty($_SESSION['trocar_senha'])) {
        redirect('perfil.php');
    }
    $_SESSION['ultima_atividade'] = time();
    return $u;
}

function login_bloqueado(string $ident): bool
{
    $desde = date('Y-m-d H:i:s', time() - ((int) config('bloqueio_minutos', 15)) * 60);
    $ip = ip_cliente();
    $st = db()->prepare('SELECT COUNT(*) FROM tentativas_login WHERE criado_em > ? AND identificador = ? AND ip = ?');
    $st->execute([$desde, $ident, $ip]);
    if ((int) $st->fetchColumn() >= (int) config('max_tentativas_login', 5)) {
        return true;
    }
    $st = db()->prepare('SELECT COUNT(*) FROM tentativas_login WHERE criado_em > ? AND ip = ?');
    $st->execute([$desde, $ip]);
    return (int) $st->fetchColumn() >= 30;
}

function registar_falha_login(string $ident): void
{
    db()->prepare('INSERT INTO tentativas_login (identificador, ip, criado_em) VALUES (?, ?, ?)')
        ->execute([$ident, ip_cliente(), date('Y-m-d H:i:s')]);
    db()->prepare('DELETE FROM tentativas_login WHERE criado_em < ?')
        ->execute([date('Y-m-d H:i:s', time() - 86400)]);
}

/** Devolve 'ok', 'bloqueado' ou 'invalido'. */
function autenticar(string $email, string $senha): string
{
    $ident = mb_strtolower(mb_substr($email, 0, 120));
    if (login_bloqueado($ident)) {
        return 'bloqueado';
    }
    foreach (TABELAS_USUARIO as $tipo => $tabela) {
        $st = db()->prepare("SELECT id, nome, senha_hash, trocar_senha, ativo FROM $tabela WHERE email = ?");
        $st->execute([$email]);
        $linha = $st->fetch();
        if (!$linha) {
            // gasta o mesmo tempo para não revelar se o email existe
            $GLOBALS['hash_falso'] = $GLOBALS['hash_falso'] ?? password_hash('falso', PASSWORD_DEFAULT);
            password_verify($senha, $GLOBALS['hash_falso']);
            continue;
        }
        if ((int) $linha['ativo'] === 1 && password_verify($senha, $linha['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = ['tipo' => $tipo, 'id' => (string) $linha['id'], 'nome' => $linha['nome']];
            $_SESSION['ultima_atividade'] = time();
            $_SESSION['trocar_senha'] = (int) $linha['trocar_senha'] === 1;
            db()->prepare('DELETE FROM tentativas_login WHERE identificador = ? AND ip = ?')->execute([$ident, ip_cliente()]);
            auditar('entrar', $tabela);
            return 'ok';
        }
    }
    registar_falha_login($ident);
    return 'invalido';
}
