<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function definirMensagem(string $tipo, string $texto): void
{
    // $tipo: 'sucesso' | 'erro'
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

function obterMensagem(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $mensagem = $_SESSION['flash'];
        unset($_SESSION['flash']); // exibe uma vez só
        return $mensagem;
    }
    return null;
}
