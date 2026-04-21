<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Gestion_POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css">
  <style>
    /* ── Layout base ── */
    .dash-layout {
      display: flex;
      min-height: 100vh;
    }

    /* ── Sidebar ── */
    .sidebar {
      width: 240px;
      min-height: 100vh;
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      padding: 1.5rem 0;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 100;
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0 1.5rem 1.75rem;
      border-bottom: 1px solid var(--border);
      text-decoration: none;
    }
    .sidebar-logo .logo-text {
      font-size: 14px;
      font-weight: 500;
      color: var(--text-primary);
      letter-spacing: -0.01em;
    }

    .sidebar-nav {
      flex: 1;
      padding: 1.25rem 0.75rem;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .nav-section-label {
      font-size: 10px;
      font-weight: 500;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--text-tertiary);
      padding: 0.75rem 0.75rem 0.35rem;
      margin-top: 0.5rem;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.55rem 0.75rem;
      border-radius: var(--radius-md);
      font-size: 13.5px;
      color: var(--text-secondary);
      text-decoration: none;
      transition: background 0.15s, color 0.15s;
      cursor: pointer;
    }
    .nav-item svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.75;
      stroke-linecap: round;
      stroke-linejoin: round;
      flex-shrink: 0;
      opacity: 0.7;
    }
    .nav-item:hover {
      background: var(--bg);
      color: var(--text-primary);
    }
    .nav-item:hover svg { opacity: 1; }
    .nav-item.active {
      background: rgba(124, 58, 237, 0.15);
      color: #a78bfa;
    }
    .nav-item.active svg { opacity: 1; }

    .nav-badge {
      margin-left: auto;
      background: rgba(124, 58, 237, 0.25);
      color: #a78bfa;
      font-size: 10px;
      font-family: 'DM Mono', monospace;
      padding: 1px 6px;
      border-radius: 20px;
    }

    .sidebar-footer {
      padding: 1rem 1.25rem;
      border-top: 1px solid var(--border);
    }
    .sidebar-user {
      display: flex;
      align-items: center;
      gap: 0.65rem;
    }
    .user-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: linear-gradient(135deg, #7c3aed, #9f7aea);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 500;
      color: #fff;
      flex-shrink: 0;
    }
    .user-info { flex: 1; min-width: 0; }
    .user-name {
      font-size: 12.5px;
      font-weight: 500;
      color: var(--text-primary);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .user-role {
      font-size: 11px;
      color: var(--text-tertiary);
    }
    .logout-btn {
      background: none;
      border: none;
      color: var(--text-tertiary);
      cursor: pointer;
      padding: 4px;
      border-radius: 6px;
      display: flex;
      transition: color 0.15s;
      text-decoration: none;
    }
    .logout-btn:hover { color: var(--text-secondary); }
    .logout-btn svg {
      width: 15px;
      height: 15px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.75;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    /* ── Main content ── */
    .dash-main {
      margin-left: 240px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .dash-topbar {
      height: 56px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 1.75rem;
      gap: 1rem;
      background: var(--surface);
      position: sticky;
      top: 0;
      z-index: 50;
    }
    .topbar-title {
      font-size: 14px;
      font-weight: 500;
      color: var(--text-primary);
    }
    .topbar-right {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .topbar-date {
      font-size: 12px;
      font-family: 'DM Mono', monospace;
      color: var(--text-tertiary);
    }
    .status-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 0 2px rgba(34,197,94,0.2);
    }

    .dash-content {
      padding: 1.75rem;
      flex: 1;
    }

    .page-header {
      margin-bottom: 1.5rem;
    }
    .page-header h1 {
      font-size: 20px;
      font-weight: 500;
      letter-spacing: -0.02em;
      color: var(--text-primary);
      margin: 0 0 0.25rem;
    }
    .page-header p {
      font-size: 13px;
      color: var(--text-tertiary);
      margin: 0;
    }

    /* ── KPI Cards ── */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .kpi-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      position: relative;
      overflow: hidden;
      transition: border-color 0.2s;
    }
    .kpi-card:hover { border-color: rgba(124,58,237,0.4); }

    .kpi-label {
      font-size: 11.5px;
      color: var(--text-tertiary);
      letter-spacing: 0.01em;
    }
    .kpi-value {
      font-size: 26px;
      font-weight: 500;
      letter-spacing: -0.03em;
      color: var(--text-primary);
      line-height: 1;
    }
    .kpi-sub {
      font-size: 11.5px;
      color: var(--text-tertiary);
      display: flex;
      align-items: center;
      gap: 0.3rem;
    }
    .kpi-sub.up { color: #22c55e; }
    .kpi-sub.down { color: #f87171; }
    .kpi-icon {
      position: absolute;
      right: 1.25rem;
      top: 1.25rem;
      width: 32px;
      height: 32px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .kpi-icon svg {
      width: 15px;
      height: 15px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.75;
      stroke-linecap: round;
      stroke-linejoin: round;
    }
    .kpi-icon.violet { background: rgba(124,58,237,0.15); color: #a78bfa; }
    .kpi-icon.green  { background: rgba(34,197,94,0.12);  color: #4ade80; }
    .kpi-icon.amber  { background: rgba(251,191,36,0.12); color: #fbbf24; }
    .kpi-icon.red    { background: rgba(248,113,113,0.12); color: #f87171; }

    /* ── Grid 2 cols ── */
    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .grid-3-1 {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    /* ── Panel base ── */
    .panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      overflow: hidden;
    }
    .panel-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--border);
    }
    .panel-title {
      font-size: 13px;
      font-weight: 500;
      color: var(--text-primary);
    }
    .panel-action {
      font-size: 12px;
      color: var(--text-tertiary);
      text-decoration: none;
      transition: color 0.15s;
    }
    .panel-action:hover { color: #a78bfa; }
    .panel-body { padding: 1.25rem; }

    /* ── Tables grid ── */
    .tables-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 0.6rem;
    }
    .table-tile {
      border-radius: var(--radius-md);
      padding: 0.75rem;
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
      border: 1px solid transparent;
      cursor: pointer;
      transition: transform 0.15s, border-color 0.15s;
      text-decoration: none;
    }
    .table-tile:hover {
      transform: translateY(-2px);
      border-color: rgba(124,58,237,0.3);
    }
    .table-tile .tt-num {
      font-family: 'DM Mono', monospace;
      font-size: 18px;
      font-weight: 500;
      line-height: 1;
    }
    .table-tile .tt-label { font-size: 10.5px; }
    .table-tile .tt-amount { font-size: 11px; font-family: 'DM Mono', monospace; margin-top: auto; }

    .tt-libre    { background: rgba(34,197,94,0.08);  color: #4ade80; }
    .tt-ocupada  { background: rgba(124,58,237,0.12); color: #a78bfa; }
    .tt-reservada{ background: rgba(251,191,36,0.1);  color: #fbbf24; }
    .tt-inactiva { background: rgba(100,116,139,0.1); color: #64748b; }

    .tt-libre .tt-label    { color: rgba(74,222,128,0.7); }
    .tt-ocupada .tt-label  { color: rgba(167,139,250,0.7); }
    .tt-reservada .tt-label{ color: rgba(251,191,36,0.7); }
    .tt-inactiva .tt-label { color: rgba(100,116,139,0.6); }

    /* ── Pedidos recientes ── */
    .orders-table {
      width: 100%;
      border-collapse: collapse;
    }
    .orders-table th {
      font-size: 10.5px;
      font-weight: 500;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--text-tertiary);
      text-align: left;
      padding: 0 0.75rem 0.75rem;
    }
    .orders-table td {
      font-size: 13px;
      color: var(--text-secondary);
      padding: 0.65rem 0.75rem;
      border-top: 1px solid var(--border);
    }
    .orders-table tr:hover td { background: rgba(255,255,255,0.02); }

    .order-id {
      font-family: 'DM Mono', monospace;
      font-size: 12px;
      color: var(--text-tertiary);
    }
    .order-amount {
      font-family: 'DM Mono', monospace;
      font-weight: 500;
      color: var(--text-primary);
    }

    .badge-pill {
      display: inline-block;
      font-size: 10.5px;
      padding: 2px 8px;
      border-radius: 20px;
      font-weight: 500;
    }
    .badge-open     { background: rgba(124,58,237,0.2); color: #a78bfa; }
    .badge-paid     { background: rgba(34,197,94,0.15); color: #4ade80; }
    .badge-pending  { background: rgba(251,191,36,0.15); color: #fbbf24; }
    .badge-canceled { background: rgba(248,113,113,0.15); color: #f87171; }

    /* ── Productos top ── */
    .product-row {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.6rem 0;
      border-top: 1px solid var(--border);
    }
    .product-row:first-child { border-top: none; }
    .product-rank {
      font-family: 'DM Mono', monospace;
      font-size: 11px;
      color: var(--text-tertiary);
      width: 18px;
      flex-shrink: 0;
      text-align: right;
    }
    .product-name {
      flex: 1;
      font-size: 13px;
      color: var(--text-secondary);
    }
    .product-bar-wrap {
      width: 80px;
      height: 4px;
      background: var(--border);
      border-radius: 2px;
      overflow: hidden;
    }
    .product-bar {
      height: 100%;
      background: linear-gradient(90deg, #7c3aed, #9f7aea);
      border-radius: 2px;
    }
    .product-qty {
      font-family: 'DM Mono', monospace;
      font-size: 12px;
      color: var(--text-tertiary);
      width: 28px;
      text-align: right;
    }

    /* ── Stock alerts ── */
    .stock-row {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 0;
      border-top: 1px solid var(--border);
    }
    .stock-row:first-child { border-top: none; }
    .stock-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .stock-dot.critical { background: #f87171; }
    .stock-dot.low      { background: #fbbf24; }
    .stock-name { flex: 1; font-size: 13px; color: var(--text-secondary); }
    .stock-qty {
      font-family: 'DM Mono', monospace;
      font-size: 12px;
    }
    .stock-qty.critical { color: #f87171; }
    .stock-qty.low      { color: #fbbf24; }

    /* ── Mini chart bars ── */
    .mini-chart {
      display: flex;
      align-items: flex-end;
      gap: 3px;
      height: 40px;
      padding-top: 0.25rem;
    }
    .mini-bar {
      flex: 1;
      background: rgba(124,58,237,0.3);
      border-radius: 2px 2px 0 0;
      transition: background 0.15s;
    }
    .mini-bar:hover { background: rgba(124,58,237,0.7); }
    .mini-bar.today { background: rgba(124,58,237,0.8); }
  </style>
</head>
<body>
<div class="dash-layout">

  <!-- ── Sidebar ── -->
  <aside class="sidebar">
    <a href="/" class="sidebar-logo">
      <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <radialGradient id="sgDash" cx="35%" cy="35%">
            <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1" />
            <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1" />
          </radialGradient>
          <radialGradient id="ssDash" cx="35%" cy="35%">
            <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6" />
            <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0" />
          </radialGradient>
        </defs>
        <circle cx="20" cy="20" r="18" fill="url(#sgDash)" />
        <circle cx="14" cy="12" r="8" fill="url(#ssDash)" />
        <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3" />
      </svg>
      <span class="logo-text">Gestion_POS</span>
    </a>

    <nav class="sidebar-nav">
      <a href="/dashboard" class="nav-item active">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>
      <a href="/mesas" class="nav-item">
        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
        Mesas
        <span class="nav-badge"><?= $stats['mesas_total'] ?? 8 ?></span>
      </a>
      <a href="/pedidos" class="nav-item">
        <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Pedidos
        <span class="nav-badge">3</span>
      </a>
      <a href="/productos" class="nav-item">
        <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        Productos
      </a>
      <a href="/inventario" class="nav-item">
        <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Inventario
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?></div>
        <div class="user-info">
          <div class="user-name"><?= esc($user['name'] ?? 'Administrador') ?></div>
          <div class="user-role"><?= esc($user['role'] ?? 'Admin') ?></div>
        </div>
        <a href="/auth/logout" class="logout-btn" title="Cerrar sesión">
          <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </a>
      </div>
    </div>
  </aside>

  <!-- ── Main ── -->
  <main class="dash-main">

    <!-- Topbar -->
    <div class="dash-topbar">
      <span class="topbar-title">Dashboard</span>
      <div class="topbar-right">
        <div class="status-dot"></div>
        <span class="topbar-date"><?= date('d/m/Y · H:i') ?></span>
        <a href="/pedidos/crear" class="btn-pill" style="font-size:12px;padding:0.4rem 1rem;">+ Nuevo pedido</a>
      </div>
    </div>

    <!-- Content -->
    <div class="dash-content">

      <div class="page-header">
        <h1>Buenas <?= date('H') < 12 ? 'días' : (date('H') < 18 ? 'tardes' : 'noches') ?>, <?= esc(explode(' ', $user['name'] ?? 'Admin')[0]) ?> 👋</h1>
        <p>Resumen operativo del turno · <?= date('l, d \d\e F') ?></p>
      </div>

      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-icon violet">
            <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
          </div>
          <div class="kpi-label">Ventas hoy</div>
          <div class="kpi-value">$<?= number_format($stats['ventas_hoy'] ?? 124500, 0, ',', '.') ?></div>
          <div class="kpi-sub up">↑ 12% vs ayer</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon green">
            <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <div class="kpi-label">Pedidos hoy</div>
          <div class="kpi-value"><?= $stats['pedidos_hoy'] ?? 37 ?></div>
          <div class="kpi-sub up">↑ 5 vs ayer</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon amber">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </div>
          <div class="kpi-label">Mesas ocupadas</div>
          <div class="kpi-value"><?= $stats['mesas_ocupadas'] ?? 4 ?><span style="font-size:14px;color:var(--text-tertiary);font-weight:400"> / <?= $stats['mesas_total'] ?? 8 ?></span></div>
          <div class="kpi-sub">50% de ocupación</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon red">
            <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          </div>
          <div class="kpi-label">Alertas de stock</div>
          <div class="kpi-value"><?= $stats['alertas_stock'] ?? 3 ?></div>
          <div class="kpi-sub down">Productos bajos</div>
        </div>
      </div>

      <!-- Mesas + Pedidos recientes -->
      <div class="grid-3-1" style="margin-bottom:1rem;">
        <!-- Pedidos recientes -->
        <div class="panel">
          <div class="panel-head">
            <span class="panel-title">Pedidos recientes</span>
            <a href="/pedidos" class="panel-action">Ver todos →</a>
          </div>
          <div class="panel-body" style="padding:0;">
            <table class="orders-table">
              <thead>
                <tr>
                  <th style="padding-left:1.25rem">#</th>
                  <th>Mesa / Tipo</th>
                  <th>Productos</th>
                  <th>Estado</th>
                  <th style="text-align:right;padding-right:1.25rem">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_orders ?? [] as $order): ?>
                <tr>
                  <td class="order-id" style="padding-left:1.25rem">#<?= esc($order['id']) ?></td>
                  <td><?= esc($order['mesa_label']) ?></td>
                  <td style="color:var(--text-tertiary)"><?= esc($order['items_count']) ?> ítem<?= $order['items_count'] != 1 ? 's' : '' ?></td>
                  <td><span class="badge-pill badge-<?= esc($order['status_class']) ?>"><?= esc($order['status_label']) ?></span></td>
                  <td class="order-amount" style="text-align:right;padding-right:1.25rem">$<?= number_format($order['total'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Ventas semana (mini chart) -->
        <div class="panel">
          <div class="panel-head">
            <span class="panel-title">Esta semana</span>
          </div>
          <div class="panel-body">
            <div class="mini-chart">
              <?php
                $week_sales = $stats['week_sales'] ?? [85000,110000,92000,135000,78000,124500,0];
                $max = max($week_sales) ?: 1;
                $days = ['L','M','X','J','V','S','D'];
                foreach($week_sales as $i => $val):
                  $h = round(($val/$max)*100);
                  $isToday = $i === 5;
              ?>
              <div class="mini-bar <?= $isToday ? 'today' : '' ?>" style="height:<?= $h ?>%" title="<?= $days[$i] ?>: $<?= number_format($val,0,',','.') ?>"></div>
              <?php endforeach; ?>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:0.4rem;">
              <?php foreach($days as $d): ?>
              <span style="font-size:9.5px;color:var(--text-tertiary);font-family:'DM Mono',monospace;flex:1;text-align:center;"><?= $d ?></span>
              <?php endforeach; ?>
            </div>
            <div style="margin-top:1rem;padding-top:0.75rem;border-top:1px solid var(--border);">
              <div style="font-size:11px;color:var(--text-tertiary)">Total semana</div>
              <div style="font-size:18px;font-weight:500;letter-spacing:-0.02em;color:var(--text-primary);margin-top:0.15rem;">
                $<?= number_format(array_sum($week_sales), 0, ',', '.') ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mesas + Top productos -->
      <div class="grid-3-1" style="margin-bottom:1rem;">
        <!-- Estado mesas -->
        <div class="panel">
          <div class="panel-head">
            <span class="panel-title">Estado de mesas</span>
            <a href="/mesas" class="panel-action">Gestionar →</a>
          </div>
          <div class="panel-body">
            <div class="tables-grid">
              <?php foreach ($tables ?? [] as $table): ?>
              <a href="<?= base_url('/mesas') ?>" class="table-tile tt-<?= esc($table['status']) ?>">
                <span class="tt-num"><?= str_pad($table['number'], 2, '0', STR_PAD_LEFT) ?></span>
                <span class="tt-label"><?= ucfirst(esc($table['status'])) ?></span>
                <?php if (!empty($table['amount'])): ?>
                  <span class="tt-amount">$<?= number_format($table['amount'], 0, ',', '.') ?></span>
                <?php endif; ?>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Top productos -->
        <div class="panel">
          <div class="panel-head">
            <span class="panel-title">Más vendidos hoy</span>
          </div>
          <div class="panel-body" style="padding-top:0.75rem;">
            <?php
              $top_products = $stats['top_products'] ?? [];
              $max_qty = !empty($top_products) ? max(array_column($top_products,'qty')) : 1;
              foreach($top_products as $i => $p):
            ?>
            <div class="product-row">
              <span class="product-rank"><?= $i+1 ?></span>
              <span class="product-name"><?= esc($p['name']) ?></span>
              <div class="product-bar-wrap">
                <div class="product-bar" style="width:<?= round(($p['qty']/$max_qty)*100) ?>%"></div>
              </div>
              <span class="product-qty"><?= $p['qty'] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Stock bajo -->
      <div class="panel" style="margin-bottom:1rem;">
        <div class="panel-head">
          <span class="panel-title">⚠️ Alertas de inventario</span>
          <a href="/stock" class="panel-action">Ver inventario →</a>
        </div>
        <div class="panel-body" style="padding-top:0.75rem;">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0 2rem;">
            <?php foreach ($stock_alerts ?? [] as $item): ?>
            <div class="stock-row">
              <div class="stock-dot <?= esc($item['level']) ?>"></div>
              <span class="stock-name"><?= esc($item['name']) ?></span>
              <span class="stock-qty <?= esc($item['level']) ?>"><?= $item['qty'] ?> u.</span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>
</body>
</html>