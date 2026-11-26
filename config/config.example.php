<?php
// Copie este ficheiro para config/config.php e ajuste os dados.
return [
    'db' => [
        'host'    => 'localhost',
        'port'    => 3306,
        'nome'    => 'registro_sumario',
        'usuario' => 'root',
        'senha'   => '',
    ],
    'timezone'                 => 'Africa/Maputo',
    'sessao_minutos'           => 30,
    'limite_faltas_percentual' => 25,   // a partir daqui o estudante entra em risco
    'minimo_aulas_para_risco'  => 4,    // só avalia o risco depois deste número de aulas
    'max_tentativas_login'     => 5,
    'bloqueio_minutos'         => 15,
];
