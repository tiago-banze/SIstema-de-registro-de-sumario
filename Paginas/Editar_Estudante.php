<?php

session_start();
include_once '../PHP/config.php';
// include_once '../PHP/processarCad_Estudante.php';
include_once '../PHP/inactividade.php';
include_once('../PHP/SemAutenticacao.php');
protect();

if (isset($_SESSION['ID_estudante'])) {
    $ID_es = $_SESSION['ID_estudante'];
    // echo $id_uc;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['atualize'])) {
    $codigo = $_POST['codigo'];
    $nomeEstudante = $_POST['nome'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $curso = $_POST['curso'];
    $nacionalidade = $_POST['nacionalidade'];
    $provincia = $_POST['provincia'];
    $distrito = $_POST['distrito'];
    $genero = $_POST['genero'];
    $turma = $_POST['turma'];
    // echo $codigo, $nomeEstudante, $tel, $email, $curso, $nacionalidade, $provincia,$distrito, $genero, $turma;

    $sql = "UPDATE estudanteTBL SET ID_estudante=:novo_id, nome = :novo_nome, contacto=:novo_contacto, email=:novo_email, nacionalidade=:nova_nacionalidade, provincia=:nova_provincia, destrito=:novo_destrito, genero=:novo_genero, curso=:novo_curso, turma=:turma WHERE ID_estudante = :idestudante";
    $stmt = $Conexao->prepare($sql);
    $stmt->bindParam(':novo_id', $codigo);
    $stmt->bindParam(':idestudante', $ID_es);
    $stmt->bindParam(':novo_nome', $nomeEstudante);
    $stmt->bindParam(':novo_contacto', $tel);
    $stmt->bindParam(':novo_email', $email);
    $stmt->bindParam(':nova_nacionalidade', $nacionalidade);
    $stmt->bindParam(':nova_provincia', $provincia);
    $stmt->bindParam(':novo_destrito', $distrito);
    $stmt->bindParam(':novo_genero', $genero);
    $stmt->bindParam(':novo_curso', $curso);
    $stmt->bindParam(':turma', $turma);
    $stmt->execute();
    echo '<div class="ante_sucesso"><div class="sucesso"><center>Estudante atualizado com sucesso!!</center></div></div>';
    header('Refresh:1; url= ../Paginas/Listar_estudante.php');
}

?>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar_Estudante</title>
    <link rel="stylesheet" href="../EStilo/NavBar.css">
    <link rel="stylesheet" href="../EStilo/Cadastro_estudante.css">
    <link rel="stylesheet" href="../EStilo/universai_user.css">
    <link rel="icon" href="../icon/open-book_171322.png">
</head>

<body>
    <div class="cima"></div>
    <ul class="sidenav">
        <li><a class="active" href="../Paginas/menu.php">Home</a></li>
        <li>
            <div class="dropdown">
                <button class="dropbtn">Cadastro</button>
                <div class="dropdown-content">
                    <a href="../Paginas/Cadastro_Curso.php">Cursos</a>
                    <a href="../Paginas/Cadatro_UC.php">UC</a>
                    <!-- <a href="../Paginas/Associar_UC_curso.php">Associar_UC ao Curso</a> -->
                    <a href="../Paginas/Cadastro_Estudante.php">Estudantes</a>
                    <a href="../Paginas/Cadastro_Docente.php">Docentes</a>
                </div>
            </div>
        </li>


        <li>
            <div class="dropdown">
                <button class="dropbtn">Associar</button>
                <div class="dropdown-content">
                    <a href="../Paginas/Associar_Estudante_UC.php">Cadeira ao Estudante</a>
                    <a href="../Paginas/Associar_UC_docente.php">Cadeira ao Docente</a>
                    <a href="../Paginas/Associar_UC_curso.php">Cadeira ao Curso</a>
                </div>
            </div>
        </li>
        <li>
            <div class="dropdown">
                <button class="dropbtn">gerenciar</button>
                <div class="dropdown-content">
                    <a href="../Paginas/Lista_Curso.php">Cursos</a>
                    <a href="../Paginas/Listar_UC.php">UC</a>
                    <a href="../Paginas/Listar_estudante.php">Estudantes</a>
                    <a href="../Paginas/Listar_Docente.php">Docentes</a>
                </div>
            </div>
        </li>
        <li>
            <div class="dropdown">
                <button class="dropbtn">Presencas</button>
                <div class="dropdown-content">
                    <a href="../Paginas/presencaEstudante.php">Estudantes</a>
                    <a href="../Paginas/presencaDocente.php">Docentes</a>
                </div>
            </div>
        </li>
        <li>
            <div class="dropdown">
                <!-- <button class="dropbtn"><a href="../Paginas/sair.php">SAir</a><button>  -->
                <a href="../Paginas/sair.php" class="adicionar"><img src="../icon/sair.png" alt="icon" style="width: 50px; height: 50px; margin-left: 97px;"></a>
            </div>
        </li>
    </ul>

    <div class="content">
        <div class="barra">
            <div class="pesquisa">
                <form action="../Paginas/search.php" method="GET">
                    <p>
                        <span><input name="search" placeholder="Pesquisar" type="text"></span>
                        <span><button type="submit">Buscar</button></span>
                    </p>
                </form>
            </div>
            <h3>
                <?php
                include "../Paginas/data_admin.php";
                ?>
            </h3>
            <!-- <img src="../icon/ui_user_profile_person_icon_208878.png" style="width: 40px; height:40px" alt=""> -->
        </div>

        <?php
        $queryES = $Conexao->prepare("SELECT * FROM estudanteTBL WHERE ID_estudante = :id_estudante");
        $queryES->bindParam(':id_estudante', $ID_es);

        if ($queryES->execute()) {
            $resultado = $queryES->fetch(PDO::FETCH_ASSOC);
            // print_r($resultado);
            echo '
        <form action="" method="POST">
            <fieldset>
                <legend>
                    <h2>Editar Estudante</h2>
                </legend>
                <div class="pai">
                    <div class="left">
                        <input type="number" value="' . $resultado['ID_estudante'] . '" name="codigo" placeholder="Codígo do Estudante" class="tamanhoinput outro"><br>
                        <input type="text" name="nome" placeholder="Nome do Estudante" class="input tamanhoinput" value="' . $resultado['nome'] . '"><br>
                        <input type="tel" name="tel" id="" placeholder="Contacto" class="input tamanhoinput" value="' . $resultado['contacto'] . '"><br>
                        <select name="turma" class="class input tamanhoinput" required>
                            <option value="" selected disabled>Selecione nova Turma</option>';

            $queryTurmas = "SELECT * FROM turmatbl";
            $stmtTurmas = $Conexao->query($queryTurmas);
            $stmtTurmas->execute();
            while ($turma = $stmtTurmas->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $turma['ID_turma'] . '">' . $turma['ID_turma'] . '</option>';
            }

            echo '
                        </select><br>
                        <select name="curso" id="" class="input tamanhoinput">
                        <option value="" selected >Selecione novo curso</option>';

            // Buscar cursos cadastrados no banco de dados e preencher as opções do select
            $sql_cursos = "SELECT * FROM cursoTBL";
            $stmt_cursos = $Conexao->query($sql_cursos);
            while ($row_curso = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $row_curso['ID_curso'] . '">' . $row_curso['nome'] . '</option>';
            }
            echo '
                        </select>
                    </div>
                    <div class="right">
                        <input type="text" name="nacionalidade" id="rightinput" placeholder="Nacionalidade" class="tamanhoinput outro" value="' . $resultado['nacionalidade'] . '">
                        <br>
                        <input type="text" name="provincia" id="rightinput" placeholder="Provincia" class="input tamanhoinput rightinput" value="' . $resultado['provincia'] . '">
                        <br>
                        <input type="text" name="distrito" id="rightinput" placeholder="Distrito" class="input tamanhoinput rightinput" value="' . $resultado['destrito'] . '">
                        <br>
                        <select name="genero" id="rightinput" class="input tamanhoinput rightinput">
                            <option value="" selected disabled>Genero</option>
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select><br>
                        <input type="email" name="email" id="" placeholder="Email" class="input tamanhoinput" value="' . $resultado['email'] . '"> <br>
                    </div>
                    <input type="submit" name="atualize" value="Atualizar" class="botao">
                </div>
            </fieldset>
        </form>
        ';
        }
        ?>
    </div>
</body>

</html>