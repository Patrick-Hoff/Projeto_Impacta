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
                    <input type="number"
                        class="form-control"
                        id="ano"
                        name="ano"
                        placeholder="Ex: 2022"
                        min="1950"
                        max="<?= date('Y') + 1 ?>"
                        inputmode="numeric"
                        required>
                    <div class="invalid-feedback">Informe um ano válido.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label for="cor" class="form-label">Cor</label>
                    <input type="text" class="form-control" id="cor" name="cor" placeholder="Ex: Prata" required>
                    <div class="invalid-feedback">Informe a cor do veículo.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="quilometragemDisplay" class="form-label">Quilometragem</label>
                    <div class="input-group">
                        <input type="text"
                            class="form-control"
                            id="quilometragemDisplay"
                            inputmode="numeric"
                            placeholder="0"
                            autocomplete="off"
                            required>
                        <span class="input-group-text">km</span>
                        <div class="invalid-feedback">Informe a quilometragem.</div>
                    </div>
                    <input type="hidden" name="quilometragem" id="quilometragem">
                </div>
                <div class="col-12 col-md-6">
                    <label for="precoDisplay" class="form-label">Preço</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text"
                            class="form-control"
                            id="precoDisplay"
                            inputmode="numeric"
                            placeholder="0,00"
                            autocomplete="off"
                            required>
                        <div class="invalid-feedback">Informe o preço do veículo.</div>
                    </div>
                    <input type="hidden" name="preco" id="preco">
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


<?php
$pageScript = <<<'JS'
(function () {
    var form = document.getElementById('formVeiculo');

    // ---------- Bloqueia teclas inválidas em campos numéricos ----------
    function bloquearTeclasInvalidas(input) {
        input.addEventListener('keydown', function (e) {
            if (['e', 'E', '+', '-'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }
    bloquearTeclasInvalidas(document.getElementById('ano'));

    // ---------- Máscara de quilometragem (só números, com ponto de milhar) ----------
    var kmDisplay = document.getElementById('quilometragemDisplay');
    var kmHidden = document.getElementById('quilometragem');

    kmDisplay.addEventListener('input', function () {
        var digitos = kmDisplay.value.replace(/\D/g, ''); // remove tudo que não é número
        kmHidden.value = digitos; // valor limpo pro PHP/MySQL
        kmDisplay.value = digitos === '' ? '' : Number(digitos).toLocaleString('pt-BR');
    });

    // ---------- Máscara de preço (R$ 0.000,00) ----------
    var precoDisplay = document.getElementById('precoDisplay');
    var precoHidden = document.getElementById('preco');

    precoDisplay.addEventListener('input', function () {
        var digitos = precoDisplay.value.replace(/\D/g, '');

        if (digitos === '') {
            precoDisplay.value = '';
            precoHidden.value = '';
            return;
        }

        var valorEmCentavos = parseInt(digitos, 10);
        var valorReais = valorEmCentavos / 100;

        // valor "limpo" pro PHP/MySQL: 125000.50 (ponto decimal)
        precoHidden.value = valorReais.toFixed(2);

        // valor exibido pro usuário: 125.000,50
        precoDisplay.value = valorReais.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    });

    // ---------- Validação geral no submit ----------
    form.addEventListener('submit', function (event) {
        var camposInvalidos = !form.checkValidity();
        var kmVazio = kmHidden.value === '';
        var precoVazio = precoHidden.value === '';

        if (camposInvalidos || kmVazio || precoVazio) {
            event.preventDefault();
            event.stopPropagation();

            if (kmVazio) kmDisplay.setCustomValidity('Informe a quilometragem.');
            if (precoVazio) precoDisplay.setCustomValidity('Informe o preço.');
        }

        form.classList.add('was-validated');
    });

    kmDisplay.addEventListener('input', function () { kmDisplay.setCustomValidity(''); });
    precoDisplay.addEventListener('input', function () { precoDisplay.setCustomValidity(''); });
})();
JS;
require __DIR__ . '/../template/footer.php';
?>