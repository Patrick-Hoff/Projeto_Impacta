<?php

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/../config/connection.php';
require __DIR__ . '/../template/header.php';

$resumoVeiculos = $pdo->query("
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(status = 'disponivel'), 0) AS disponiveis,
        COALESCE(SUM(status = 'vendido'), 0) AS vendidos,
        COALESCE(SUM(CASE WHEN status = 'disponivel' THEN preco ELSE 0 END), 0) AS valor_estoque
    FROM veiculos
")->fetch();

$resumoVendasMes = $pdo->query("
    SELECT
        COUNT(*) AS quantidade,
        COALESCE(SUM(valor), 0) AS total
    FROM vendas
    WHERE data_venda >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
      AND data_venda < DATE_FORMAT(CURDATE(), '%Y-%m-01') + INTERVAL 1 MONTH
")->fetch();

$totalVendido = (float) $pdo->query("SELECT COALESCE(SUM(valor), 0) FROM vendas")->fetchColumn();

$disponiveis = $pdo->query("
    SELECT marca, modelo, ano, cor, quilometragem, preco, status
    FROM veiculos
    WHERE status = 'disponivel'
    ORDER BY id DESC
    LIMIT 3
")->fetchAll();

$totalVeiculos  = (int) $resumoVeiculos['total'];
$qtdDisponiveis = (int) $resumoVeiculos['disponiveis'];
$qtdVendidos    = (int) $resumoVeiculos['vendidos'];
$valorEmEstoque = (float) $resumoVeiculos['valor_estoque'];
$vendasNoMes    = (int) $resumoVendasMes['quantidade'];
$valorVendasMes = (float) $resumoVendasMes['total'];

function formatarPreco(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function badgeStatus(string $status): string
{
    return $status === 'disponivel'
        ? '<span class="badge rounded-pill" style="background-color: var(--ad-success);">Disponível</span>'
        : '<span class="badge rounded-pill" style="background-color: var(--ad-secondary);">Vendido</span>';
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h2 class="h4 mb-1">Visão geral</h2>
        <p class="text-muted mb-0">Resumo do estoque e das vendas da loja.</p>
    </div>
    <a href="cadastrar-veiculo.php" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Novo veículo
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-car-front-fill"></i></div>
                <div>
                    <div class="stat-card__label">Total de veículos</div>
                    <div class="stat-card__value"><?= $totalVeiculos ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card stat-card stat-card--success h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-card__label">Disponíveis</div>
                    <div class="stat-card__value"><?= $qtdDisponiveis ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card stat-card stat-card--secondary h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="stat-card__label">Vendidos</div>
                    <div class="stat-card__value"><?= $qtdVendidos ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="stat-card__label">Valor em estoque</div>
                    <div class="stat-card__value fs-4"><?= formatarPreco($valorEmEstoque) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card stat-card stat-card--success h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div class="stat-card__label">Vendas no mês</div>
                    <div class="stat-card__value"><?= $vendasNoMes ?></div>
                    <div class="small text-muted"><?= formatarPreco($valorVendasMes) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card stat-card stat-card--secondary h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="stat-card__label">Total vendido</div>
                    <div class="stat-card__value fs-4"><?= formatarPreco($totalVendido) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
        <h2 class="h6 mb-0">Veículos disponíveis</h2>
        <a href="veiculos.php" class="small">Ver todos <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="card-body">
        <?php if (empty($disponiveis)): ?>
            <p class="text-muted text-center py-3 mb-0">Nenhum veículo disponível no momento.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($disponiveis as $v): ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card vehicle-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="h6 mb-0"><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></h3>
                                    <?= badgeStatus($v['status']) ?>
                                </div>
                                <p class="text-muted small mb-2">
                                    <?= (int) $v['ano'] ?> &middot; <?= htmlspecialchars($v['cor'] ?? '') ?> &middot; <?= number_format((float) $v['quilometragem'], 0, ',', '.') ?> km
                                </p>
                                <p class="fw-bold mb-0"><?= formatarPreco((float) $v['preco']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>
