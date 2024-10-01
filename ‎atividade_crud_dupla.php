<?php
$servidor = "localhost"; 
$usuario = "root"; 
$senha = "root"; 
$banco_de_dados = "Atividade_dupla_ruan"; 

$conexao = new mysqli($servidor, $usuario, $senha, $banco_de_dados);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_nota = $_POST['titulo'];
    $texto_nota = $_POST['conteudo'];
    $usuario_id = 1; 

    if (isset($_POST['nota_id']) && !empty($_POST['nota_id'])) {
        $nota_id = $_POST['nota_id'];
        $consulta = $conexao->prepare("UPDATE nota SET titulo = ?, conteudo = ? WHERE id_nota = ?");
        $consulta->bind_param("ssi", $titulo_nota, $texto_nota, $nota_id);
    } else {
        $consulta = $conexao->prepare("INSERT INTO nota (titulo, conteudo, id_usuario) VALUES (?, ?, ?)");
        $consulta->bind_param("ssi", $titulo_nota, $texto_nota, $usuario_id);
    }
    $consulta->execute();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['remover_id'])) {
    $nota_id = $_GET['remover_id'];
    $consulta = $conexao->prepare("DELETE FROM nota WHERE id_nota = ?");
    $consulta->bind_param("i", $nota_id);
    $consulta->execute();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$nota_detalhada = null;
if (isset($_GET['nota_id'])) {
    $nota_id = $_GET['nota_id'];
    $consulta = $conexao->prepare("SELECT * FROM nota WHERE id_nota = ?");
    $consulta->bind_param("i", $nota_id);
    $consulta->execute();
    $resultado = $consulta->get_result();
    $nota_detalhada = $resultado->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Notas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?= $nota_detalhada ? 'Atualizar Nota' : 'Adicionar Nova Nota' ?></h1>
    <form method="POST">
        <?php if ($nota_detalhada): ?>
            <input type="hidden" name="nota_id" value="<?= htmlspecialchars($nota_detalhada['id_nota']) ?>">
        <?php endif; ?>
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?= $nota_detalhada ? htmlspecialchars($nota_detalhada['titulo']) : '' ?>" required>

        <label for="conteudo">Conteúdo:</label>
        <textarea id="conteudo" name="conteudo" required><?= $nota_detalhada ? htmlspecialchars($nota_detalhada['conteudo']) : '' ?></textarea>

        <button type="submit"><?= $nota_detalhada ? 'Atualizar' : 'Adicionar' ?></button>
    </form>

    <h2>Lista de Notas</h2>
    <ul>
        <?php
        $resultado = $conexao->query("SELECT * FROM nota");
        while ($linha = $resultado->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($linha['titulo']) ?></strong>
                <br>
                <small><?= nl2br(htmlspecialchars($linha['conteudo'])) ?></small>
                <br>
                <a href="?nota_id=<?= $linha['id_nota'] ?>">Editar</a> |
                <a href="?remover_id=<?= $linha['id_nota'] ?>" onclick="return confirm('Você realmente deseja excluir esta nota?');">Excluir</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>