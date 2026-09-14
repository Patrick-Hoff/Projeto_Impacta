<?php
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require __DIR__ . '/../template/header.php';

/**
 * Dados mockados — substituir por consulta ao MySQL (tabela `veiculos`)
 * quando a conexão estiver pronta em config/connection.php.
 */
$veiculosMock = [
    ['marca' => 'Toyota',     'modelo' => 'Corolla',    'ano' => 2022, 'cor' => 'Prata',    'km' => 32000, 'preco' => 125000.00, 'status' => 'disponivel'],
    ['marca' => 'Honda',      'modelo' => 'HR-V',       'ano' => 2021, 'cor' => 'Branco',   'km' => 41200, 'preco' => 118500.00, 'status' => 'disponivel'],
    ['marca' => 'Volkswagen', 'modelo' => 'T-Cross',    'ano' => 2023, 'cor' => 'Cinza',    'km' => 12800, 'preco' => 132900.00, 'status' => 'disponivel'],
    ['marca' => 'Chevrolet',  'modelo' => 'Onix',       'ano' => 2020, 'cor' => 'Vermelho', 'km' => 58000, 'preco' => 72900.00,  'status' => 'vendido'],
    ['marca' => 'Hyundai',    'modelo' => 'HB20',       'ano' => 2022, 'cor' => 'Preto',    'km' => 27500, 'preco' => 79900.00,  'status' => 'disponivel'],
    ['marca' => 'Jeep',       'modelo' => 'Renegade',   'ano' => 2019, 'cor' => 'Branco',   'km' => 63400, 'preco' => 89900.00,  'status' => 'vendido'],
];

$totalVeiculos     = count($veiculosMock);
$disponiveis       = array_filter($veiculosMock, fn($v) => $v['status'] === 'disponivel');
$vendidos          = array_filter($veiculosMock, fn($v) => $v['status'] === 'vendido');
$valorEmEstoque    = array_sum(array_column($disponiveis, 'preco'));

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
        <p class="text-muted mb-0">Resumo do estoque da loja em tempo real.</p>
    </div>
    <a href="cadastrar-veiculo.php" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Novo veículo
    </a>
</div>

<!-- Cards de estatística -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
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
    <div class="col-6 col-lg-3">
        <div class="card stat-card stat-card--success h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-card__label">Disponíveis</div>
                    <div class="stat-card__value"><?= count($disponiveis) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card stat-card--secondary h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-card__icon"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="stat-card__label">Vendidos</div>
                    <div class="stat-card__value"><?= count($vendidos) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
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
</div>

<!-- Veículos disponíveis -->
<div class="card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom">
        <h2 class="h6 mb-0">Veículos disponíveis</h2>
        <a href="veiculos.php" class="small">Ver todos <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php foreach (array_slice($disponiveis, 0, 3) as $v): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card vehicle-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h3 class="h6 mb-0"><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></h3>
                                <?= badgeStatus($v['status']) ?>
                            </div>
                            <p class="text-muted small mb-2">
                                <?= $v['ano'] ?> &middot; <?= htmlspecialchars($v['cor']) ?> &middot; <?= number_format($v['km'], 0, ',', '.') ?> km
                            </p>
                            <p class="fw-bold mb-0"><?= formatarPreco($v['preco']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../template/footer.php'; ?>