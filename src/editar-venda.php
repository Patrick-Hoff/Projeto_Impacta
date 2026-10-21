<?php
$pageTitle  = 'Editar venda';
$activePage = 'vendas';
// require __DIR__ . '/../models/venda.php'; // TODO: reativar quando o back estiver pronto

// ===================== DADOS MOCK (remover quando o back estiver pronto) =====================
$clientesOpcoes = [
    ['id' => 1, 'nome' => 'Ana Beatriz Souza',    'cpf' => '12345678901'],
    ['id' => 2, 'nome' => 'Carlos Eduardo Lima',  'cpf' => '23456789012'],
    ['id' => 3, 'nome' => 'Mariana Alves',        'cpf' => '34567890123'],
    ['id' => 4, 'nome' => 'Roberto Nascimento',   'cpf' => '45678901234'],
];

$veiculosOpcoes = [
    ['id' => 1, 'marca' => 'Toyota',     'modelo' => 'Corolla XEi', 'ano' => 2021, 'cor' => 'Prata',    'preco' => '112500.00'],
    ['id' => 2, 'marca' => 'Honda',      'modelo' => 'Civic EXL',   'ano' => 2019, 'cor' => 'Preto',    'preco' => '98900.00'],
    ['id' => 3, 'marca' => 'Volkswagen', 'modelo' => 'Polo TSI',    'ano' => 2022, 'cor' => 'Branco',   'preco' => '87000.00'],
    ['id' => 4, 'marca' => 'Fiat',       'modelo' => 'Toro Volcano', 'ano' => 2020, 'cor' => 'Vermelho', 'preco' => '124990.00'],
];

// Na edição, o veículo da venda (já vendido) também aparece no select
$venda = [
    'id'         => (int) ($_GET['id'] ?? 1),
    'cliente_id' => 2,
    'veiculo_id' => 2,
    'valor'      => '95000.00',
    'data_venda' => '2026-10-15',
];
// =============================================================================================

if (empty($venda)) {
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
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente_id" name="cliente_id" required>
                        <option value="" disabled>Selecione...</option>
                        <?php foreach ($clientesOpcoes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (int) $venda['cliente_id'] === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome']) ?> &middot; <?= formatarCpf($c['cpf']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecione o cliente.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="veiculo_id" class="form-label">Veículo</label>
                    <select class="form-select" id="veiculo_id" name="veiculo_id" required>
                        <option value="" disabled>Selecione...</option>
                        <?php foreach ($veiculosOpcoes as $v): ?>
                            <option value="<?= $v['id'] ?>"
                                data-preco="<?= htmlspecialchars((string) $v['preco']) ?>"
                                <?= (int) $venda['veiculo_id'] === (int) $v['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?> &middot; <?= $v['ano'] ?> &middot; <?= htmlspecialchars($v['cor']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Selecione um veículo disponível.</div>
                    <div class="form-text">
                        Aparecem os veículos disponíveis e o veículo desta venda. O veículo escolhido fica marcado como vendido.
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

require __DIR__ . '/../template/footer.php';
?>