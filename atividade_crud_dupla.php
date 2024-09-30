<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "bloco_de_notas";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para escapar entradas e evitar SQL Injection
function escape($conn, $data) {
    return $conn->real_escape_string($data);
}

// Criar nota
if (isset($_POST['create'])) {
    $titulo = escape($conn, $_POST['titulo']);
    $categoria = escape($conn, $_POST['categoria']);
    $conteudo = escape($conn, $_POST['conteudo']);

    $sql = "INSERT INTO notas (titulo, categoria, conteudo) VALUES ('$titulo', '$categoria', '$conteudo')";
    
    if ($conn->query($sql) === TRUE) {
        echo "A nota foi criada";
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Atualizar nota
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $titulo = escape($conn, $_POST['titulo']);
    $categoria = escape($conn, $_POST['categoria']);
    $conteudo = escape($conn, $_POST['conteudo']);

    $sql = "UPDATE notas SET titulo='$titulo', categoria='$categoria', conteudo='$conteudo' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Alteração feita com sucesso!";
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Excluir nota
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $sql = "DELETE FROM notas WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Excluído com sucesso!";
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Selecionar notas
$sql = "SELECT * FROM notas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>CRUD AULA</title>
</head>
<body>

<h2>Criar nota</h2>
<form method="POST">
    Título: <input type="text" name="titulo" required><br>
    Categoria: <input type="text" name="categoria" required><br>
    Conteúdo: <textarea name="conteudo" required></textarea><br>
    <input type="submit" name="create" value="Criar Nota">
</form>

<h2>Atualizar nota</h2>
<form method="POST">
    ID da nota: <input type="number" name="id" placeholder="id" required><br><br>
    Título: <input type="text" name="titulo" required><br>
    Categoria: <input type="text" name="categoria" required><br>
    Conteúdo: <textarea name="conteudo" required></textarea><br>
    <input type="submit" name="update" value="Atualizar Nota">
</form>

<h2>Tabela de Notas</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Categoria</th>
        <th>Conteúdo</th>
        <th>Ações</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['titulo']; ?></td>
                <td><?php echo $row['categoria']; ?></td>
                <td><?php echo $row['conteudo']; ?></td>
                <td>
                    <a href="index.php?delete=<?php echo $row['id']; ?>">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">Nenhuma nota encontrada</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>

<?php
$conn->close();
?>