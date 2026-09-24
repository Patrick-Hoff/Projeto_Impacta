<?php
$pageTitle  = 'Clientes';
$activePage = 'Clientes';
// require __DIR__ . '/../models/cliente.php'; // TODO: reativar quando o back estiver pronto
require __DIR__ . '/../template/header.php';

// ==========================================================
// MOCK DATA - remover este bloco quando o model/cliente.php
// estiver pronto e passar a fornecer $clientes, $totalClientes,
// $busca, $paginaAtual e $totalPaginas de verdade.
// ==========================================================
$clientesMock = [
    ['id' => 1,  'nome' => 'Ana Beatriz Souza',      'cpf' => '11144477735', 'telefone' => '11987654321', 'email' => 'ana.souza@email.com'],
    ['id' => 2,  'nome' => 'Bruno Carvalho Lima',     'cpf' => '22255588846', 'telefone' => '21976543210', 'email' => 'bruno.lima@email.com'],
    ['id' => 3,  'nome' => 'Carla Mendes Ferreira',   'cpf' => '33366699957', 'telefone' => '31965432109', 'email' => 'carla.ferreira@email.com'],
    ['id' => 4,  'nome' => 'Diego Ramos Oliveira',    'cpf' => '44477700068', 'telefone' => '41954321098', 'email' => 'diego.oliveira@email.com'],
    ['id' => 5,  'nome' => 'Elaine Pires Gonçalves',  'cpf' => '55588811179', 'telefone' => '51943210987', 'email' => 'elaine.goncalves@email.com'],
    ['id' => 6,  'nome' => 'Felipe Nunes Barbosa',    'cpf' => '66699922280', 'telefone' => '61932109876', 'email' => 'felipe.barbosa@email.com'],
    ['id' => 7,  'nome' => 'Gabriela Rocha Martins',  'cpf' => '77700033391', 'telefone' => '71921098765', 'email' => 'gabriela.martins@email.com'],
    ['id' => 8,  'nome' => 'Henrique Alves Teixeira', 'cpf' => '88811144402', 'telefone' => '81910987654', 'email' => 'henrique.teixeira@email.com'],
    ['id' => 9,  'nome' => 'Isabela Costa Ribeiro',   'cpf' => '99922255513', 'telefone' => '91909876543', 'email' => 'isabela.ribeiro@email.com'],
    ['id' => 10, 'nome' => 'João Pedro Santos',       'cpf' => '10033366624', 'telefone' => '19998765432', 'email' => 'joao.santos@email.com'],
    ['id' => 11, 'nome' => 'Karina Duarte Moreira',   'cpf' => '12144470035', 'telefone' => '27987654321', 'email' => 'karina.moreira@email.com'],
    ['id' => 12, 'nome' => 'Lucas Fonseca Azevedo',   'cpf' => '13255583146', 'telefone' => '48976543210', 'email' => 'lucas.azevedo@email.com'],
];

$busca      = trim($_GET['busca'] ?? '');
$porPagina  = 5;
$paginaAtual = max(1, (int) ($_GET['pagina'] ?? 1));

$clientesFiltrados = $clientesMock;

if ($busca !== '') {
    $termo = mb_strtolower($busca);
    $clientesFiltrados = array_values(array_filter($clientesMock, function ($c) use ($termo) {
        $alvo = mb_strtolower($c['nome'] . ' ' . $c['cpf'] . ' ' . $c['telefone'] . ' ' . $c['email']);
        return str_contains($alvo, $termo);
    }));
}

$totalClientes = count($clientesFiltrados);
$totalPaginas  = max(1, (int) ceil($totalClientes / $porPagina));
$paginaAtual   = min($paginaAtual, $totalPaginas);

$clientes = array_slice($clientesFiltrados, ($paginaAtual - 1) * $porPagina, $porPagina);
// ==========================================================
// FIM DO MOCK DATA
// ==========================================================


