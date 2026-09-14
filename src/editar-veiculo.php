<?php
$pageTitle  = 'Editar veículo';
$activePage = 'veiculos';
require __DIR__ . '/../models/veiculo.php';

if (empty($veiculo)) {
    header('Location: veiculos.php');
    exit;
}

require __DIR__ . '/../template/header.php';
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-2">
            <li class="breadcrumb-item"><a href="veiculos.php">Veículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>
    <h2 class="h4 mb-1">Editar veículo</h2>
    <p class="text-muted mb-0">Atualize os dados do veículo selecionado.</p>
</div>

<div class="card">
    <div class="card-body p-3 p-lg-4">
        <form
            novalidate
            id="formVeiculo"
            class="needs-validation"
            method="post"
            action="../models/veiculo.php">
            <div class="row g-3">
                <input type="hidden" name="type" value="update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($veiculo['id']) ?>">

                <div class="col-12 col-md-6">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" class="form-control" id="marca" name="marca" required
                           value="<?= htmlspecialchars($veiculo['marca']) ?>">
                    <div class="invalid-feedback">Informe a marca do veículo.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" required
                           value="<?= htmlspecialchars($veiculo['modelo']) ?>">
                    <div class="invalid-feedback">Informe o modelo do veículo.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="ano" class="form-label">Ano</label>
                    <input type="number" class="form-control" id="ano" name="ano" min="1950" max="<?= date('Y') + 1 ?>" required
                           value="<?= htmlspecialchars($veiculo['ano']) ?>">
                    <div class="invalid-feedback">Informe um ano válido.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="cor" class="form-label">Cor</label>
                    <input type="text" class="form-control" id="cor" name="cor" required
                           value="<?= htmlspecialchars($veiculo['cor']) ?>">
                    <div class="invalid-feedback">Informe a cor do veículo.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="quilometragem" class="form-label">Quilometragem</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="quilometragem" name="quilometragem" min="0" required
                               value="<?= htmlspecialchars($veiculo['quilometragem']) ?>">
                        <span class="input-group-text">km</span>
                        <div class="invalid-feedback">Informe a quilometragem.</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="preco" class="form-label">Preço</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" class="form-control" id="preco" name="preco" min="0" step="0.01" required
                               value="<?= htmlspecialchars($veiculo['preco']) ?>">
                        <div class="invalid-feedback">Informe o preço do veículo.</div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="" disabled <?= empty($veiculo['status']) ? 'selected' : '' ?>>Selecione...</option>
                        <option value="disponivel" <?= $veiculo['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                        <option value="vendido" <?= $veiculo['status'] === 'vendido' ? 'selected' : '' ?>>Vendido</option>
                    </select>
                    <div class="invalid-feedback">Selecione o status do veículo.</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                <a href="veiculos.php" class="btn btn-outline-secondary order-2 order-sm-1">Cancelar</a>
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
    var form = document.getElementById('formVeiculo');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
})();
JS;
require __DIR__ . '/../template/footer.php';
?>