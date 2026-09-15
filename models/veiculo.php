<?php
require_once __DIR__ . '/../config/flash.php';
include_once __DIR__ . "/../config/connection.php";

$data = $_POST;

if (!empty($data)) {


    if ($data["type"] === "create") {

        $marca = $data["marca"];
        $modelo = $data["modelo"];
        $ano = $data["ano"];
        $cor = $data["cor"];
        $quilometragem = $data["quilometragem"];
        $preco = $data["preco"];
        $status = $data["status"];

        $query = "INSERT INTO veiculos
            (marca, modelo, ano, cor, quilometragem, preco, status)
            VALUES
            (:marca, :modelo, :ano, :cor, :quilometragem, :preco, :status)";

        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":marca", $marca);
        $stmt->bindParam(":modelo", $modelo);
        $stmt->bindParam(":ano", $ano);
        $stmt->bindParam(":cor", $cor);
        $stmt->bindParam(":quilometragem", $quilometragem);
        $stmt->bindParam(":preco", $preco);
        $stmt->bindParam(":status", $status);

        $stmt->execute();

        definirMensagem("sucesso", "Veículo cadastrado com sucesso!");
        header("Location: ../src/veiculos.php");
        exit;
    } else if ($data["type"] === "update") {

        $id = $data["id"];
        $marca = $data["marca"];
        $modelo = $data["modelo"];
        $ano = $data["ano"];
        $cor = $data["cor"];
        $quilometragem = $data["quilometragem"];
        $preco = $data["preco"];
        $status = $data["status"];

        $query = "UPDATE veiculos
        SET marca = :marca, modelo = :modelo, ano = :ano, cor = :cor, quilometragem = :quilometragem, preco = :preco, status = :status
        WHERE id = :id";

        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":marca", $marca);
        $stmt->bindParam(":modelo", $modelo);
        $stmt->bindParam(":ano", $ano);
        $stmt->bindParam(":cor", $cor);
        $stmt->bindParam(":quilometragem", $quilometragem);
        $stmt->bindParam(":preco", $preco);
        $stmt->bindParam(":status", $status);

        $stmt->execute();

        definirMensagem("sucesso", "Veículo atualizado com sucesso!");
        header("Location: ../src/veiculos.php");
        exit;
    } else if ($data["type"] === "delete") {

        $id = $data["id"];

        $query = "DELETE FROM veiculos WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $id);

        $stmt->execute();

        definirMensagem("sucesso", "Veículo excluído com sucesso!");
        header("Location: ../src/veiculos.php");
        exit;
    }
} else {

    // Verificação edit
    $id = $_GET["id"] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = :id");
        $stmt->execute(["id" => $id]);

        $veiculo = $stmt->fetch();
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
    // FILTROS
    // =====================================================

    $busca = trim($_GET['busca'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $marca = trim($_GET['marca'] ?? '');


    // =====================================================
    // MONTAR WHERE
    // =====================================================

    $where = [];
    $params = [];


    // Busca por marca ou modelo
    if ($busca !== '') {

        $where[] = "
        (
            marca LIKE :busca_marca
            OR modelo LIKE :busca_modelo
        )
    ";

        $termoBusca = '%' . $busca . '%';

        $params[':busca_marca'] = $termoBusca;
        $params[':busca_modelo'] = $termoBusca;
    }


    // Filtro por status
    if ($status !== '') {

        $where[] = 'status = :filtro_status';

        $params[':filtro_status'] = $status;
    }


    // Filtro por marca
    if ($marca !== '') {

        $where[] = 'marca = :filtro_marca';

        $params[':filtro_marca'] = $marca;
    }


    $whereSql = '';

    if (!empty($where)) {

        $whereSql = 'WHERE ' . implode(
            ' AND ',
            $where
        );
    }


    // =====================================================
    // CONSULTAR TOTAL
    // =====================================================

    try {

        $queryTotal = "
        SELECT COUNT(*)
        FROM veiculos
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

        $totalVeiculos = (int) $stmtTotal->fetchColumn();


        // =================================================
        // CALCULAR PAGINAÇÃO
        // =================================================

        $totalPaginas = max(
            1,
            (int) ceil(
                $totalVeiculos / $porPagina
            )
        );


        if ($paginaAtual > $totalPaginas) {

            $paginaAtual = $totalPaginas;
        }


        $offset = (
            $paginaAtual - 1
        ) * $porPagina;


        // =================================================
        // CONSULTAR VEÍCULOS
        // =================================================

        $query = "
        SELECT *
        FROM veiculos
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

        $veiculos = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    } catch (PDOException $e) {

        die('Erro ao consultar veículos: ' .
            $e->getMessage());
    }


    // =====================================================
    // MARCAS DISPONÍVEIS
    // =====================================================

    try {

        $stmtMarcas = $pdo->query("
        SELECT DISTINCT marca
        FROM veiculos
        WHERE marca IS NOT NULL
        AND marca <> ''
        ORDER BY marca ASC
    ");

        $marcasDisponiveis = $stmtMarcas->fetchAll(
            PDO::FETCH_COLUMN
        );
    } catch (PDOException $e) {

        $marcasDisponiveis = [];
    }
}
