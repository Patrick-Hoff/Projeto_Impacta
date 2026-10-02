<?php
require_once __DIR__ . '/../config/connection.php';

// O veiculo.php lê $_GET['busca']; aqui ele vem do parâmetro "q" do fetch
$_GET['busca'] = trim($_GET['q'] ?? '');

// O id da URL é do cliente, então não pode ser usado como id de veículo
unset($_GET['id']);

// Com "apenas marcados", traz todos os selecionados (não só 10)
if (!empty($_GET['ids'])) {
    $porPagina = 200;
}

require __DIR__ . '/veiculo.php'; // gera $veiculos já filtrado

header('Content-Type: application/json; charset=utf-8');

echo json_encode(array_map(function ($v) {
    return [
        'id'     => (int) $v['id'],
        'marca'  => $v['marca'],
        'modelo' => $v['modelo'],
        'ano'    => $v['ano'],
        'preco'  => number_format($v['preco'], 2, ',', '.'),
    ];
}, $veiculos));