<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$arquivoConfig = BASE_PATH . '/config/config.php';
if (!is_file($arquivoConfig)) {
    http_response_code(500);
    exit('Falta o ficheiro config/config.php. Copie config/config.example.php e ajuste os dados.');
}
$GLOBALS['config'] = require $arquivoConfig;
date_default_timezone_set($GLOBALS['config']['timezone'] ?? 'Africa/Maputo');

session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();

/* ---------- Base de dados ---------- */
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
            exit('Não foi possível ligar à base de dados. Verifique config/config.php.');
        }
    }
    return $pdo;
}

/* ---------- Utilitários ---------- */
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

function data_pt(string $data): string
{
    $t = strtotime($data);
    return $t ? date('d/m/Y', $t) : $data;
}

/* ---------- Autenticação ---------- */
function usuario(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function encerrar_sessao(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

function exigir_login(?string $tipo = null): array
{
    $u = usuario();
    $limite = ((int) ($GLOBALS['config']['sessao_minutos'] ?? 30)) * 60;
    if ($u !== null && time() - ($_SESSION['ultima_atividade'] ?? time()) > $limite) {
        encerrar_sessao();
        flash('aviso', 'A sessão expirou por inatividade. Entre novamente.');
        $u = null;
    }
    if ($u === null) {
        redirect('index.php');
    }
    if ($tipo !== null && $u['tipo'] !== $tipo) {
        http_response_code(403);
        exit('Acesso negado.');
    }
    $_SESSION['ultima_atividade'] = time();
    return $u;
}

function autenticar(string $email, string $senha): bool
{
    $consultas = [
        'admin'   => 'SELECT id, nome, senha_hash FROM administradores WHERE email = ?',
        'docente' => 'SELECT id, nome, senha_hash FROM docentes WHERE email = ?',
    ];
    foreach ($consultas as $tipo => $sql) {
        $st = db()->prepare($sql);
        $st->execute([$email]);
        $linha = $st->fetch();
        if ($linha && password_verify($senha, $linha['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = ['tipo' => $tipo, 'id' => $linha['id'], 'nome' => $linha['nome']];
            $_SESSION['ultima_atividade'] = time();
            return true;
        }
    }
    return false;
}
