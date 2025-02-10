<?php
// Conectar ao banco de dados SQLite
$db = new SQLite3('database.db');

// Criar a tabela se não existir
$db->exec("CREATE TABLE IF NOT EXISTS items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
)");

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $stmt = $db->prepare("INSERT INTO items (name) VALUES (:name)");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $stmt = $db->prepare("UPDATE items SET name = :name WHERE id = :id");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM items WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
    }
    header('Location: index.php');
    exit;
}

// Obter itens do banco de dados
$result = $db->query("SELECT * FROM items");
$items = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD com PHP e SQLite</title>
</head>
<body>
    <h2>Adicionar Item</h2>
    <form method="POST">
        <input type="text" name="name" required>
        <button type="submit" name="add">Adicionar</button>
    </form>
    
    <h2>Itens Cadastrados</h2>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <?php echo htmlspecialchars($item['name']); ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                    <button type="submit" name="edit">Editar</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <button type="submit" name="delete" onclick="return confirm('Tem certeza?');">Excluir</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
