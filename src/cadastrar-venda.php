<?php
$pageTitle  = 'Registrar venda';
$activePage = 'vendas';
// require __DIR__ . '/../models/venda.php'; // TODO: reativar quando o back estiver pronto

$veiculosOpcoes = [
    ['id' => 1, 'marca' => 'Toyota',     'modelo' => 'Corolla XEi', 'ano' => 2021, 'cor' => 'Prata',    'preco' => '112500.00'],
    ['id' => 2, 'marca' => 'Honda',      'modelo' => 'Civic EXL',   'ano' => 2019, 'cor' => 'Preto',    'preco' => '98900.00'],
    ['id' => 3, 'marca' => 'Volkswagen', 'modelo' => 'Polo TSI',    'ano' => 2022, 'cor' => 'Branco',   'preco' => '87000.00'],
    ['id' => 4, 'marca' => 'Fiat',       'modelo' => 'Toro Volcano', 'ano' => 2020, 'cor' => 'Vermelho', 'preco' => '124990.00'],
];
// =============================================================================================
require __DIR__ . '/../template/header.php';
require __DIR__ . '/../models/cliente.php';

function formatarCpf(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

$semClientes = empty($clientes);
$semVeiculos = empty($veiculosOpcoes);
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-2">
            <li class="breadcrumb-item"><a href="vendas.php">Vendas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registrar</li>
        </ol>
    </nav>
    <h2 class="h4 mb-1">Registrar venda</h2>
    <p class="text-muted mb-0">Selecione o cliente e o veículo e informe o valor e a data da venda.</p>
</div>

<?php if ($semClientes): ?>
    <div class="alert alert-warning" role="alert">
        Nenhum cliente cadastrado. <a href="cadastrar-cliente.php" class="alert-link">Cadastrar cliente</a>
    </div>
<?php endif; ?>

<?php if ($semVeiculos): ?>
    <div class="alert alert-warning" role="alert">
        Nenhum veículo disponível para venda. <a href="cadastrar-veiculo.php" class="alert-link">Cadastrar veículo</a>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-3 p-lg-4">
        <form novalidate
            action="/projeto_faculdade/models/venda.php"
            method="post"
            id="formVenda"
            class="needs-validation">
            <div class="row g-3">

                <!-- Envio de informação Cadastrar ou Editar -->
                <input type="hidden" name="type" value="create">

                <div class="col-12 col-md-6">
                    <label for="buscaCliente" class="form-label">Cliente</label>

                    <div class="position-relative">
                        <input type="text"
                            id="buscaCliente"
                            class="form-control"
                            placeholder="Digite o nome ou CPF..."
                            autocomplete="off"
                            role="combobox"
                            aria-expanded="false"
                            aria-controls="listaClientes"
                            required
                            <?= $semClientes ? 'disabled' : '' ?>>

                        <!-- id do cliente escolhido: é este campo que vai no POST -->
                        <input type="hidden" name="cliente_id" id="cliente_id">

                        <div id="listaClientes"
                            class="list-group position-absolute w-100 shadow-sm d-none"
                            style="z-index: 1000; max-height: 240px; overflow-y: auto;"
                            role="listbox"></div>

                        <div class="invalid-feedback">Selecione um cliente da lista.</div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="veiculo_id" class="form-label">Veículo</label>
                    <select class="form-select" id="veiculo_id" name="veiculo_id" required <?= $semVeiculos ? 'disabled' : '' ?>>
                        <option value="" selected disabled>Selecione...</option>
                        <?php foreach ($veiculosOpcoes as $v): ?>
                            <option value="<?= $v['id'] ?>" data-preco="<?= htmlspecialchars((string) $v['preco']) ?>">
                                <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?> &middot; <?= $v['ano'] ?> &middot; <?= htmlspecialchars($v['cor']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecione um veículo disponível.</div>
                    <div class="form-text">
                        Somente veículos disponíveis. Ao salvar, o veículo é marcado como vendido.
                        <span id="precoTabela" class="d-block"></span>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="valorDisplay" class="form-label">Valor da venda</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text"
                            class="form-control"
                            id="valorDisplay"
                            inputmode="numeric"
                            autocomplete="off"
                            required>
                        <div class="invalid-feedback">Informe o valor da venda.</div>
                    </div>
                    <input type="hidden" name="valor" id="valor">
                </div>

                <div class="col-12 col-md-6">
                    <label for="dataVenda" class="form-label">Data da venda</label>
                    <input type="date" class="form-control" id="dataVenda" name="data_venda" required>
                    <div class="invalid-feedback">Informe uma data válida.</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                <a href="vendas.php" class="btn btn-outline-secondary order-2 order-sm-1">Cancelar</a>
                <button type="submit"
                    class="btn btn-primary order-1 order-sm-2 d-inline-flex align-items-center justify-content-center gap-2"
                    <?= ($semClientes || $semVeiculos) ? 'disabled' : '' ?>>
                    <i class="bi bi-check-lg"></i> Registrar venda
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
    iniciarFormVenda();
});
JS;
?>

<script src="/projeto_faculdade/js/venda-busca-cliente.js" defer></script>

<?php require __DIR__ . '/../template/footer.php'; ?>