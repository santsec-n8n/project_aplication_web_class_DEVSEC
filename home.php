<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

require 'db.php';

mysqli_report(MYSQLI_REPORT_OFF);

$dashboardReady = false;
$dashboardError = null;
$metrics = null;
$products = [];

// Estoque baixo: quantidade maior que zero e menor ou igual a cinco.
$metricsQuery = "SELECT
    COUNT(*) AS total_produtos,
    COALESCE(SUM(estoque), 0) AS total_unidades,
    COALESCE(SUM(CASE WHEN estoque > 0 AND estoque <= 5 THEN 1 ELSE 0 END), 0) AS estoque_baixo,
    COALESCE(SUM(CASE WHEN estoque = 0 THEN 1 ELSE 0 END), 0) AS sem_estoque,
    COALESCE(SUM(preco * estoque), 0) AS valor_estoque
    FROM produtos";

$metricsResult = $conn->query($metricsQuery);
$productsResult = $conn->query(
    "SELECT id, nome, preco, estoque
     FROM produtos
        WHERE estoque <= 5
     ORDER BY estoque ASC, id DESC
     LIMIT 5"
);

if ($metricsResult && $productsResult) {
    $metrics = $metricsResult->fetch_assoc();

    while ($product = $productsResult->fetch_assoc()) {
        $products[] = $product;
    }

    $dashboardReady = $metrics !== null;
}

if (!$dashboardReady) {
    $dashboardError = 'Não foi possível carregar os dados do estoque no momento.';
    error_log('Falha ao carregar as métricas do dashboard.');
}

$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8');
$inicial = htmlspecialchars(strtoupper(substr($_SESSION['usuario_nome'], 0, 1)), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KNOT HARDWARE — Painel</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="dashboard-page">
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-name">KNOT<span>HW</span></div>
            <div class="brand-sub">Estoque de Hardware</div>
        </div>

        <nav aria-label="Navegação principal">
            <ul class="sidebar-nav">
                <li><a href="home.php" class="active"><span class="nav-icon" aria-hidden="true">⌂</span>Dashboard</a></li>
                <li><a href="produtos.php"><span class="nav-icon" aria-hidden="true">▣</span>Produtos</a></li>
                <li><a href="logout.php"><span class="nav-icon" aria-hidden="true">↪</span>Sair</a></li>
            </ul>
        </nav>
    </aside>

    <div class="dashboard-main">
        <header class="topbar">
            <div>
                <span class="eyebrow">KNOT HARDWARE</span>
                <h2>Dashboard</h2>
            </div>
            <div class="topbar-user">
                <span><?= $nomeUsuario ?></span>
                <span class="avatar" aria-hidden="true"><?= $inicial ?></span>
            </div>
        </header>

        <main class="main-content">
            <!-- [CUSTOM IMAGE] BANNER / FOTO DO SLIPKNOT -->
            <section class="dashboard-banner">
                <div>
                    <span class="banner-kicker">PAINEL DE CONTROLE</span>
                    <h1>Bem-vindo, <?= $nomeUsuario ?>.</h1>
                    <p>Visão geral do estoque de hardware.</p>
                </div>
            </section>

            <?php if ($dashboardError): ?>
                <div class="dashboard-error" role="alert"><?= htmlspecialchars($dashboardError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php else: ?>
                <section class="stats-grid" aria-label="Resumo do estoque">
                    <article class="stat-card stat-card-featured">
                        <span class="stat-label">Produtos cadastrados</span>
                        <strong class="stat-value"><?= (int) $metrics['total_produtos'] ?></strong>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Unidades em estoque</span>
                        <strong class="stat-value"><?= (int) $metrics['total_unidades'] ?></strong>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Estoque baixo</span>
                        <strong class="stat-value"><?= (int) $metrics['estoque_baixo'] ?></strong>
                        <span class="stat-note">1 a 5 unidades</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-label">Sem estoque</span>
                        <strong class="stat-value"><?= (int) $metrics['sem_estoque'] ?></strong>
                    </article>
                    <article class="stat-card stat-card-value">
                        <span class="stat-label">Valor estimado</span>
                        <strong class="stat-value">R$ <?= number_format((float) $metrics['valor_estoque'], 2, ',', '.') ?></strong>
                        <span class="stat-note">preço x quantidade</span>
                    </article>
                </section>

                <section class="dashboard-section">
                    <div class="section-heading">
                        <div>
                            <span class="eyebrow">MONITORAMENTO</span>
                            <h2>Itens que exigem atenção</h2>
                        </div>
                        <a href="produtos.php" class="text-link">Ver todos</a>
                    </div>

                    <div class="product-summary">
                        <?php if (!$products): ?>
                            <p class="empty-state">Nenhum produto exige atenção no momento.</p>
                        <?php else: ?>
                            <div class="table-wrapper">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Preço</th>
                                            <th>Estoque</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($product['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>R$ <?= number_format((float) $product['preco'], 2, ',', '.') ?></td>
                                                <td>
                                                    <span class="stock-status <?= (int) $product['estoque'] === 0 ? 'stock-status-out' : ((int) $product['estoque'] <= 5 ? 'stock-status-low' : 'stock-status-ok') ?>">
                                                        <?= (int) $product['estoque'] ?> un.
                                                    </span>
                                                </td>
                                                <td><a href="produto_editar.php?id=<?= (int) $product['id'] ?>">Editar</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <div class="dashboard-actions">
                <a href="produtos.php" class="btn">Acessar produtos</a>
                <a href="produto_novo.php" class="btn btn-secondary">Cadastrar produto</a>
            </div>
        </main>
    </div>
</div>
</body>
</html>