function formatarCpf(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

function formatarTelefone(string $telefone): string
{
    $telefone = preg_replace('/\D/', '', $telefone);

    if (strlen($telefone) === 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
    }

    if (strlen($telefone) === 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6, 4);
    }

    return $telefone;
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h2 class="h4 mb-1">Clientes</h2>
        <p class="text-muted mb-0">
            <?= number_format($totalClientes, 0, ',', '.') ?>
            Clientes encontrados.
        </p>
    </div>
    <a href="cadastrar-cliente.php" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Novo Cliente
    </a>
</div>

<!-- Filtros -->
<form method="GET" action="" class="card mb-3">
    <div class="card-body">
        <div class="row g-2">

            <div class="col-12 col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="search"
                        name="busca"
                        class="form-control border-start-0 ps-0"
                        placeholder="Buscar por nome, CPF, telefone ou email..."
                        value="<?= htmlspecialchars($busca) ?>">
                </div>
            </div>

            <div class="col-6 col-md-1 d-grid">
                <a href="clientes.php" class="btn btn-outline-secondary">
                    Limpar
                </a>
            </div>

            <div class="col-6 col-md-1 d-grid">
                <button
                    type="submit"
                    class="btn btn-primary"
                    title="Pesquisar">
                    <i class="bi bi-search"></i>
                </button>
            </div>

        </div>
    </div>
</form>


<!-- Tabela (desktop / tablet) -->
<div class="card d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="tabelaClientes">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <tr data-busca="<?= htmlspecialchars(strtolower($c['nome'] . ' ' . $c['cpf'] . ' ' . $c['telefone'] . ' ' . $c['email'])) ?>">
                        <td><?= htmlspecialchars($c['nome']) ?></td>
                        <td><?= formatarCpf($c['cpf']) ?></td>
                        <td><?= formatarTelefone($c['telefone']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td class="text-end">
                            <a href="interesses-cliente.php?id=<?= $c['id'] ?>" class="btn btn-icon btn-sm" title="Interesse em veículos">
                                <i class="bi bi-heart"></i>
                            </a>
                            <a href="editar-cliente.php?id=<?= $c['id'] ?>" class="btn btn-icon btn-sm" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                class="btn btn-icon btn-sm btn-excluir"
                                title="Excluir"
                                data-bs-toggle="modal"
                                data-bs-target="#modalExcluir"
                                data-id="<?= $c['id'] ?>"
                                data-nome="<?= htmlspecialchars($c['nome']) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-muted text-center py-4 mb-0 d-none" id="tabelaVazia">Nenhum cliente encontrado com esse filtro.</p>
</div>

<!-- Cards (mobile) -->
<div class="row g-3 d-md-none" id="cardsClientes">
    <?php foreach ($clientes as $c): ?>
        <div class="col-12" data-busca="<?= htmlspecialchars(strtolower($c['nome'] . ' ' . $c['cpf'] . ' ' . $c['telefone'] . ' ' . $c['email'])) ?>">
            <div class="card client-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h3 class="h6 mb-0"><?= htmlspecialchars($c['nome']) ?></h3>
                        <span class="text-muted small"><?= formatarCpf($c['cpf']) ?></span>
                    </div>
                    <p class="text-muted small mb-2">
                        <?= formatarTelefone($c['telefone']) ?> &middot; <?= htmlspecialchars($c['email']) ?>
                    </p>
                    <div class="d-flex justify-content-end gap-1">
                        <a href="interesses-cliente.php?id=<?= $c['id'] ?>" class="btn btn-icon btn-sm" title="Interesse em veículos">
                            <i class="bi bi-heart"></i>
                        </a>
                        <a href="editar-cliente.php?id=<?= $c['id'] ?>" class="btn btn-icon btn-sm" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button"
                            class="btn btn-icon btn-sm btn-excluir"
                            title="Excluir"
                            data-bs-toggle="modal"
                            data-bs-target="#modalExcluir"
                            data-id="<?= $c['id'] ?>"
                            data-nome="<?= htmlspecialchars($c['nome']) ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <p class="text-muted text-center py-4 mb-0 d-none" id="cardsVazio">Nenhum cliente encontrado com esse filtro.</p>
</div>

<!-- Paginação -->
<?php if ($totalPaginas > 1): ?>
    <nav aria-label="Paginação de clientes" class="mt-4">
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
                <h2 class="modal-title h5" id="modalExcluirLabel">Excluir cliente</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    Excluir <strong id="modalExcluirNome"></strong> do cadastro?
                    Essa ação não pode ser desfeita.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>

                <form method="POST" action="../models/cliente.php" class="d-inline">
                    <input type="hidden" name="id" id="modalExcluirId">
                    <input type="hidden" name="type" value="delete">
                    <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2">
                        <i class="bi bi-trash"></i> Excluir cliente
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