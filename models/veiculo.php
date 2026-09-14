<?php

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
        header("Location: ../src/veiculos.php");
        exit;
    }
} else {
    $id = $_GET["id"] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = :id");
        $stmt->execute(["id" => $id]);

        $veiculo = $stmt->fetch();
    }

    // SELECT
    $query = "SELECT * FROM veiculos";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $veiculosList = $stmt->fetchAll();

    $marcasDisponiveis = array_values(
        array_unique(
            array_column($veiculosList, 'marca')
        )
    );

    sort($marcasDisponiveis);
}
