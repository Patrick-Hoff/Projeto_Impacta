<?php
/**
 * Header / layout shell
 * Variáveis esperadas antes do include:
 *   $pageTitle  (string) - título da página, ex: "Veículos"
 *   $activePage (string) - chave do menu ativo: 'dashboard' | 'veiculos' | 'clientes' | 'vendas'
 */
$pageTitle  = $pageTitle ?? 'Dashboard';
$activePage = $activePage ?? 'dashboard';

$navItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'href' => 'index.php'],
    ['key' => 'veiculos',  'label' => 'Veículos',  'icon' => 'bi-car-front-fill', 'href' => 'veiculos.php'],
    ['key' => 'clientes',  'label' => 'Clientes',  'icon' => 'bi-people-fill', 'href' => '#', 'disabled' => true],
    ['key' => 'vendas',    'label' => 'Vendas',    'icon' => 'bi-receipt', 'href' => '#', 'disabled' => true],
];

function renderNavItems(array $items, string $active): void
{
    foreach ($items as $item) {
        $isActive   = $item['key'] === $active;
        $isDisabled = !empty($item['disabled']);
        $classes    = 'nav-link d-flex align-items-center gap-2';
        $classes   .= $isActive ? ' active' : '';
        $classes   .= $isDisabled ? ' disabled' : '';
        $href       = $isDisabled ? '#' : $item['href'];
        $extra      = $isDisabled ? ' tabindex="-1" aria-disabled="true"' : '';
        echo '<li class="nav-item">';
        echo '<a class="' . $classes . '" href="' . htmlspecialchars($href) . '"' . $extra . '>';
        echo '<i class="bi ' . $item['icon'] . '"></i>';
        echo '<span>' . htmlspecialchars($item['label']) . '</span>';
        if ($isDisabled) {
            echo '<span class="badge text-bg-secondary ms-auto small">em breve</span>';
        }
        echo '</a></li>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle) ?> · Auto Dealer</title>

    <!-- Evita flash de tema errado ao carregar -->
    <script>
        (function () {
            var saved = localStorage.getItem('auto-dealer-theme');
            if (saved === 'light' || saved === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', saved);
            }
        })();
    </script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Tipografia -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">

    <!-- Sidebar offcanvas (mobile) -->
    <div class="offcanvas offcanvas-start text-bg-sidebar" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
        <div class="offcanvas-header">
            <a href="index.php" class="brand" id="sidebarOffcanvasLabel">
                <i class="bi bi-car-front-fill"></i>
                <span>Auto Dealer</span>
            </a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <ul class="nav nav-pills flex-column gap-1">
                <?php renderNavItems($navItems, $activePage); ?>
            </ul>
            <div class="mt-auto pt-3 border-top border-secondary-subtle">
                <span class="text-muted small">Loja Auto Dealer &middot; painel interno</span>
            </div>
        </div>
    </div>

    <!-- Sidebar fixa (desktop) -->
    <aside class="sidebar d-none d-lg-flex flex-column">
        <a href="index.php" class="brand">
            <i class="bi bi-car-front-fill"></i>
            <span>Auto Dealer</span>
        </a>
        <ul class="nav nav-pills flex-column gap-1 flex-grow-1">
            <?php renderNavItems($navItems, $activePage); ?>
        </ul>
        <div class="pt-3 border-top border-secondary-subtle">
            <span class="text-muted small">Loja Auto Dealer &middot; painel interno</span>
        </div>
    </aside>

    <div class="main-area">
        <!-- Topbar -->
        <nav class="topbar navbar">
            <div class="container-fluid gap-2 px-3 px-lg-4">
                <button class="btn btn-icon d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Abrir menu">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <h1 class="page-title mb-0 d-none d-sm-block"><?= htmlspecialchars($pageTitle) ?></h1>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <form class="d-none d-md-block" role="search" onsubmit="return false;">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control border-start-0 ps-0" placeholder="Buscar veículo, cliente...">
                        </div>
                    </form>

                    <button class="btn btn-icon" type="button" id="themeToggle" aria-label="Alternar tema" title="Alternar tema">
                        <i class="bi bi-moon-stars-fill" id="themeIconDark"></i>
                        <i class="bi bi-sun-fill d-none" id="themeIconLight"></i>
                    </button>

                    <div class="avatar-badge" title="Usuário">AD</div>
                </div>
            </div>
        </nav>

        <main class="content-area">