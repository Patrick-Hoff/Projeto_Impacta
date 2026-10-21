<?php
require_once __DIR__ . '/../config/connection.php';

// O cliente.php lê $_GET['busca']; aqui ele vem do parâmetro "q" do fetch
$_GET['busca'] = trim($_GET['q'] ?? '');

// Evita que um ?id= na URL faça o cliente.php carregar um cliente para edição
unset($_GET['id']);

require __DIR__ . '/cliente.php'; // gera $clientes já filtrado

function formatarCpfJson(string $cpf): string
{
    $d = preg_replace('/\D/', '', $cpf);
    return substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-' . substr($d, 9, 2);
}

header('Content-Type: application/json; charset=utf-8');

echo json_encode(array_map(function ($c) {
    return [
        'id'   => (int) $c['id'],
        'nome' => $c['nome'],
        'cpf'  => formatarCpfJson($c['cpf']),
    ];
}, $clientes), JSON_UNESCAPED_UNICODE);