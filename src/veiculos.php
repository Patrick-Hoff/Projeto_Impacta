<?php
$pageTitle  = 'Veículos';
$activePage = 'veiculos';
require __DIR__ . '/../models/veiculo.php';
require __DIR__ . '/../template/header.php';

$marcasDisponiveis = array_values(array_unique(array_column($veiculos, 'marca')));
sort($marcasDisponiveis);

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
        <p class="text-muted mb-0">
            <?= number_format($totalVeiculos, 0, ',', '.') ?>
            veículos encontrados.
        </p>
    </div>
    <a href="cadastrar-veiculo.php" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Novo veículo
    </a>
</div>

<!-- Filtros -->
<form method="GET" action="" class="card mb-3">
    <div class="card-body">
        <div class="row g-2">

            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="search"
                        name="busca"
                        class="form-control border-start-0 ps-0"
                        placeholder="Buscar por marca ou modelo..."
                        value="<?= htmlspecialchars($busca) ?>">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>

                    <option
                        value="disponivel"
                        <?= $status === 'disponivel' ? 'selected' : '' ?>>
                        Disponível
                    </option>

                    <option
                        value="vendido"
                        <?= $status === 'vendido' ? 'selected' : '' ?>>
                        Vendido
                    </option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <select name="marca" class="form-select">
                    <option value="">Todas as marcas</option>

                    <?php foreach ($marcasDisponiveis as $marcaItem): ?>

                        <option
                            value="<?= htmlspecialchars($marcaItem) ?>"
                            <?= $marca === $marcaItem ? 'selected' : '' ?>>
                            <?= htmlspecialchars($marcaItem) ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-1 d-grid">
                <a href="veiculos.php" class="btn btn-outline-secondary">
                    Limpar
                </a>

            </div>
            <button
                type="submit"
                class="btn btn-primary"
                title="Pesquisar">
                <i class="bi bi-search"></i>
            </button>

        </div>
    </div>
</form>


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
                <?php foreach ($veiculos as $v): ?>
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
                            <button type="button"
                                class="btn btn-icon btn-sm btn-excluir"
                                title="Excluir"
                                data-bs-toggle="modal"
                                data-bs-target="#modalExcluir"
                                data-id="<?= $v['id'] ?>"
                                data-nome="<?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?>">
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
    <?php foreach ($veiculos as $v): ?>
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
                            <button type="button"
                                class="btn btn-icon btn-sm btn-excluir"
                                title="Excluir"
                                data-bs-toggle="modal"
                                data-bs-target="#modalExcluir"
                                data-id="<?= $v['id'] ?>"
                                data-nome="<?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?>">
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

<!-- Paginação -->
<?php if ($totalPaginas > 1): ?>
    <nav aria-label="Paginação de veículos" class="mt-4">
        <ul class="pagination justify-content-center mb-0">

            <?php
            function urlPagina(int $pagina): string
            {
                $params = $_GET;
                $params['pagina'] = $pagina;
                return '?' . http_build_query($params);
            }
            ?>

            <li class="page-item <?= $paginaAtual <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= urlPagina($paginaAtual - 1) ?>" aria-label="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                <li class="page-item <?= $p === $paginaAtual ? 'active' : '' ?>">
                    <a class="page-link" href="<?= urlPagina($p) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= urlPagina($paginaAtual + 1) ?>" aria-label="Próxima">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>

        </ul>
    </nav>
<?php endif; ?>


<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="modalExcluirLabel">Excluir veículo</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    Excluir <strong id="modalExcluirNome"></strong> do estoque?
                    Essa ação não pode ser desfeita.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>

                <form method="POST" action="../models/veiculo.php" class="d-inline">
                    <input type="hidden" name="id" id="modalExcluirId">
                    <input type="hidden" name="type" value="delete">
                    <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2">
                        <i class="bi bi-trash"></i> Excluir veículo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$pageScript = <<<'JS'
(function () {
    var modal = document.getElementById('modalExcluir');

    modal.addEventListener('show.bs.modal', function (event) {
        var botao = event.relatedTarget;
        document.getElementById('modalExcluirId').value = botao.dataset.id;
        document.getElementById('modalExcluirNome').textContent = botao.dataset.nome;
    });
})();
JS;

require __DIR__ . '/../template/footer.php';
?>