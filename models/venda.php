<?php
require_once __DIR__ . '/../config/flash.php';
include_once __DIR__ . "/../config/connection.php";

$data = $_POST;
$tipo = $data["type"] ?? null;

if ($tipo === "create" || $tipo === "update") {

    $id        = (int) ($data["id"] ?? 0);
    $clienteId = filter_var($data["cliente_id"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $veiculoId = filter_var($data["veiculo_id"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $valor     = trim((string) ($data["valor"] ?? ""));
    $dataVenda = trim((string) ($data["data_venda"] ?? ""));
    $dataObj   = DateTime::createFromFormat("!Y-m-d", $dataVenda);

    $voltar = $tipo === "create"
        ? "/projeto_faculdade/cadastrar-venda.php"
        : "/projeto_faculdade/editar-venda.php?id=" . $id;

    $erro = null;

    if (!$clienteId) {
        $erro = "Selecione um cliente.";
    } else if (!$veiculoId) {
        $erro = "Selecione um veículo.";
    } else if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $valor) || (float) $valor <= 0) {
        $erro = "Informe um valor de venda válido.";
    } else if (!$dataObj || $dataObj->format("Y-m-d") !== $dataVenda || $dataObj > new DateTime("tomorrow")) {
        $erro = "Informe uma data de venda válida.";
    }

    if ($erro) {
        definirMensagem("erro", $erro);
        header("Location: " . $voltar);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $veiculoAnterior = null;

        if ($tipo === "update") {
            $stmt = $pdo->prepare("SELECT veiculo_id FROM vendas WHERE id = :id FOR UPDATE");
            $stmt->execute([":id" => $id]);
            $vendaAtual = $stmt->fetch();

            if (!$vendaAtual) {
                throw new RuntimeException("Venda não encontrada.");
            }

            $veiculoAnterior = (int) $vendaAtual["veiculo_id"];
        }

        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id = :id");
        $stmt->execute([":id" => $clienteId]);

        if (!$stmt->fetch()) {
            throw new RuntimeException("Cliente não encontrado.");
        }

        $trocouVeiculo = $veiculoAnterior !== $veiculoId;

        if ($trocouVeiculo) {
            $stmt = $pdo->prepare("SELECT status FROM veiculos WHERE id = :id FOR UPDATE");
            $stmt->execute([":id" => $veiculoId]);
            $veiculo = $stmt->fetch();

            if (!$veiculo) {
                throw new RuntimeException("Veículo não encontrado.");
            }

            if ($veiculo["status"] !== "disponivel") {
                throw new RuntimeException("Este veículo já foi vendido.");
            }
        }

        if ($tipo === "create") {
            $stmt = $pdo->prepare("
                INSERT INTO vendas (cliente_id, veiculo_id, valor, data_venda)
                VALUES (:cliente, :veiculo, :valor, :data_venda)
            ");
            $stmt->execute([
                ":cliente"    => $clienteId,
                ":veiculo"    => $veiculoId,
                ":valor"      => $valor,
                ":data_venda" => $dataVenda,
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE vendas
                SET cliente_id = :cliente, veiculo_id = :veiculo, valor = :valor, data_venda = :data_venda
                WHERE id = :id
            ");
            $stmt->execute([
                ":cliente"    => $clienteId,
                ":veiculo"    => $veiculoId,
                ":valor"      => $valor,
                ":data_venda" => $dataVenda,
                ":id"         => $id,
            ]);

            if ($trocouVeiculo) {
                $stmt = $pdo->prepare("UPDATE veiculos SET status = 'disponivel' WHERE id = :id");
                $stmt->execute([":id" => $veiculoAnterior]);
            }
        }

        if ($trocouVeiculo) {
            $stmt = $pdo->prepare("UPDATE veiculos SET status = 'vendido' WHERE id = :id AND status = 'disponivel'");
            $stmt->execute([":id" => $veiculoId]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException("Este veículo já foi vendido.");
            }
        }

        $pdo->commit();

        definirMensagem("sucesso", $tipo === "create" ? "Venda registrada com sucesso!" : "Venda atualizada com sucesso!");
        header("Location: /projeto_faculdade/vendas.php");
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if ($e instanceof RuntimeException) {
            $mensagem = $e->getMessage();
        } else if ($e instanceof PDOException && ($e->errorInfo[1] ?? null) === 1062) {
            $mensagem = "Este veículo já foi vendido.";
        } else {
            error_log($e->getMessage());
            $mensagem = $tipo === "create" ? "Erro ao registrar a venda." : "Erro ao atualizar a venda.";
        }

        definirMensagem("erro", $mensagem);
        header("Location: " . $voltar);
        exit;
    }
} else if ($tipo === "delete") {

    $id = (int) ($data["id"] ?? 0);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT veiculo_id FROM vendas WHERE id = :id FOR UPDATE");
        $stmt->execute([":id" => $id]);
        $venda = $stmt->fetch();

        if ($venda) {
            $stmt = $pdo->prepare("DELETE FROM vendas WHERE id = :id");
            $stmt->execute([":id" => $id]);

            $stmt = $pdo->prepare("UPDATE veiculos SET status = 'disponivel' WHERE id = :id");
            $stmt->execute([":id" => $venda["veiculo_id"]]);
        }

        $pdo->commit();

        definirMensagem("sucesso", "Venda excluída com sucesso!");
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        definirMensagem("erro", "Erro ao excluir a venda.");
    }

    header("Location: /projeto_faculdade/vendas.php");
    exit;
} else if (!empty($_GET["id"])) {

    $stmt = $pdo->prepare("
        SELECT
            v.id,
            v.cliente_id,
            v.veiculo_id,
            v.valor,
            v.data_venda,
            c.nome    AS cliente_nome,
            c.cpf     AS cliente_cpf,
            ve.marca  AS veiculo_marca,
            ve.modelo AS veiculo_modelo,
            ve.ano    AS veiculo_ano,
            ve.preco  AS veiculo_preco
        FROM vendas v
        INNER JOIN clientes c ON c.id = v.cliente_id
        INNER JOIN veiculos ve ON ve.id = v.veiculo_id
        WHERE v.id = :id
    ");
    $stmt->execute([":id" => (int) $_GET["id"]]);
    $venda = $stmt->fetch();
} else {

    $porPagina = 10;

    $paginaAtual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);

    if (!$paginaAtual || $paginaAtual < 1) {
        $paginaAtual = 1;
    }

    try {
        $totalVendas = (int) $pdo->query("SELECT COUNT(*) FROM vendas")->fetchColumn();

        $totalPaginas = max(1, (int) ceil($totalVendas / $porPagina));

        if ($paginaAtual > $totalPaginas) {
            $paginaAtual = $totalPaginas;
        }

        $offset = ($paginaAtual - 1) * $porPagina;

        $stmt = $pdo->prepare("
            SELECT
                v.id,
                v.valor,
                v.data_venda,
                c.nome   AS cliente_nome,
                ve.marca AS veiculo_marca,
                ve.modelo AS veiculo_modelo,
                ve.ano   AS veiculo_ano
            FROM vendas v
            INNER JOIN clientes c ON c.id = v.cliente_id
            INNER JOIN veiculos ve ON ve.id = v.veiculo_id
            ORDER BY v.data_venda DESC, v.id DESC
            LIMIT :limite
            OFFSET :offset
        ");

        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Erro ao consultar vendas: ' . $e->getMessage());
    }
}
