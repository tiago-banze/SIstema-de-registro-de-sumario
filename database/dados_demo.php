<?php
/*
 * Dados de demonstração para o Sistema de Registo de Sumário.
 * Uso, na pasta do projeto:   php database/dados_demo.php
 * Para apagar tudo e recriar: php database/dados_demo.php --reset
 * Cria um período letivo à volta da data de hoje, docentes, estudantes, horários e
 * cerca de doze semanas de sumários e presenças, para ver o sistema a funcionar.
 */
if (PHP_SAPI !== 'cli') {
    exit("Execute este ficheiro no terminal.\n");
}
mt_srand(2026);
$cfg = require __DIR__ . '/../config/config.php';
date_default_timezone_set($cfg['timezone'] ?? 'Africa/Maputo');
$c = $cfg['db'];
$pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['nome']), $c['usuario'], $c['senha'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$reset = in_array('--reset', $argv, true);
$existentes = (int) $pdo->query('SELECT COUNT(*) FROM faculdades')->fetchColumn() + (int) $pdo->query('SELECT COUNT(*) FROM administradores')->fetchColumn();
if ($existentes > 0) {
    if (!$reset) {
        exit("A base de dados já tem dados. Use --reset para apagar tudo e recriar os dados de demonstração.\n");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (['presencas', 'sumarios', 'horarios', 'atribuicoes', 'feriados', 'estudantes', 'docentes', 'curso_uc', 'periodos', 'ucs', 'turmas', 'cursos', 'faculdades', 'administradores', 'tentativas_login', 'auditoria'] as $t) {
        $pdo->exec("TRUNCATE TABLE $t");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

function ins(PDO $pdo, string $tabela, array $linha): string
{
    $cols = implode(',', array_map(fn($c) => "`$c`", array_keys($linha)));
    $marcas = implode(',', array_fill(0, count($linha), '?'));
    $pdo->prepare("INSERT INTO `$tabela` ($cols) VALUES ($marcas)")->execute(array_values($linha));
    return $pdo->lastInsertId();
}

function sem_acentos(string $s): string
{
    return strtolower(strtr($s, ['á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'õ' => 'o', 'ô' => 'o', 'ú' => 'u', 'ç' => 'c',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ç' => 'C']));
}

$SENHA_ADMIN = 'Admin@1234';
$SENHA_DEMO = 'Demo@1234';
$hashAdmin = password_hash($SENHA_ADMIN, PASSWORD_DEFAULT);
$hashDemo = password_hash($SENHA_DEMO, PASSWORD_DEFAULT);

$pdo->beginTransaction();

$adminId = ins($pdo, 'administradores', ['nome' => 'Administração Demo', 'email' => 'admin@demo.test', 'senha_hash' => $hashAdmin]);

foreach ([['FAGEC', 'Faculdade de Administração, Gestão e Contabilidade'], ['FET', 'Faculdade de Engenharia e Tecnologia'],
          ['FACED', 'Faculdade de Ciências de Educação'], ['FAHCS', 'Faculdade de Humanidades e Ciências Sociais']] as [$id, $nome]) {
    ins($pdo, 'faculdades', ['id' => $id, 'nome' => $nome]);
}
foreach ([['CAG', 'Ciências de Administração e Gestão', 'Fernando Muzime', 'FAGEC'], ['EIT', 'Engenharia Informática e Tecnologia', 'Jean Marie', 'FET'],
          ['TEO', 'Teologia', 'Filipe Huguane', 'FAHCS']] as [$id, $nome, $diretor, $fac]) {
    ins($pdo, 'cursos', ['id' => $id, 'nome' => $nome, 'diretor' => $diretor, 'faculdade_id' => $fac]);
}
$turmas = [['EIT22', 'EIT'], ['EIT23', 'EIT'], ['CAG22', 'CAG'], ['CAG23', 'CAG']];
foreach ($turmas as [$id, $curso]) {
    ins($pdo, 'turmas', ['id' => $id, 'curso_id' => $curso]);
}

$ucs = [
    ['EEI-05', 'Algoritmos e Estruturas de Dados 1', 6, 60], ['EEI-09', 'Base de Dados', 6, 60], ['EEI-11', 'Algoritmos e Estruturas de Dados 2', 6, 60],
    ['EEI-12', 'Laboratório 1', 7, 60], ['EEI-15', 'Laboratório 2', 7, 60], ['EEI-20', 'Redes de Computadores', 4, 30],
    ['EEI-21', 'Engenharia de Software', 6, 60], ['MAT-02', 'Matemática Discreta', 4, 30], ['TIA', 'Estatística', 4, 30],
    ['GES-10', 'Contabilidade Geral', 6, 60], ['GES-11', 'Gestão de Recursos Humanos', 4, 30], ['GES-12', 'Marketing', 6, 60],
];
foreach ($ucs as [$id, $nome, $cred, $horas]) {
    ins($pdo, 'ucs', ['id' => $id, 'nome' => $nome, 'creditos' => $cred, 'horas_semestrais' => $horas]);
}
foreach ([['EIT', 'EEI-05', 1, 1], ['EIT', 'EEI-09', 2, 1], ['EIT', 'EEI-11', 2, 2], ['EIT', 'EEI-12', 2, 1], ['EIT', 'EEI-15', 2, 2], ['EIT', 'EEI-20', 3, 1],
          ['EIT', 'EEI-21', 3, 1], ['EIT', 'MAT-02', 1, 1], ['CAG', 'TIA', 2, 1], ['CAG', 'GES-10', 1, 1], ['CAG', 'GES-11', 2, 1], ['CAG', 'GES-12', 2, 2]] as [$cu, $uc, $ano, $sem]) {
    ins($pdo, 'curso_uc', ['curso_id' => $cu, 'uc_id' => $uc, 'ano' => $ano, 'semestre' => $sem]);
}

$docentes = [
    ['D001', 'Carlos Macamo', '+258 84 310 1122', 'carlos.macamo@demo.test', 'M', 'Mestre', 'Engenharia Informática', 'FET'],
    ['D002', 'Ana Paula Nhantumbo', '+258 82 455 3321', 'ana.nhantumbo@demo.test', 'F', 'Doutor', 'Ciências da Computação', 'FET'],
    ['D003', 'Fernando Chissano', '+258 86 220 9087', 'fernando.chissano@demo.test', 'M', 'Mestre', 'Gestão de Empresas', 'FAGEC'],
    ['D004', 'Lúcia Mondlane', '+258 87 612 4410', 'lucia.mondlane@demo.test', 'F', 'Licenciado', 'Contabilidade e Auditoria', 'FAGEC'],
    ['D005', 'Eduardo Sitoe', '+258 84 903 7755', 'eduardo.sitoe@demo.test', 'M', 'Mestre', 'Matemática Aplicada', 'FET'],
];
foreach ($docentes as [$id, $nome, $tel, $email, $gen, $grau, $form, $fac]) {
    ins($pdo, 'docentes', ['id' => $id, 'nome' => $nome, 'contacto' => $tel, 'email' => $email, 'genero' => $gen, 'grau_academico' => $grau,
        'formacao' => $form, 'nacionalidade' => 'Moçambique', 'faculdade_id' => $fac, 'senha_hash' => $hashDemo]);
}

// Estudantes
$nomesF = ['Ana', 'Beatriz', 'Celeste', 'Dulce', 'Elsa', 'Fátima', 'Graça', 'Helena', 'Isabel', 'Joana', 'Lurdes', 'Marta', 'Nádia', 'Olívia', 'Paula', 'Rosa'];
$nomesM = ['Alberto', 'Bernardo', 'Carlos', 'Délio', 'Edson', 'Filipe', 'Gilberto', 'Hélder', 'Ivan', 'Jorge', 'Lino', 'Mário', 'Nelson', 'Osvaldo', 'Pedro', 'Rui'];
$apelidos = ['Macamo', 'Chissano', 'Mondlane', 'Sitoe', 'Nhantumbo', 'Cossa', 'Mabunda', 'Tembe', 'Muianga', 'Bila', 'Langa', 'Chongo', 'Matsinhe', 'Mucavele', 'Zandamela', 'Guambe', 'Machava', 'Nhaca', 'Cumbe', 'Massingue'];
$locais = [['Maputo', 'Matola'], ['Maputo', 'Boane'], ['Gaza', 'Xai-Xai'], ['Inhambane', 'Maxixe'], ['Sofala', 'Beira'], ['Zambézia', 'Quelimane'], ['Nampula', 'Nampula'], ['Cabo Delgado', 'Pemba']];
$estudantes = [];
$usados = [];
foreach ($turmas as $i => [$turma, $curso]) {
    for ($n = 1; $n <= 8; $n++) {
        do {
            $fem = mt_rand(0, 1) === 1;
            $primeiro = $fem ? $nomesF[mt_rand(0, count($nomesF) - 1)] : $nomesM[mt_rand(0, count($nomesM) - 1)];
            $apelido = $apelidos[mt_rand(0, count($apelidos) - 1)];
            $nome = "$primeiro $apelido";
        } while (isset($usados[$nome]));
        $usados[$nome] = true;
        $id = 20260000 + ($i + 1) * 100 + $n;
        [$prov, $dist] = $locais[mt_rand(0, count($locais) - 1)];
        $email = sem_acentos($primeiro . '.' . $apelido) . $n . $i . '@estudante.demo.test';
        ins($pdo, 'estudantes', ['id' => $id, 'nome' => $nome, 'contacto' => '+258 8' . mt_rand(2, 7) . ' ' . mt_rand(100, 999) . ' ' . mt_rand(1000, 9999),
            'email' => $email, 'genero' => $fem ? 'F' : 'M', 'nacionalidade' => 'Moçambique', 'provincia' => $prov, 'distrito' => $dist,
            'curso_id' => $curso, 'turma_id' => $turma, 'senha_hash' => $hashDemo]);
        $estudantes[$turma][] = ['id' => $id, 'nome' => $nome, 'email' => $email, 'prob' => 0.06];
    }
}
// alguns estudantes faltam mais, para o relatório de risco ter o que mostrar
$todos = [];
foreach ($estudantes as $t => $lista) {
    foreach (array_keys($lista) as $k) {
        $todos[] = [$t, $k];
    }
}
shuffle($todos);
foreach (array_slice($todos, 0, 3) as [$t, $k]) {
    $estudantes[$t][$k]['prob'] = 0.45;
}
foreach (array_slice($todos, 3, 3) as [$t, $k]) {
    $estudantes[$t][$k]['prob'] = 0.2;
}

// Período à volta de hoje
$periodoInicio = date('Y-m-d', strtotime('monday this week -11 weeks'));
$periodoFim = date('Y-m-d', strtotime('friday this week +5 weeks'));
$periodoId = ins($pdo, 'periodos', ['nome' => 'Semestre ' . date('Y'), 'inicio' => $periodoInicio, 'fim' => $periodoFim, 'ativo' => 1]);

$feriados = [['2026-09-07', 'Dia da Vitória'], ['2026-09-25', 'Dia das Forças Armadas de Libertação Nacional'], ['2026-10-04', 'Dia da Paz e Reconciliação'], ['2026-12-25', 'Dia da Família']];
foreach ($feriados as [$d, $desc]) {
    ins($pdo, 'feriados', ['data' => $d, 'descricao' => $desc]);
}
$setFeriados = array_column($feriados, 1, 0);

// Atribuições e horários
$blocos = ['A' => ['07:30', '09:30'], 'B' => ['09:45', '11:45'], 'C' => ['13:00', '15:00'], 'D' => ['15:15', '17:15']];
$planeamento = [
    ['EIT22', 'EEI-09', 'D001', [[1, 'A'], [3, 'A']], 'Sala 101'], ['EIT22', 'EEI-21', 'D002', [[2, 'B'], [4, 'B']], 'Sala 101'], ['EIT22', 'EEI-20', 'D005', [[5, 'A']], 'Lab 2'],
    ['EIT23', 'EEI-05', 'D002', [[1, 'B'], [4, 'A']], 'Sala 102'], ['EIT23', 'EEI-12', 'D001', [[2, 'C'], [5, 'C']], 'Lab 1'], ['EIT23', 'MAT-02', 'D005', [[3, 'B']], 'Sala 102'],
    ['CAG22', 'GES-10', 'D004', [[1, 'C'], [3, 'C']], 'Sala 201'], ['CAG22', 'GES-11', 'D003', [[2, 'A']], 'Sala 201'], ['CAG22', 'TIA', 'D005', [[4, 'C']], 'Sala 201'],
    ['CAG23', 'GES-12', 'D003', [[2, 'D'], [5, 'D']], 'Sala 202'], ['CAG23', 'GES-10', 'D004', [[1, 'D'], [3, 'D']], 'Sala 202'], ['CAG23', 'TIA', 'D005', [[4, 'D']], 'Sala 202'],
];
$horariosPorDia = [];
foreach ($planeamento as [$turma, $uc, $doc, $slots, $sala]) {
    $atribId = ins($pdo, 'atribuicoes', ['periodo_id' => $periodoId, 'turma_id' => $turma, 'uc_id' => $uc, 'docente_id' => $doc]);
    foreach ($slots as [$dow, $bloco]) {
        $hid = ins($pdo, 'horarios', ['atribuicao_id' => $atribId, 'dia_semana' => $dow, 'hora_inicio' => $blocos[$bloco][0], 'hora_fim' => $blocos[$bloco][1], 'sala' => $sala]);
        $horariosPorDia[$dow][] = ['horario' => $hid, 'atrib' => $atribId, 'turma' => $turma, 'uc' => $uc, 'fim' => $blocos[$bloco][1]];
    }
}

$temas = [
    'EEI-09' => ['Modelo entidade-relação e cardinalidades', 'Normalização até à terceira forma normal', 'Criação de tabelas e restrições em SQL', 'Consultas com JOIN e subconsultas', 'Funções de agregação e GROUP BY', 'Índices e desempenho de consultas', 'Transações e propriedades ACID', 'Vistas e procedimentos armazenados'],
    'EEI-21' => ['Ciclo de vida do software', 'Levantamento e análise de requisitos', 'Diagramas UML de casos de uso', 'Diagramas de classes e de sequência', 'Padrões de projeto básicos', 'Testes unitários e de integração', 'Controlo de versões com Git', 'Gestão de projetos ágeis'],
    'EEI-20' => ['Modelo OSI e TCP/IP', 'Endereçamento IPv4 e sub-redes', 'Protocolos da camada de transporte', 'Encaminhamento e routers', 'DNS e DHCP', 'Segurança em redes', 'Redes sem fios', 'Configuração de uma rede local'],
    'EEI-05' => ['Noção de algoritmo e pseudocódigo', 'Variáveis, tipos e operadores', 'Estruturas condicionais', 'Ciclos e repetição', 'Vetores e matrizes', 'Funções e procedimentos', 'Pesquisa sequencial e binária', 'Ordenação por seleção e inserção'],
    'EEI-12' => ['Instalação do ambiente de desenvolvimento', 'Primeiros programas em linguagem C', 'Depuração de erros', 'Manipulação de ficheiros', 'Estruturas e apontadores', 'Projeto em grupo: desenho da solução', 'Projeto em grupo: implementação', 'Apresentação e revisão do projeto'],
    'MAT-02' => ['Lógica proposicional', 'Conjuntos e relações', 'Funções e cardinalidade', 'Indução matemática', 'Combinatória básica', 'Grafos e árvores', 'Relações de recorrência', 'Álgebra de Boole'],
    'GES-10' => ['Princípios contabilísticos', 'Balanço e demonstração de resultados', 'Método das partidas dobradas', 'Registo de operações correntes', 'Amortizações e reintegrações', 'Inventário e existências', 'Balancete e apuramento de resultados', 'Análise de rácios financeiros'],
    'GES-11' => ['Evolução da gestão de pessoas', 'Recrutamento e seleção', 'Descrição de funções', 'Avaliação de desempenho', 'Formação e desenvolvimento', 'Remuneração e benefícios', 'Legislação laboral moçambicana', 'Clima organizacional'],
    'GES-12' => ['Conceitos de marketing', 'Análise do mercado', 'Segmentação e posicionamento', 'Mistura de marketing: produto e preço', 'Mistura de marketing: distribuição e promoção', 'Comportamento do consumidor', 'Plano de marketing', 'Marketing digital'],
    'TIA'    => ['Estatística descritiva', 'Medidas de tendência central', 'Medidas de dispersão', 'Probabilidades', 'Distribuição normal', 'Amostragem', 'Intervalos de confiança', 'Testes de hipóteses'],
];
$contador = [];
$nSumarios = 0;
$nPresencas = 0;
$insSum = $pdo->prepare('INSERT INTO sumarios (atribuicao_id, horario_id, data_aula, hora_registo, sumario, duracao_horas, estado, nota, validado_por, validado_em) VALUES (?, ?, ?, ?, ?, 2, ?, ?, ?, ?)');
$insPre = $pdo->prepare('INSERT INTO presencas (sumario_id, estudante_id, estado) VALUES (?, ?, ?)');

$ontem = date('Y-m-d', strtotime('-1 day'));
$limiteValidacao = date('Y-m-d', strtotime('-10 days'));
for ($d = new DateTimeImmutable($periodoInicio); $d->format('Y-m-d') <= $ontem; $d = $d->modify('+1 day')) {
    $data = $d->format('Y-m-d');
    $dow = (int) $d->format('N');
    if (isset($setFeriados[$data]) || empty($horariosPorDia[$dow])) {
        continue;
    }
    foreach ($horariosPorDia[$dow] as $h) {
        $chanceFalha = $data === $ontem ? 0.35 : 0.07;
        if (mt_rand(0, 999) / 1000 < $chanceFalha) {
            continue; // aula sem sumário registado
        }
        $k = $h['atrib'];
        $contador[$k] = ($contador[$k] ?? -1) + 1;
        $lista = $temas[$h['uc']];
        $texto = $lista[$contador[$k] % count($lista)] . ($contador[$k] >= count($lista) ? ' (revisão)' : '');
        $estado = 'registado';
        $nota = null;
        $validador = null;
        $validadoEm = null;
        if ($data <= $limiteValidacao) {
            $validador = $adminId;
            $validadoEm = date('Y-m-d H:i:s', strtotime($data . ' +1 day 10:00'));
            if (mt_rand(0, 99) < 3) {
                $estado = 'rejeitado';
                $nota = 'Sumário demasiado genérico. Detalhe os conteúdos lecionados.';
            } else {
                $estado = 'validado';
            }
        }
        $insSum->execute([$h['atrib'], $h['horario'], $data, $h['fim'] . ':00', $texto, $estado, $nota, $validador, $validadoEm]);
        $sid = (int) $pdo->lastInsertId();
        $nSumarios++;
        foreach ($estudantes[$h['turma']] as $e) {
            $r = mt_rand(0, 999) / 1000;
            $estadoP = 'presente';
            if ($r < $e['prob']) {
                $estadoP = mt_rand(0, 99) < 30 ? 'justificada' : 'falta';
            }
            $insPre->execute([$sid, $e['id'], $estadoP]);
            $nPresencas++;
        }
    }
}
$pdo->commit();

echo "Dados de demonstração criados.\n";
echo "Período: $periodoInicio a $periodoFim, $nSumarios sumários, $nPresencas registos de presença.\n\n";
echo "Contas (a senha é igual para todas as contas do mesmo tipo):\n";
echo "  Administrador: admin@demo.test / $SENHA_ADMIN\n";
foreach ($docentes as $d) {
    echo "  Docente:       {$d[3]} / $SENHA_DEMO\n";
}
foreach (['EIT22', 'EIT23', 'CAG22', 'CAG23'] as $t) {
    $e = $estudantes[$t][0];
    echo "  Estudante ($t): {$e['email']} / $SENHA_DEMO\n";
}
echo "\nUse estas contas só para testes. Em produção, crie contas reais e apague estes dados.\n";
