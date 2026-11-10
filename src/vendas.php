<?php
$pageTitle  = 'Vendas';
$activePage = 'vendas';
require __DIR__ . '/../models/venda.php';
require __DIR__ . '/../template/header.php';

function formatarPreco(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatarData(string $data): string
{
    return date('d/m/Y', strtotime($data));
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h2 class="h4 mb-1">Vendas</h2>
        <p class="text-muted mb-0">
            <?= number_format($totalVendas, 0, ',', '.') ?>
            vendas registradas.
        </p>
    </div>
    <a href="cadastrar-venda.php" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Nova venda
    </a>
</div>

<!-- Tabela (desktop / tablet) -->
<div class="card d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="tabelaVendas">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Veículo</th>
                    <th>Valor</th>
                    <th>Data da venda</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['cliente_nome']) ?></td>
                        <td>
                            <?= htmlspecialchars($v['veiculo_marca'] . ' ' . $v['veiculo_modelo']) ?>
                            <span class="text-muted small">&middot; <?= $v['veiculo_ano'] ?></span>
                        </td>
                        <td class="fw-semibold"><?= formatarPreco((float) $v['valor']) ?></td>
                        <td><?= formatarData($v['data_venda']) ?></td>
                        <td class="text-end">
                            <a href="editar-venda.php?id=<?= $v['id'] ?>" class="btn btn-icon btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                class="btn btn-icon btn-sm btn-excluir"
                                title="Excluir"
                                data-bs-toggle="modal"
                                data-bs-target="#modalExcluir"
                                data-id="<?= $v['id'] ?>"
                                data-nome="<?= htmlspecialchars($v['cliente_nome'] . ' — ' . $v['veiculo_marca'] . ' ' . $v['veiculo_modelo']) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($vendas)): ?>
        <p class="text-muted text-center py-4 mb-0">Nenhuma venda registrada.</p>
    <?php endif; ?>
</div>

<!-- Cards (mobile) -->
<div class="row g-3 d-md-none" id="cardsVendas">
    <?php foreach ($vendas as $v): ?>
        <div class="col-12">
            <div class="card vehicle-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h3 class="h6 mb-0"><?= htmlspecialchars($v['veiculo_marca'] . ' ' . $v['veiculo_modelo']) ?></h3>
                        <span class="text-muted small"><?= formatarData($v['data_venda']) ?></span>
                    </div>
                    <p class="text-muted small mb-2">
                        <?= htmlspecialchars($v['cliente_nome']) ?> &middot; <?= $v['veiculo_ano'] ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><?= formatarPreco((float) $v['valor']) ?></span>
                        <div class="d-flex gap-1">
                            <a href="editar-venda.php?id=<?= $v['id'] ?>" class="btn btn-icon btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                class="btn btn-icon btn-sm btn-excluir"
                                title="Excluir"
                                data-bs-toggle="modal"
                                data-bs-target="#modalExcluir"
                                data-id="<?= $v['id'] ?>"
                                data-nome="<?= htmlspecialchars($v['cliente_nome'] . ' — ' . $v['veiculo_marca'] . ' ' . $v['veiculo_modelo']) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($vendas)): ?>
        <p class="text-muted text-center py-4 mb-0">Nenhuma venda registrada.</p>
    <?php endif; ?>
</div>

<!-- Paginação -->
<?php if ($totalPaginas > 1): ?>
    <nav aria-label="Paginação de vendas" class="mt-4">
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
                <h2 class="modal-title h5" id="modalExcluirLabel">Excluir venda</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    Excluir a venda <strong id="modalExcluirNome"></strong>?
                    O veículo volta a ficar disponível. Essa ação não pode ser desfeita.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>

                <form method="POST" action="/projeto_faculdade/models/venda.php" class="d-inline">
                    <input type="hidden" name="id" id="modalExcluirId">
                    <input type="hidden" name="type" value="delete">
                    <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2">
                        <i class="bi bi-trash"></i> Excluir venda
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