<?php
$pageTitle  = 'Cadastrar veículo';
$activePage = 'veiculos';
require __DIR__ . '/../template/header.php';
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-2">
            <li class="breadcrumb-item"><a href="veiculos.php">Veículos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cadastrar</li>
        </ol>
    </nav>
    <h2 class="h4 mb-1">Cadastrar veículo</h2>
    <p class="text-muted mb-0">Preencha os dados do veículo para adicioná-lo ao estoque.</p>
</div>

<div class="card">
    <div class="card-body p-3 p-lg-4">
        <!--
            action="salvar-veiculo.php" (a criar quando conectar ao MySQL)
            method="POST"
        -->
        <form novalidate
            action="../models/veiculo.php"
            method="post"
            id="formVeiculo"
            class="needs-validation">
            <div class="row g-3">

                <!-- Envio de informação Cadastrar ou Editar -->
                <input type="hidden" name="type" value="create">

                <div class="col-12 col-md-6">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" class="form-control" id="marca" name="marca" placeholder="Ex: Toyota" required>
                    <div class="invalid-feedback">Informe a marca do veículo.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ex: Corolla" required>
                    <div class="invalid-feedback">Informe o modelo do veículo.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="ano" class="form-label">Ano</label>
                    <input type="number" class="form-control" id="ano" name="ano" placeholder="Ex: 2022" min="1950" max="<?= date('Y') + 1 ?>" required>
                    <div class="invalid-feedback">Informe um ano válido.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="cor" class="form-label">Cor</label>
                    <input type="text" class="form-control" id="cor" name="cor" placeholder="Ex: Prata" required>
                    <div class="invalid-feedback">Informe a cor do veículo.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="quilometragem" class="form-label">Quilometragem</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="quilometragem" name="quilometragem" placeholder="0" min="0" required>
                        <span class="input-group-text">km</span>
                        <div class="invalid-feedback">Informe a quilometragem.</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="preco" class="form-label">Preço</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" class="form-control" id="preco" name="preco" placeholder="0,00" min="0" step="0.01" required>
                        <div class="invalid-feedback">Informe o preço do veículo.</div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="" selected disabled>Selecione...</option>
                        <option value="disponivel">Disponível</option>
                        <option value="vendido">Vendido</option>
                    </select>
                    <div class="invalid-feedback">Selecione o status do veículo.</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                <a href="veiculos.php" class="btn btn-outline-secondary order-2 order-sm-1">Cancelar</a>
                <button type="submit" class="btn btn-primary order-1 order-sm-2 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-check-lg"></i> Cadastrar veículo
                </button>
            </div>
        </form>
    </div>
</div>

<?php
require __DIR__ . '/../template/footer.php';
?>