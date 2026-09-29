<?php
require_once __DIR__ . '/../config/flash.php';
include_once __DIR__ . "/../config/connection.php";

$data = $_POST;

if (($data["type"] ?? null) === "create") {

    $nome = $data["nome"];
    $cpf = $data["cpf"];
    $telefone = $data["telefone"];
    $email = $data["email"];

    $pdo->beginTransaction();

    try {

        // 1. Cria o cliente
        $stmt = $pdo->prepare("
            INSERT INTO clientes (nome, cpf, telefone, email)
            VALUES (:nome, :cpf, :telefone, :email)
        ");

        $stmt->execute([
            ":nome" => $nome,
            ":cpf" => $cpf,
            ":telefone" => $telefone,
            ":email" => $email
        ]);

        // 2. Pega o ID criado
        $idCliente = $pdo->lastInsertId();

        // 3. Cadastra os veículos
        foreach ($data["veiculos_interesse"] as $idVeiculo) {

            $stmt = $pdo->prepare("
                INSERT INTO interesses (cliente_id, veiculo_id)
                VALUES (:cliente, :idveiculo)
            ");

            $stmt->execute([
                ":cliente" => $idCliente,
                ":idveiculo" => $idVeiculo
            ]);
        }

        // 4. Confirma tudo
        $pdo->commit();

        definirMensagem("sucesso", "Cliente cadastrado com sucesso!");
    } catch (Exception $e) {

        // Desfaz tudo
        $pdo->rollBack();

        throw $e;
    }

    header("Location: ../src/clientes.php");
    exit;
} else if (($data["type"] ?? null) === "update") {

    $nome     = $data["nome"];
    $cpf      = $data["cpf"];
    $telefone = $data["telefone"];
    $email    = $data["email"];
    $id       = (int) $data["id"];

    // IDs marcados no formulário (se desmarcar todos, a chave não existe)
    $marcados = array_values(array_unique(array_map('intval', $data["veiculos_interesse"] ?? [])));

    try {
        $pdo->beginTransaction();

        // 1. Atualiza os dados do cliente
        $query = "UPDATE clientes
        SET nome = :nome, cpf = :cpf, telefone = :telefone, email = :email
        WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->bindParam(":telefone", $telefone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        // 2. Remove somente os veículos desmarcados
        if ($marcados) {
            $placeholders = implode(',', array_fill(0, count($marcados), '?'));
            $del = $pdo->prepare("DELETE FROM interesses WHERE cliente_id = ? AND veiculo_id NOT IN ($placeholders)");
            $del->execute(array_merge([$id], $marcados));
        } else {
            $del = $pdo->prepare("DELETE FROM interesses WHERE cliente_id = ?");
            $del->execute([$id]);
        }

        // 3. Insere os marcados (os que já existem são ignorados)
        if ($marcados) {
            $ins = $pdo->prepare("INSERT IGNORE INTO interesses (cliente_id, veiculo_id) VALUES (?, ?)");
            foreach ($marcados as $veiculoId) {
                $ins->execute([$id, $veiculoId]);
            }
        }

        $pdo->commit();

        definirMensagem("sucesso", "Cliente atualizado com sucesso!");
        header("Location: ../src/clientes.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        definirMensagem("erro", "Erro ao atualizar o cliente.");
        header("Location: ../src/clientes.php");
        exit;
    }
} else if (($data["type"] ?? null) === "delete") {

    // Em desenvolvimento

} else {

    // Verificação edit
    $id = $_GET["id"] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->execute(["id" => $id]);

        $cliente = $stmt->fetch();

        $query = "SELECT veiculo_id FROM interesses WHERE cliente_id = :cliente";

        $stmt = $pdo->prepare($query);

        $stmt->execute([":cliente" => $id]);

        $veiculosInteresse = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // =====================================================
    // PAGINAÇÃO
    // =====================================================

    $porPagina = 10;

    $paginaAtual = filter_input(
        INPUT_GET,
        'pagina',
        FILTER_VALIDATE_INT
    );

    if (!$paginaAtual || $paginaAtual < 1) {
        $paginaAtual = 1;
    }


    // =====================================================
    // FILTRO (campo único: nome, email, cpf ou telefone)
    // =====================================================

    $busca = trim($_GET['busca'] ?? '');


    // =====================================================
    // MONTAR WHERE
    // =====================================================

    $whereSql = '';
    $params = [];

    if ($busca !== '') {

        $condicoes = [
            'nome LIKE :busca_nome',
            'email LIKE :busca_email',
        ];

        $termoBusca = '%' . $busca . '%';

        $params[':busca_nome'] = $termoBusca;
        $params[':busca_email'] = $termoBusca;

        // CPF e telefone são salvos só com dígitos (os campos hidden do form).
        // Então a busca deles usa só os dígitos digitados, ignorando pontos,
        // traço, parênteses e espaços. Se o usuário digitou só texto (ex: um
        // nome), não há dígitos e essas duas condições nem entram na query,
        // pois LIKE '%%' casaria com todos os clientes.
        $buscaDigitos = preg_replace('/\D/', '', $busca);

        if ($buscaDigitos !== '') {

            $condicoes[] = 'cpf LIKE :busca_cpf';
            $condicoes[] = 'telefone LIKE :busca_telefone';

            $termoDigitos = '%' . $buscaDigitos . '%';

            $params[':busca_cpf'] = $termoDigitos;
            $params[':busca_telefone'] = $termoDigitos;
        }

        $whereSql = 'WHERE (' . implode(' OR ', $condicoes) . ')';
    }


    // =====================================================
    // CONSULTAR TOTAL
    // =====================================================

    try {

        $queryTotal = "
        SELECT COUNT(*)
        FROM clientes
        $whereSql
    ";

        $stmtTotal = $pdo->prepare($queryTotal);

        foreach ($params as $param => $valor) {

            $stmtTotal->bindValue(
                $param,
                $valor
            );
        }

        $stmtTotal->execute();

        $totalClientes = (int) $stmtTotal->fetchColumn();


        // =================================================
        // CALCULAR PAGINAÇÃO
        // =================================================

        $totalPaginas = max(
            1,
            (int) ceil(
                $totalClientes / $porPagina
            )
        );


        if ($paginaAtual > $totalPaginas) {

            $paginaAtual = $totalPaginas;
        }


        $offset = (
            $paginaAtual - 1
        ) * $porPagina;


        // =================================================
        // CONSULTAR CLIENTES
        // =================================================

        $query = "
        SELECT *
        FROM clientes
        $whereSql
        ORDER BY id DESC
        LIMIT :limite
        OFFSET :offset
    ";

        $stmt = $pdo->prepare($query);


        foreach ($params as $param => $valor) {

            $stmt->bindValue(
                $param,
                $valor
            );
        }


        $stmt->bindValue(
            ':limite',
            $porPagina,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );


        $stmt->execute();

        $clientes = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    } catch (PDOException $e) {

        die('Erro ao consultar clientes: ' .
            $e->getMessage());
    }
}
