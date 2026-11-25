<?php
// Definição dos cadastros. Cada entrada descreve a tabela, os campos do formulário e a listagem.
$genero = ['M' => 'Masculino', 'F' => 'Feminino'];

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
            'faculdade_id' => ['label' => 'Faculdade', 'tipo' => 'select', 'obrigatorio' => true,
                               'opcoes_sql' => 'SELECT id, nome FROM faculdades ORDER BY nome'],
        ],
    ],
    'turmas' => [
        'titulo' => 'Turmas', 'singular' => 'turma', 'tabela' => 'turmas', 'pk' => 'id',
        'consulta' => 'SELECT t.id, c.nome AS curso FROM turmas t JOIN cursos c ON c.id = t.curso_id',
        'colunas' => ['id' => 'Turma', 'curso' => 'Curso'],
        'busca' => ['t.id', 'c.nome'], 'ordem' => 't.id',
        'campos' => [
            'id'       => ['label' => 'Código da turma', 'tipo' => 'text', 'max' => 10, 'obrigatorio' => true, 'so_criacao' => true],
            'curso_id' => ['label' => 'Curso', 'tipo' => 'select', 'obrigatorio' => true,
                           'opcoes_sql' => 'SELECT id, nome FROM cursos ORDER BY nome'],
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
            'horas_semestrais' => ['label' => 'Horas semestrais', 'tipo' => 'number', 'obrigatorio' => true],
        ],
    ],
    'estudantes' => [
        'titulo' => 'Estudantes', 'singular' => 'estudante', 'tabela' => 'estudantes', 'pk' => 'id',
        'consulta' => 'SELECT e.id, e.nome, e.contacto, e.email, e.curso_id AS curso, e.turma_id AS turma FROM estudantes e',
        'colunas' => ['id' => 'Código', 'nome' => 'Nome', 'contacto' => 'Contacto', 'email' => 'Email', 'curso' => 'Curso', 'turma' => 'Turma'],
        'busca' => ['e.id', 'e.nome', 'e.email'], 'ordem' => 'e.nome',
        'campos' => [
            'id'            => ['label' => 'Código do estudante', 'tipo' => 'number', 'obrigatorio' => true, 'so_criacao' => true],
            'nome'          => ['label' => 'Nome completo', 'tipo' => 'text', 'max' => 80, 'obrigatorio' => true],
            'contacto'      => ['label' => 'Contacto', 'tipo' => 'tel', 'max' => 20, 'obrigatorio' => true],
            'email'         => ['label' => 'Email', 'tipo' => 'email', 'max' => 120, 'obrigatorio' => true],
            'genero'        => ['label' => 'Género', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $genero],
            'nacionalidade' => ['label' => 'Nacionalidade', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true, 'padrao' => 'Moçambique'],
            'provincia'     => ['label' => 'Província', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true],
            'distrito'      => ['label' => 'Distrito', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true],
            'curso_id'      => ['label' => 'Curso', 'tipo' => 'select', 'obrigatorio' => true,
                                'opcoes_sql' => 'SELECT id, nome FROM cursos ORDER BY nome'],
            'turma_id'      => ['label' => 'Turma', 'tipo' => 'select', 'obrigatorio' => true,
                                'opcoes_sql' => 'SELECT id, id FROM turmas ORDER BY id'],
        ],
    ],
    'docentes' => [
        'titulo' => 'Docentes', 'singular' => 'docente', 'tabela' => 'docentes', 'pk' => 'id',
        'consulta' => 'SELECT d.id, d.nome, d.contacto, d.email, d.grau_academico AS grau FROM docentes d',
        'colunas' => ['id' => 'Código', 'nome' => 'Nome', 'contacto' => 'Contacto', 'email' => 'Email', 'grau' => 'Grau académico'],
        'busca' => ['d.id', 'd.nome', 'd.email'], 'ordem' => 'd.nome',
        'campos' => [
            'id'             => ['label' => 'Código do docente', 'tipo' => 'text', 'max' => 10, 'obrigatorio' => true, 'so_criacao' => true],
            'nome'           => ['label' => 'Nome completo', 'tipo' => 'text', 'max' => 80, 'obrigatorio' => true],
            'contacto'       => ['label' => 'Contacto', 'tipo' => 'tel', 'max' => 20, 'obrigatorio' => true],
            'email'          => ['label' => 'Email (usado no login)', 'tipo' => 'email', 'max' => 120, 'obrigatorio' => true],
            'genero'         => ['label' => 'Género', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $genero],
            'grau_academico' => ['label' => 'Grau académico', 'tipo' => 'select', 'obrigatorio' => true,
                                 'opcoes' => ['Licenciado' => 'Licenciado', 'Mestre' => 'Mestre', 'Doutor' => 'Doutor']],
            'formacao'       => ['label' => 'Formação', 'tipo' => 'text', 'max' => 120, 'obrigatorio' => true],
            'nacionalidade'  => ['label' => 'Nacionalidade', 'tipo' => 'text', 'max' => 50, 'obrigatorio' => true, 'padrao' => 'Moçambique'],
            'faculdade_id'   => ['label' => 'Faculdade', 'tipo' => 'select', 'obrigatorio' => true,
                                 'opcoes_sql' => 'SELECT id, nome FROM faculdades ORDER BY nome'],
            'senha'          => ['label' => 'Senha (mínimo 8 caracteres)', 'tipo' => 'password', 'coluna' => 'senha_hash'],
        ],
    ],
];
