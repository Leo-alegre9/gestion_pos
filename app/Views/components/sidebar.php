<?php
/**
 * Componente de Sidebar Unificado
 * 
 * Variables disponibles:
 * - $currentSegment: Segmento actual de la URL (ej: 'productos', 'mesas', etc.)
 * - $user: Información del usuario (name, role)
 */

// Obtener segmento actual
$seg = $currentSegment ?? service('uri')->getSegment(1) ?? 'dashboard';
$userName = esc($user['name'] ?? session('nombre') ?? 'Usuario');
$userRole = esc($user['role'] ?? session('rol_nombre') ?? '');
$initials = strtoupper(mb_substr(strip_tags($userName), 0, 2));

// Items de navegación con sus atributos
$navItems = [
  [
    'icon' => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    'label' => 'Dashboard',
    'href' => '/dashboard',
    'segment' => 'dashboard'
  ],
  [
    'icon' => '<svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>',
    'label' => 'Mesas',
    'href' => '/mesas',
    'segment' => 'mesas'
  ],
  [
    'icon' => '<svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
    'label' => 'Pedidos',
    'href' => '/pedidos',
    'segment' => 'pedidos'
  ],
];

$catalogItems = [
  [
    'icon' => '<svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
    'label' => 'Productos',
    'href' => '/productos',
    'segment' => 'productos'
  ],
  [
    'icon' => '<svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>',
    'label' => 'Categorías',
    'href' => '/categorias',
    'segment' => 'categorias'
  ],
  [
    'icon' => '<svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',
    'label' => 'Inventario',
    'href' => '/inventario',
    'segment' => 'inventario'
  ],
];

$reportItems = [
  [
    'icon' => '<svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
    'label' => 'Facturación',
    'href' => '/facturacion',
    'segment' => 'facturacion'
  ],
];
?>

<!-- ─── Sidebar ─── -->
<aside class="sidebar" id="sidebar">
  <a href="/dashboard" class="sidebar-logo">
    <svg width="26" height="26" viewBox="0 0 40 40" fill="none">
      <defs>
        <radialGradient id="sg" cx="35%" cy="35%">
          <stop offset="0%" stop-color="#9f7aea"/>
          <stop offset="50%" stop-color="#7c3aed"/>
          <stop offset="100%" stop-color="#5b21b6"/>
        </radialGradient>
        <radialGradient id="ss" cx="35%" cy="35%">
          <stop offset="0%" stop-color="#ffffff" stop-opacity="0.6"/>
          <stop offset="50%" stop-color="#ffffff" stop-opacity="0"/>
        </radialGradient>
      </defs>
      <circle cx="20" cy="20" r="18" fill="url(#sg)"/>
      <circle cx="14" cy="12" r="8" fill="url(#ss)"/>
      <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3"/>
    </svg>
    <div>
      <div class="sidebar-logo-text">Gestion_POS</div>
      <span class="sidebar-logo-sub">Sistema de gestión</span>
    </div>
  </a>

  <!-- Navegación -->
  <nav class="sidebar-nav">
    <!-- Sección Principal -->
    <?php foreach ($navItems as $item): ?>
      <a href="<?= $item['href'] ?>" class="nav-item <?= $seg === $item['segment'] ? 'active' : '' ?>" title="<?= $item['label'] ?>">
        <?= $item['icon'] ?>
        <span><?= $item['label'] ?></span>
      </a>
    <?php endforeach; ?>

    <!-- Sección Catálogo -->
    <div class="nav-section">Catálogo</div>
    <?php foreach ($catalogItems as $item): ?>
      <a href="<?= $item['href'] ?>" class="nav-item <?= $seg === $item['segment'] ? 'active' : '' ?>" title="<?= $item['label'] ?>">
        <?= $item['icon'] ?>
        <span><?= $item['label'] ?></span>
      </a>
    <?php endforeach; ?>

    <!-- Sección Reportes -->
    <div class="nav-section">Reportes</div>
    <?php foreach ($reportItems as $item): ?>
      <a href="<?= $item['href'] ?>" class="nav-item <?= $seg === $item['segment'] ? 'active' : '' ?>" title="<?= $item['label'] ?>">
        <?= $item['icon'] ?>
        <span><?= $item['label'] ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- Footer del Sidebar -->
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="user-avatar"><?= $initials ?></div>
      <div class="user-info">
        <div class="user-name"><?= $userName ?></div>
        <div class="user-role"><?= $userRole ?: 'Usuario' ?></div>
      </div>
      <a href="/auth/logout" class="logout-btn" title="Cerrar sesión">
        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </a>
    </div>
  </div>
</aside>

<!-- Overlay móvil -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>
