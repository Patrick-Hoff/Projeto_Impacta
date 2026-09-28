<?php
$pageTitle  = 'Editar cliente';
$activePage = 'Clientes';

// ==========================================================
// MOCK DATA - remover quando o model/cliente.php estiver pronto
// Substituir por: $cliente = buscarClientePorId($id);
//                 $veiculosInteresse = buscarInteressesDoCliente($id);
// ==========================================================
$clientesMock = [
    1 => ['id' => 1, 'nome' => 'Ana Beatriz Souza', 'cpf' => '12345678909', 'telefone' => '11987654321', 'email' => 'ana.souza@email.com', 'veiculos_interesse' => [1, 4]],
    2 => ['id' => 2, 'nome' => 'Carlos Eduardo Lima', 'cpf' => '98765432100', 'telefone' => '1133445566', 'email' => 'carlos.lima@email.com', 'veiculos_interesse' => []],
];

// Lista de veículos disponíveis para marcar como "interesse" do cliente
$veiculosDisponiveis = [
    ['id' => 1, 'marca' => 'Toyota',     'modelo' => 'Corolla',  'ano' => 2022, 'preco' => 125000.00],
    ['id' => 2, 'marca' => 'Honda',      'modelo' => 'Civic',    'ano' => 2021, 'preco' => 118000.00],
    ['id' => 3, 'marca' => 'Volkswagen', 'modelo' => 'Gol',      'ano' => 2019, 'preco' => 58000.00],
    ['id' => 4, 'marca' => 'Chevrolet',  'modelo' => 'Onix',     'ano' => 2023, 'preco' => 92000.00],
    ['id' => 5, 'marca' => 'Fiat',       'modelo' => 'Argo',     'ano' => 2020, 'preco' => 68000.00],
    ['id' => 6, 'marca' => 'Hyundai',    'modelo' => 'HB20',     'ano' => 2022, 'preco' => 79000.00],
    ['id' => 7, 'marca' => 'Jeep',       'modelo' => 'Renegade', 'ano' => 2021, 'preco' => 115000.00],
    ['id' => 8, 'marca' => 'Toyota',     'modelo' => 'Hilux',    'ano' => 2020, 'preco' => 210000.00],
];
// ==========================================================

// Recebe e valida o id do cliente (clientes.php -> editar-cliente.php?id=1)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$cliente = $id ? ($clientesMock[$id] ?? null) : null;

// Antes do require do header, pois o header já imprime HTML
if (!$cliente) {
    header('Location: clientes.php');
    exit;
}

$veiculosInteresse = $cliente['veiculos_interesse'];

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
                    <?php foreach ($veiculosDisponiveis as $v): ?>
                        <li class="list-group-item"
                            data-busca="<?= htmlspecialchars(strtolower($v['marca'] . ' ' . $v['modelo'] . ' ' . $v['ano'])) ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="veiculos_interesse[]"
                                        value="<?= $v['id'] ?>"
                                        id="veiculo<?= $v['id'] ?>"
                                        <?= in_array($v['id'], $veiculosInteresse, true) ? 'checked' : '' ?>>
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