<?php
$pageTitle  = 'Editar venda';
$activePage = 'vendas';
require __DIR__ . '/../models/venda.php';

if (empty($venda)) {
    definirMensagem('erro', 'Venda não encontrada.');
    header('Location: vendas.php');
    exit;
}

require __DIR__ . '/../template/header.php';

function formatarCpf(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-2">
            <li class="breadcrumb-item"><a href="vendas.php">Vendas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>
    <h2 class="h4 mb-1">Editar venda</h2>
    <p class="text-muted mb-0">Atualize os dados da venda selecionada.</p>
</div>

<div class="card">
    <div class="card-body p-3 p-lg-4">
        <form novalidate
            action="/projeto_faculdade/models/venda.php"
            method="post"
            id="formVenda"
            class="needs-validation">
            <div class="row g-3">

                <!-- Envio de informação Cadastrar ou Editar -->
                <input type="hidden" name="type" value="update">
                <input type="hidden" name="id" value="<?= (int) $venda['id'] ?>">

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
                            value="<?= htmlspecialchars($venda['cliente_nome'] . ' · ' . formatarCpf($venda['cliente_cpf'])) ?>"
                            required>

                        <input type="hidden" name="cliente_id" id="cliente_id" value="<?= (int) $venda['cliente_id'] ?>">

                        <div id="listaClientes"
                            class="list-group position-absolute w-100 shadow-sm d-none"
                            style="z-index: 1000; max-height: 240px; overflow-y: auto;"
                            role="listbox"></div>

                        <div class="invalid-feedback">Selecione um cliente da lista.</div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="buscaVeiculo" class="form-label">Veículo</label>

                    <div class="position-relative">
                        <input type="text"
                            id="buscaVeiculo"
                            class="form-control"
                            placeholder="Digite o nome veiculo"
                            autocomplete="off"
                            role="combobox"
                            aria-expanded="false"
                            aria-controls="listaVeiculos"
                            value="<?= htmlspecialchars($venda['veiculo_marca'] . ' ' . $venda['veiculo_modelo'] . ' · ' . $venda['veiculo_ano']) ?>"
                            required>

                        <input type="hidden"
                            name="veiculo_id"
                            id="veiculo_id"
                            value="<?= (int) $venda['veiculo_id'] ?>"
                            data-preco="<?= htmlspecialchars((string) $venda['veiculo_preco']) ?>">

                        <div id="listaVeiculos"
                            class="list-group position-absolute w-100 shadow-sm d-none"
                            style="z-index: 1000; max-height: 240px; overflow-y: auto;"
                            role="listbox"></div>

                        <div class="invalid-feedback">Selecione um veículo da lista.</div>
                    </div>
                    <div class="form-text">
                        A busca mostra somente veículos disponíveis. Ao trocar o veículo, o anterior volta a ficar disponível.
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
                    <input type="date"
                        class="form-control"
                        id="dataVenda"
                        name="data_venda"
                        value="<?= htmlspecialchars($venda['data_venda']) ?>"
                        required>
                    <div class="invalid-feedback">Informe uma data válida.</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                <a href="vendas.php" class="btn btn-outline-secondary order-2 order-sm-1">Cancelar</a>
                <button type="submit" class="btn btn-primary order-1 order-sm-2 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i> Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$valorJs = (float) $venda['valor'];

$pageScript = <<<JS
document.addEventListener('DOMContentLoaded', function () {
    iniciarFormVenda({
        valor: {$valorJs}
    });
});
JS;
?>

<script src="/projeto_faculdade/js/venda-busca.js" defer></script>

<?php require __DIR__ . '/../template/footer.php'; ?>
