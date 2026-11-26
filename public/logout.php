<?php
require __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    if (usuario() !== null) {
        auditar('sair');
    }
    encerrar_sessao();
}
redirect('index.php');
