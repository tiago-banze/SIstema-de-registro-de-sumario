<?php
// Definição dos cadastros. Cada entrada descreve a tabela, os campos do formulário e a listagem.
$genero = ['M' => 'Masculino', 'F' => 'Feminino'];
$ativo = ['1' => 'Ativo', '0' => 'Inativo'];

return [
    'faculdades' => [
        'titulo' => 'Faculdades', 'singular' => 'faculdade', 'tabela' => 'faculdades', 'pk' => 'id',
        'consulta' => 'SELECT id, nome FROM faculdades',
        'colunas' => ['id' => 'Código', 'nome' => 'Nome'],
        'busca' => ['id', 'nome'], 'ordem' => 'nome',
        'campos' => [
            'id'   => ['label' => 'Código', 'tipo' => 'text', 'max' => 10, 'obrigatorio' => true, 'so_criacao' => true],
            'nome' => ['label' => 'Nome', 'tipo' => 'text', 'max' => 120, 'obrigatorio' => true],
        ],
    ],
    'cursos' => [
        'titulo' => 'Cursos', 'singular' => 'curso', 'tabela' => 'cursos', 'pk' => 'id',
        'consulta' => 'SELECT c.id, c.nome, c.diretor, f.nome AS faculdade FROM cursos c JOIN faculdades f ON f.id = c.faculdade_id',
        'colunas' => ['id' => 'Código', 'nome' => 'Nome', 'diretor' => 'Diretor', 'faculdade' => 'Faculdade'],
        'busca' => ['c.id', 'c.nome', 'c.diretor'], 'ordem' => 'c.nome',
        'campos' => [
            'id'           => ['label' => 'Código', 'tipo' => 'text', 'max' => 10, 'obrigatorio' => true, 'so_criacao' => true],
            'nome'         => ['label' => 'Nome do curso', 'tipo' => 'text', 'max' => 120, 'obrigatorio' => true],
            'diretor'      => ['label' => 'Diretor', 'tipo' => 'text', 'max' => 80, 'obrigatorio' => true],
            'faculdade_id' => ['label' => 'Faculdade', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, nome FROM faculdades ORDER BY nome'],
        ],
    ],
    'turmas' => [
        'titulo' => 'Turmas', 'singular' => 'turma', 'tabela' => 'turmas', 'pk' => 'id',
        'consulta' => 'SELECT t.id, c.nome AS curso FROM turmas t JOIN cursos c ON c.id = t.curso_id',
        'colunas' => ['id' => 'Turma', 'curso' => 'Curso'],
        'busca' => ['t.id', 'c.nome'], 'ordem' => 't.id',
        'campos' => [
            'id'       => ['label' => 'Código da turma', 'tipo' => 'text', 'max' => 10, 'obrigatorio' => true, 'so_criacao' => true],
            'curso_id' => ['label' => 'Curso', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, nome FROM cursos ORDER BY nome'],
        ],
    ],
    'ucs' => [
        'titulo' => 'Unidades curriculares', 'singular' => 'unidade curricular', 'tabela' => 'ucs', 'pk' => 'id',
        'consulta' => 'SELECT id, nome, creditos, horas_semestrais FROM ucs',
        'colunas' => ['id' => 'Código', 'nome' => 'Nome', 'creditos' => 'Créditos', 'horas_semestrais' => 'Horas semestrais'],
        'busca' => ['id', 'nome'], 'ordem' => 'nome',
        'campos' => [
            'id'               => ['label' => 'Código', 'tipo' => 'text', 'max' => 10, 'obrigatorio' => true, 'so_criacao' => true],
            'nome'             => ['label' => 'Nome', 'tipo' => 'text', 'max' => 120, 'obrigatorio' => true],
            'creditos'         => ['label' => 'Créditos', 'tipo' => 'number', 'obrigatorio' => true],
            'horas_semestrais' => ['label' => 'Horas semestrais', 'tipo' => 'number', 'obrigatorio' => true, 'ajuda' => 'Usadas para calcular quanto da matéria já foi lecionada.'],
        ],
    ],
    'estudantes' => [
        'titulo' => 'Estudantes', 'singular' => 'estudante', 'tabela' => 'estudantes', 'pk' => 'id',
        'consulta' => "SELECT e.id, e.nome, e.contacto, e.email, e.curso_id AS curso, e.turma_id AS turma, IF(e.ativo = 1, 'Ativo', 'Inativo') AS estado FROM estudantes e",
        'colunas' => ['id' => 'Código', 'nome' => 'Nome', 'contacto' => 'Contacto', 'email' => 'Email', 'curso' => 'Curso', 'turma' => 'Turma', 'estado' => 'Estado'],
        'busca' => ['e.id', 'e.nome', 'e.email'], 'ordem' => 'e.nome',
        'campos' => [
            'id'            => ['label' => 'Código do estudante', 'tipo' => 'number', 'obrigatorio' => true, 'so_criacao' => true],
            'nome'          => ['label' => 'Nome completo', 'tipo' => 'text', 'max' => 80, 'obrigatorio' => true],
            'contacto'      => ['label' => 'Contacto', 'tipo' => 'tel', 'max' => 20, 'obrigatorio' => true],
            'email'         => ['label' => 'Email (usado no login)', 'tipo' => 'email', 'max' => 120, 'obrigatorio' => true],
            'genero'        => ['label' => 'Género', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $genero],
            'nacionalidade' => ['label' => 'Nacionalidade', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true, 'padrao' => 'Moçambique'],
            'provincia'     => ['label' => 'Província', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true],
            'distrito'      => ['label' => 'Distrito', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true],
            'curso_id'      => ['label' => 'Curso', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, nome FROM cursos ORDER BY nome'],
            'turma_id'      => ['label' => 'Turma', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, id FROM turmas ORDER BY id'],
            'ativo'         => ['label' => 'Estado', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $ativo, 'padrao' => '1'],
            'senha'         => ['label' => 'Senha (mínimo 8 caracteres)', 'tipo' => 'password', 'coluna' => 'senha_hash', 'marca_troca' => 'trocar_senha'],
        ],
    ],
    'docentes' => [
        'titulo' => 'Docentes', 'singular' => 'docente', 'tabela' => 'docentes', 'pk' => 'id',
        'consulta' => "SELECT d.id, d.nome, d.contacto, d.email, d.grau_academico AS grau, IF(d.ativo = 1, 'Ativo', 'Inativo') AS estado FROM docentes d",
        'colunas' => ['id' => 'Código', 'nome' => 'Nome', 'contacto' => 'Contacto', 'email' => 'Email', 'grau' => 'Grau', 'estado' => 'Estado'],
        'busca' => ['d.id', 'd.nome', 'd.email'], 'ordem' => 'd.nome',
        'campos' => [
            'id'             => ['label' => 'Código do docente', 'tipo' => 'text', 'max' => 10, 'obrigatorio' => true, 'so_criacao' => true],
            'nome'           => ['label' => 'Nome completo', 'tipo' => 'text', 'max' => 80, 'obrigatorio' => true],
            'contacto'       => ['label' => 'Contacto', 'tipo' => 'tel', 'max' => 20, 'obrigatorio' => true],
            'email'          => ['label' => 'Email (usado no login)', 'tipo' => 'email', 'max' => 120, 'obrigatorio' => true],
            'genero'         => ['label' => 'Género', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $genero],
            'grau_academico' => ['label' => 'Grau académico', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => ['Licenciado' => 'Licenciado', 'Mestre' => 'Mestre', 'Doutor' => 'Doutor']],
            'formacao'       => ['label' => 'Formação', 'tipo' => 'text', 'max' => 120, 'obrigatorio' => true],
            'nacionalidade'  => ['label' => 'Nacionalidade', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true, 'padrao' => 'Moçambique'],
            'faculdade_id'   => ['label' => 'Faculdade', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, nome FROM faculdades ORDER BY nome'],
            'ativo'          => ['label' => 'Estado', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $ativo, 'padrao' => '1'],
            'senha'          => ['label' => 'Senha (mínimo 8 caracteres)', 'tipo' => 'password', 'coluna' => 'senha_hash', 'marca_troca' => 'trocar_senha'],
        ],
    ],
    'administradores' => [
        'titulo' => 'Administradores', 'singular' => 'administrador', 'tabela' => 'administradores', 'pk' => 'id', 'proteger_proprio' => true,
        'consulta' => "SELECT id, nome, email, IF(ativo = 1, 'Ativo', 'Inativo') AS estado FROM administradores",
        'colunas' => ['nome' => 'Nome', 'email' => 'Email', 'estado' => 'Estado'],
        'busca' => ['nome', 'email'], 'ordem' => 'nome',
        'campos' => [
            'nome'  => ['label' => 'Nome', 'tipo' => 'text', 'max' => 80, 'obrigatorio' => true],
            'email' => ['label' => 'Email (usado no login)', 'tipo' => 'email', 'max' => 120, 'obrigatorio' => true],
            'ativo' => ['label' => 'Estado', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $ativo, 'padrao' => '1'],
            'senha' => ['label' => 'Senha (mínimo 8 caracteres)', 'tipo' => 'password', 'coluna' => 'senha_hash', 'marca_troca' => 'trocar_senha'],
        ],
    ],
    'periodos' => [
        'titulo' => 'Períodos letivos', 'singular' => 'período', 'tabela' => 'periodos', 'pk' => 'id',
        'consulta' => "SELECT id, nome, inicio, fim, IF(ativo = 1, 'Sim', 'Não') AS ativo FROM periodos",
        'colunas' => ['nome' => 'Nome', 'inicio' => 'Início', 'fim' => 'Fim', 'ativo' => 'Ativo'],
        'busca' => ['nome'], 'ordem' => 'inicio DESC',
        'campos' => [
            'nome'   => ['label' => 'Nome (por exemplo 2026 semestre 1)', 'tipo' => 'text', 'max' => 40, 'obrigatorio' => true],
            'inicio' => ['label' => 'Início', 'tipo' => 'date', 'obrigatorio' => true],
            'fim'    => ['label' => 'Fim', 'tipo' => 'date', 'obrigatorio' => true],
            'ativo'  => ['label' => 'Período em curso', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => ['1' => 'Sim', '0' => 'Não'], 'padrao' => '1', 'ajuda' => 'O sistema trabalha com o período ativo mais recente.'],
        ],
        'validar' => function (array $d, ?string $pk): array {
            return ($d['fim'] ?? '') <= ($d['inicio'] ?? '') ? ['fim' => 'O fim deve ser depois do início.'] : [];
        },
    ],
    'feriados' => [
        'titulo' => 'Feriados e dias sem aulas', 'singular' => 'feriado', 'tabela' => 'feriados', 'pk' => 'data',
        'consulta' => 'SELECT data, descricao FROM feriados',
        'colunas' => ['data' => 'Data', 'descricao' => 'Descrição'],
        'busca' => ['descricao'], 'ordem' => 'data DESC',
        'campos' => [
            'data'      => ['label' => 'Data', 'tipo' => 'date', 'obrigatorio' => true, 'so_criacao' => true],
            'descricao' => ['label' => 'Descrição', 'tipo' => 'text', 'max' => 80, 'obrigatorio' => true],
        ],
    ],
    'atribuicoes' => [
        'titulo' => 'Atribuições', 'singular' => 'atribuição', 'tabela' => 'atribuicoes', 'pk' => 'id',
        'consulta' => 'SELECT a.id, p.nome AS periodo, a.turma_id AS turma, u.nome AS uc, d.nome AS docente FROM atribuicoes a JOIN periodos p ON p.id = a.periodo_id JOIN ucs u ON u.id = a.uc_id JOIN docentes d ON d.id = a.docente_id',
        'colunas' => ['periodo' => 'Período', 'turma' => 'Turma', 'uc' => 'Unidade curricular', 'docente' => 'Docente'],
        'busca' => ['a.turma_id', 'u.nome', 'd.nome', 'p.nome'], 'ordem' => 'p.inicio DESC, a.turma_id, u.nome',
        'campos' => [
            'periodo_id' => ['label' => 'Período', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, nome FROM periodos ORDER BY inicio DESC'],
            'turma_id'   => ['label' => 'Turma', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, id FROM turmas ORDER BY id'],
            'uc_id'      => ['label' => 'Unidade curricular', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => "SELECT id, CONCAT(id, ' - ', nome) FROM ucs ORDER BY nome"],
            'docente_id' => ['label' => 'Docente', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes_sql' => 'SELECT id, nome FROM docentes WHERE ativo = 1 ORDER BY nome'],
        ],
    ],
    'horarios' => [
        'titulo' => 'Horários', 'singular' => 'horário', 'tabela' => 'horarios', 'pk' => 'id',
        'consulta' => "SELECT h.id, ELT(h.dia_semana, 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo') AS dia, TIME_FORMAT(h.hora_inicio, '%H:%i') AS inicio, TIME_FORMAT(h.hora_fim, '%H:%i') AS fim, h.sala, a.turma_id AS turma, u.nome AS uc, d.nome AS docente FROM horarios h JOIN atribuicoes a ON a.id = h.atribuicao_id JOIN ucs u ON u.id = a.uc_id JOIN docentes d ON d.id = a.docente_id",
        'colunas' => ['dia' => 'Dia', 'inicio' => 'Início', 'fim' => 'Fim', 'sala' => 'Sala', 'turma' => 'Turma', 'uc' => 'Unidade curricular', 'docente' => 'Docente'],
        'busca' => ['u.nome', 'd.nome', 'a.turma_id', 'h.sala'], 'ordem' => 'h.dia_semana, h.hora_inicio',
        'campos' => [
            'atribuicao_id' => ['label' => 'Atribuição', 'tipo' => 'select', 'obrigatorio' => true,
                'opcoes_sql' => "SELECT a.id, CONCAT(p.nome, ' | ', a.turma_id, ' | ', u.nome, ' | ', d.nome) FROM atribuicoes a JOIN periodos p ON p.id = a.periodo_id JOIN ucs u ON u.id = a.uc_id JOIN docentes d ON d.id = a.docente_id ORDER BY p.inicio DESC, a.turma_id, u.nome"],
            'dia_semana'    => ['label' => 'Dia da semana', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => ['1' => 'Segunda-feira', '2' => 'Terça-feira', '3' => 'Quarta-feira', '4' => 'Quinta-feira', '5' => 'Sexta-feira', '6' => 'Sábado', '7' => 'Domingo']],
            'hora_inicio'   => ['label' => 'Hora de início', 'tipo' => 'time', 'obrigatorio' => true],
            'hora_fim'      => ['label' => 'Hora de fim', 'tipo' => 'time', 'obrigatorio' => true],
            'sala'          => ['label' => 'Sala', 'tipo' => 'text', 'max' => 30],
        ],
        'validar' => function (array $d, ?string $pk): array {
            $ini = substr((string) $d['hora_inicio'], 0, 5);
            $fim = substr((string) $d['hora_fim'], 0, 5);
            if ($ini >= $fim) {
                return ['hora_fim' => 'A hora de fim deve ser depois da hora de início.'];
            }
            $st = db()->prepare(
                'SELECT COUNT(*) FROM horarios h JOIN atribuicoes a ON a.id = h.atribuicao_id JOIN atribuicoes n ON n.id = ?
                  WHERE h.dia_semana = ? AND h.hora_inicio < ? AND h.hora_fim > ? AND h.id <> ?
                    AND a.periodo_id = n.periodo_id AND (a.turma_id = n.turma_id OR a.docente_id = n.docente_id)'
            );
            $st->execute([$d['atribuicao_id'], $d['dia_semana'], $fim, $ini, $pk ?? 0]);
            return (int) $st->fetchColumn() > 0 ? ['_geral' => 'Conflito de horário: a turma ou o docente já tem aula nesse intervalo.'] : [];
        },
    ],
];
