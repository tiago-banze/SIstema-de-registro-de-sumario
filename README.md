# Sistema de Registo de Sumário

Aplicação web para registar as aulas de uma escola ou universidade: o docente escreve o sumário e marca as presenças, o estudante vê as suas faltas e a administração acompanha o que foi lecionado e o que ficou por registar.

Está escrita em PHP 8 e MySQL, sem frameworks. O desenho começa pelo telemóvel e passa a usar a barra lateral em ecrãs largos.

## O que faz cada perfil

Administração: gere faculdades, cursos, turmas, unidades curriculares (UCs), plano curricular, estudantes, docentes e administradores; define períodos letivos, atribuições (quem dá que UC a que turma), horários e feriados; valida ou rejeita sumários; corrige presenças e justifica faltas; vê relatórios de cumprimento do horário, horas lecionadas e estudantes em risco; exporta CSV para o Excel; consulta o registo de auditoria.

Docente: vê as aulas do dia, regista o sumário e as presenças com poucos toques, corrige sumários que ainda não foram validados, acompanha as horas dadas em cada disciplina e vê o horário.

Estudante: vê a assiduidade por disciplina, o histórico de aulas e faltas, o aviso de risco por faltas e o horário da turma.

## A lógica do sistema

Uma atribuição liga período, turma, UC e docente. O horário diz em que dias e horas essa atribuição tem aula. Com isso o sistema sabe que aulas deviam ter acontecido, e por isso consegue mostrar as que ficaram sem sumário. Cada sumário guarda a presença de cada estudante da turma (presente, falta ou justificada). As faltas justificadas não contam para o limite de faltas.

Regras configuráveis em `config/config.php`: percentagem de faltas a partir da qual o estudante entra em risco (25 por defeito), número mínimo de aulas antes de avaliar o risco, tentativas de login permitidas e tempo de bloqueio.

O docente só regista aulas das suas atribuições, nos últimos 7 dias e dentro do período letivo. O horário não aceita duas aulas ao mesmo tempo para a mesma turma ou o mesmo docente.

## Como correr (Windows com XAMPP)

1. Instale o XAMPP e ligue o Apache e o MySQL no painel de controlo.
2. No phpMyAdmin, separador Importar, importe `database/schema.sql`. Atenção: este ficheiro apaga a base `registro_sumario` e cria-a de novo.
3. Copie `config/config.example.php` para `config/config.php` (os valores por defeito servem para o XAMPP).
4. Dados de exemplo (opcional), no terminal, dentro da pasta do projeto:

   ```
   C:\xampp\php\php.exe database\dados_demo.php
   ```

   O script cria um período à volta da data de hoje, 5 docentes, 32 estudantes, horários e cerca de doze semanas de sumários e presenças. No fim escreve as contas de teste. Para apagar tudo e refazer, acrescente `--reset`.
5. Arranque o servidor:

   ```
   C:\xampp\php\php.exe -S localhost:8000 -t public
   ```

6. Abra `http://localhost:8000`. Sem os dados de exemplo, clique em "Primeira utilização" para criar o primeiro administrador.

Para colocar num servidor, aponte o domínio para a pasta `public` e deixe `config`, `src` e `database` fora dela.

## Segurança

Senhas guardadas com `password_hash`. Consultas preparadas em todo o acesso à base de dados. Texto escapado em todas as páginas. Token CSRF em todos os formulários que alteram dados. Sessão com cookie `HttpOnly` e `SameSite`, identificador renovado no login e expiração por inatividade. Bloqueio temporário depois de várias tentativas de login falhadas. Senhas definidas pela administração têm de ser trocadas no primeiro acesso. Cabeçalhos de segurança (CSP, X-Frame-Options e outros). Verificação de perfil em cada página. Registo de auditoria com quem fez o quê. Campos de CSV protegidos contra fórmulas do Excel.

## Estrutura

```
config/     configuração (config.example.php é o modelo)
database/   schema.sql e dados_demo.php
src/        ligação à base de dados, login, layout, cálculo de aulas e assiduidade, motor de cadastros
public/     páginas e a pasta assets (css e js)
```

Os cadastros simples (cursos, turmas, UCs, estudantes, docentes, administradores, faculdades, períodos, feriados, atribuições e horários) são descritos em `src/entidades.php` e usam o mesmo motor em `src/crud.php`. Para acrescentar um cadastro novo basta uma entrada nesse ficheiro e um ficheiro de três linhas em `public`.

## Limites conhecidos

Não envia emails: a recuperação de senha é feita pela administração, que define uma senha nova. Não há ainda notas nem pagamentos. O registo de aulas não funciona sem ligação à internet. Cada docente tem uma atribuição por turma e UC, sem suporte a dois docentes na mesma disciplina.
