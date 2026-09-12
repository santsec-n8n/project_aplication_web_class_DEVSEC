<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

$termoBusca = trim($_GET['busca'] ?? '');
$res = null;
$erroConsulta = false;

if ($termoBusca !== '') {
    $sql = "SELECT id, nome, preco, estoque
            FROM produtos
            WHERE nome LIKE ?
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log('Erro ao preparar a consulta de produtos: ' . $conn->error);
        $erroConsulta = true;
    } else {
        $like = '%' . $termoBusca . '%';
        $stmt->bind_param('s', $like);

        if (!$stmt->execute()) {
            error_log('Erro ao consultar produtos: ' . $stmt->error);
            $erroConsulta = true;
        } else {
            $res = $stmt->get_result();
        }
    }
} else {
    $sql = "SELECT id, nome, preco, estoque FROM produtos ORDER BY id DESC";
    $res = $conn->query($sql);

    if ($res === false) {
        error_log('Erro ao consultar produtos: ' . $conn->error);
        $erroConsulta = true;
    }
}

$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8');
$inicial = htmlspecialchars(strtoupper(substr($_SESSION['usuario_nome'], 0, 1)), ENT_QUOTES, 'UTF-8');

function statusEstoque(int $estoque): string
{
    if ($estoque === 0) {
        return '<span class="stock-status stock-status-out">Esgotado</span>';
    }

    if ($estoque <= 5) {
        return '<span class="stock-status stock-status-low">Estoque baixo</span>';
    }

    return '<span class="stock-status stock-status-ok">Em estoque</span>';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KNOT HARDWARE — Produtos</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="inventory-page">
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-name">KNOT<span>HW</span></div>
            <div class="brand-sub">Estoque de Hardware</div>
        </div>

        <nav aria-label="Navegação principal">
            <ul class="sidebar-nav">
                <li><a href="home.php"><span class="nav-icon" aria-hidden="true">⌂</span>Dashboard</a></li>
                <li><a href="produtos.php" class="active"><span class="nav-icon" aria-hidden="true">▣</span>Produtos</a></li>
            </ul>
        </nav>
    </aside>

    <div class="dashboard-main">
        <header class="topbar">
            <div>
                <span class="eyebrow">KNOT HARDWARE</span>
                <h2>Produtos</h2>
            </div>
            <div class="topbar-user">
                <span><?= $nomeUsuario ?></span>
                <span class="avatar" aria-hidden="true"><?= $inicial ?></span>
            </div>
        </header>

        <main class="main-content">
            <section class="inventory-header">
                <div>
                    <span class="eyebrow">INVENTÁRIO</span>
                    <h1>Produtos</h1>
                    <p>Gerenciamento de componentes e estoque.</p>
                </div>
                <a href="produto_novo.php" class="btn">+ Novo produto</a>
            </section>

            <section class="inventory-panel">
                <?php if (!empty($_GET['sucesso'])): ?>
                    <p class="sucesso">Operação realizada com sucesso.</p>
                <?php endif; ?>

                <form class="search-bar inventory-search" action="produtos.php" method="GET">
                    <label class="sr-only" for="busca">Buscar produto</label>
                    <input
                        type="text"
                        id="busca"
                        name="busca"
                        placeholder="Buscar produto..."
                        value="<?= htmlspecialchars($termoBusca, ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <button type="submit" class="btn btn-secondary">Buscar</button>
                </form>

                <?php if ($erroConsulta): ?>
                    <div class="inventory-message inventory-message-error" role="alert">
                        Não foi possível carregar os produtos no momento. Tente novamente mais tarde.
                    </div>
                <?php elseif ($res->num_rows === 0): ?>
                    <div class="inventory-message">
                        <?= $termoBusca !== '' ? 'Nenhum produto encontrado.' : 'Nenhum produto cadastrado.' ?>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $res->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        </td>
                                        <td>R$ <?= number_format((float) $row['preco'], 2, ',', '.') ?></td>
                                        <td><?= (int) $row['estoque'] ?> un.</td>
                                        <td><?= statusEstoque((int) $row['estoque']) ?></td>
                                        <td class="inventory-actions">
                                            <a href="produto_editar.php?id=<?= (int) $row['id'] ?>">Editar</a>
                                            <a href="produto_excluir.php?id=<?= (int) $row['id'] ?>" onclick="return confirm('Deseja excluir?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <a href="home.php" class="inventory-back">Voltar ao painel</a>
        </main>
    </div>
</div>
</body>
</html>
