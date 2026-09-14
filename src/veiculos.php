<?php
$pageTitle  = 'Veículos';
$activePage = 'veiculos';
require_once __DIR__ . '/../models/veiculo.php';
require __DIR__ . '/../template/header.php';

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
        <h2 class="h4 mb-1">Veículos</h2>
        <!-- <p class="text-muted mb-0"><?= count($veiculosList) ?> veículos cadastrados no estoque.</p> -->
    </div>
    <a href="cadastrar-veiculo.php" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Novo veículo
    </a>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                    <input type="search" id="filtroBusca" class="form-control border-start-0 ps-0" placeholder="Buscar por marca ou modelo...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select id="filtroStatus" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="disponivel">Disponível</option>
                    <option value="vendido">Vendido</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select id="filtroMarca" class="form-select">
                    <option value="">Todas as marcas</option>
                    <?php foreach ($marcasDisponiveis as $marca): ?>
                        <option value="<?= htmlspecialchars($marca) ?>"><?= htmlspecialchars($marca) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-1 d-grid">
                <button type="button" id="filtroLimpar" class="btn btn-outline-secondary" title="Limpar filtros">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabela (desktop / tablet) -->
<div class="card d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="tabelaVeiculos">
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Ano</th>
                    <th>Cor</th>
                    <th>Km</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($veiculosList as $v): ?>
                    <tr data-marca="<?= htmlspecialchars($v['marca']) ?>" data-status="<?= $v['status'] ?>" data-busca="<?= htmlspecialchars(strtolower($v['marca'] . ' ' . $v['modelo'])) ?>">
                        <td><?= htmlspecialchars($v['marca']) ?></td>
                        <td><?= htmlspecialchars($v['modelo']) ?></td>
                        <td><?= $v['ano'] ?></td>
                        <td><?= htmlspecialchars($v['cor']) ?></td>
                        <td><?= number_format($v['quilometragem'], 0, ',', '.') ?> km</td>
                        <td class="fw-semibold"><?= formatarPreco($v['preco']) ?></td>
                        <td><?= badgeStatus($v['status']) ?></td>
                        <td class="text-end">
                            <a href="editar-veiculo.php?id=<?= $v['id'] ?>" class="btn btn-icon btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-icon btn-sm" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-muted text-center py-4 mb-0 d-none" id="tabelaVazia">Nenhum veículo encontrado com esse filtro.</p>
</div>

<!-- Cards (mobile) -->
<div class="row g-3 d-md-none" id="cardsVeiculos">
    <?php foreach ($veiculosList as $v): ?>
        <div class="col-12" data-marca="<?= htmlspecialchars($v['marca']) ?>" data-status="<?= $v['status'] ?>" data-busca="<?= htmlspecialchars(strtolower($v['marca'] . ' ' . $v['modelo'])) ?>">
            <div class="card vehicle-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h3 class="h6 mb-0"><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></h3>
                        <?= badgeStatus($v['status']) ?>
                    </div>
                    <p class="text-muted small mb-2">
                        <?= $v['ano'] ?> &middot; <?= htmlspecialchars($v['cor']) ?> &middot; <?= number_format($v['quilometragem'], 0, ',', '.') ?> km
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><?= formatarPreco($v['preco']) ?></span>
                        <div class="d-flex gap-1">
                            <a href="editar-veiculo.php?id=<?= $v['id'] ?>" class="btn btn-icon btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-icon btn-sm" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <p class="text-muted text-center py-4 mb-0 d-none" id="cardsVazio">Nenhum veículo encontrado com esse filtro.</p>
</div>

<?php
$pageScript = <<<'JS'
(function () {
    var busca = document.getElementById('filtroBusca');
    var status = document.getElementById('filtroStatus');
    var marca = document.getElementById('filtroMarca');
    var limpar = document.getElementById('filtroLimpar');

    var linhas = document.querySelectorAll('#tabelaVeiculos tbody tr');
    var cards = document.querySelectorAll('#cardsVeiculos > [data-busca]');
    var tabelaVazia = document.getElementById('tabelaVazia');
    var cardsVazio = document.getElementById('cardsVazio');

    function aplicarFiltro() {
        var termo = busca.value.trim().toLowerCase();
        var statusVal = status.value;
        var marcaVal = marca.value;
        var visiveisTabela = 0;
        var visiveisCards = 0;

        function corresponde(el) {
            var okBusca = !termo || el.dataset.busca.indexOf(termo) !== -1;
            var okStatus = !statusVal || el.dataset.status === statusVal;
            var okMarca = !marcaVal || el.dataset.marca === marcaVal;
            return okBusca && okStatus && okMarca;
        }

        linhas.forEach(function (linha) {
            var visivel = corresponde(linha);
            linha.classList.toggle('d-none', !visivel);
            if (visivel) visiveisTabela++;
        });

        cards.forEach(function (card) {
            var visivel = corresponde(card);
            card.classList.toggle('d-none', !visivel);
            if (visivel) visiveisCards++;
        });

        tabelaVazia.classList.toggle('d-none', visiveisTabela !== 0);
        cardsVazio.classList.toggle('d-none', visiveisCards !== 0);
    }

    [busca, status, marca].forEach(function (el) {
        el.addEventListener('input', aplicarFiltro);
    });

    limpar.addEventListener('click', function () {
        busca.value = '';
        status.value = '';
        marca.value = '';
        aplicarFiltro();
    });
})();
JS;
require __DIR__ . '/../template/footer.php';
?>