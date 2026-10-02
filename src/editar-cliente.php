<?php
$pageTitle  = 'Editar cliente';
$activePage = 'Clientes';
require __DIR__ . '/../models/cliente.php';
require __DIR__ . '/../models/veiculo.php';

if (!$cliente) {
    header('Location: clientes.php');
    exit;
}

require __DIR__ . '/../template/header.php';
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-2">
            <li class="breadcrumb-item"><a href="clientes.php">Clientes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>
    <h2 class="h4 mb-1">Editar cliente</h2>
    <p class="text-muted mb-0">Edite os dados do cliente e, se quiser, marque os veículos de interesse.</p>
</div>

<div class="card">
    <div class="card-body p-3 p-lg-4">
        <form novalidate
            action="../models/cliente.php"
            method="post"
            id="formCliente"
            class="needs-validation">
            <div class="row g-3">

                <!-- Envio de informação Cadastrar ou Editar -->
                <input type="hidden" name="type" value="update">
                <input type="hidden" name="id" value="<?= (int) $cliente['id'] ?>">

                <div class="col-12 col-md-6">
                    <label for="nome" class="form-label">Nome completo</label>
                    <input type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars($cliente['nome']) ?>"
                        placeholder="Ex: Ana Beatriz Souza"
                        required>
                    <div class="invalid-feedback">Informe o nome do cliente.</div>
                </div>

                <div class="col-12 col-md-3">
                    <label for="cpfDisplay" class="form-label">CPF</label>
                    <input type="text"
                        class="form-control"
                        id="cpfDisplay"
                        inputmode="numeric"
                        value="<?= htmlspecialchars($cliente['cpf']) ?>"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        autocomplete="off"
                        required>
                    <div class="invalid-feedback">Informe um CPF válido.</div>
                    <input type="hidden" name="cpf" id="cpf" value="<?= htmlspecialchars(preg_replace('/\D/', '', $cliente['cpf'])) ?>">
                </div>

                <div class="col-12 col-md-3">
                    <label for="telefoneDisplay" class="form-label">Telefone</label>
                    <input type="text"
                        class="form-control"
                        id="telefoneDisplay"
                        inputmode="numeric"
                        value="<?= htmlspecialchars($cliente['telefone']) ?>"
                        placeholder="(00) 00000-0000"
                        maxlength="15"
                        autocomplete="off"
                        required>
                    <div class="invalid-feedback">Informe um telefone válido.</div>
                    <input type="hidden" name="telefone" id="telefone" value="<?= htmlspecialchars(preg_replace('/\D/', '', $cliente['telefone'])) ?>">
                </div>

                <div class="col-12 col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($cliente['email']) ?>"
                        placeholder="Ex: ana.souza@email.com"
                        required>
                    <div class="invalid-feedback">Informe um email válido.</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                <div>
                    <h3 class="h6 mb-1">Veículos de interesse</h3>
                    <p class="text-muted small mb-0">Opcional. Marque os veículos que esse cliente tem interesse.</p>
                </div>
                <div class="input-group" style="max-width: 280px;">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search"
                        id="buscaVeiculoInteresse"
                        class="form-control border-start-0 ps-0"
                        placeholder="Filtrar veículos...">
                </div>
            </div>


            <div class="border rounded" style="max-height: 260px; overflow-y: auto;">
                <ul class="list-group list-group-flush" id="listaVeiculosInteresse">
                    <?php foreach ($veiculos as $v): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        value="<?= $v['id'] ?>"
                                        id="veiculo<?= $v['id'] ?>"
                                        <?= in_array((int) $v['id'], $idsInteresse, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="veiculo<?= $v['id'] ?>">
                                        <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?>
                                        <span class="text-muted small">&middot; <?= $v['ano'] ?></span>
                                    </label>
                                </div>
                                <span class="text-muted small">R$ <?= number_format($v['preco'], 2, ',', '.') ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p class="text-muted text-center py-3 mb-0 d-none" id="listaVeiculosVazia">Nenhum veículo encontrado.</p>
            </div>

            <!-- Seleção completa que vai no POST (mantida pelo JS) -->
            <div id="interessesSelecionados">
                <?php foreach ($idsInteresse as $idInteresse): ?>
                    <input type="hidden" name="veiculos_interesse[]" value="<?= (int) $idInteresse ?>">
                <?php endforeach; ?>
            </div>

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

            <hr class="my-4">

            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                <a href="clientes.php" class="btn btn-outline-secondary order-2 order-sm-1">Cancelar</a>
                <button type="submit" class="btn btn-primary order-1 order-sm-2 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i> Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$pageScript = <<<'JS'
(function () {
    iniciarFormCliente();
})();
JS;



require __DIR__ . '/../template/footer.php';
?>