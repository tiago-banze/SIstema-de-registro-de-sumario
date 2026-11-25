# Sistema de Registo de Sumário

Sistema web para docentes registarem sumários e presenças, e para a administração gerir cursos, turmas, UCs, estudantes e docentes.

Feito em PHP 8 (PDO) e MySQL, sem frameworks.

## Estrutura

```
config/      configuração (config.example.php é o modelo)
database/    schema.sql (tabelas e dados iniciais)
src/         código partilhado (ligação à BD, login, layout, motor de cadastros)
public/      páginas acessíveis no navegador e a pasta assets (css, js)
```

## Como correr (Windows, XAMPP ou WAMP)

1. Crie a base de dados: abra o phpMyAdmin, separador **Importar**, e importe `database/schema.sql`.
2. Copie `config/config.example.php` para `config/config.php` e ajuste utilizador e senha da base de dados.
3. Arranque pelo terminal (Git Bash) na pasta do projeto:

   ```bash
   php -S localhost:8000 -t public
   ```

   Ou coloque o projeto em `htdocs` e aponte o Apache para a pasta `public`.
4. Abra `http://localhost:8000`. Na primeira vez, clique em **Criar o administrador**.

Os docentes entram com o email e a senha definidos no cadastro de docentes.

## O que mudou em relação à versão antiga

- Menu, cabeçalho e rodapé num único sítio (antes estavam copiados em cada página).
- Um único ficheiro de estilos com uma paleta nova (teal, azul-petróleo e âmbar).
- Senhas guardadas com `password_hash` (antes estavam em texto simples).
- Proteção contra XSS (todo o texto sai escapado) e contra CSRF (token em todos os formulários).
- Apagar passa a ser por POST com confirmação (antes era por link).
- Cadastros de cursos, turmas, UCs, estudantes, docentes e faculdades gerados a partir de `src/entidades.php`.
- Registo de sumário e presenças gravados numa única transação.
- Sessão expira por inatividade (30 minutos, configurável).

## Notas

- Requer PHP 8.0 ou superior com a extensão `pdo_mysql`.
- O esquema da base de dados é novo. Os dados de exemplo do projeto antigo (faculdades, cursos, turmas e UCs) já vêm no `schema.sql`.
