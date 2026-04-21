<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($titulo ?? 'Gestion_POS') ?> — Gestion_POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; }

    :root {
      --sidebar-w: 232px;
      --topbar-h: 54px;
      --purple:   #7c3aed;
      --purple-dim: rgba(124,58,237,0.12);
      --border:   #e5e7eb;
      --radius-sm: 6px;
      --radius-md: 8px;
      --radius-lg: 12px;
    }

    body { margin:0; background:var(--bg); font-family:'DM Sans',sans-serif; color:var(--text-primary); }

    /* ─────────── Layout ─────────── */
    .app-layout { display:flex; min-height:100vh; }

    /* ─────────── Sidebar ─────────── */
    .sidebar {
      width: var(--sidebar-w);
      min-height: 100vh;
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0;
      z-index: 200;
      transition: transform 0.22s ease;
    }

    /* Logo */
    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 1.1rem 1.25rem 1.1rem;
      border-bottom: 1px solid var(--border);
      text-decoration: none;
      flex-shrink: 0;
    }
    .sidebar-logo-text {
      font-size: 13.5px;
      font-weight: 600;
      color: var(--text-primary);
      letter-spacing: -0.02em;
    }
    .sidebar-logo-sub {
      font-size: 10px;
      color: var(--text-tertiary);
      letter-spacing: 0.04em;
      font-weight: 400;
      display: block;
      margin-top: 1px;
    }

    /* Nav */
    .sidebar-nav {
      flex: 1;
      padding: 0.75rem 0.625rem;
      display: flex;
      flex-direction: column;
      gap: 1px;
      overflow-y: auto;
    }

    .nav-section {
      font-size: 9.5px;
      font-weight: 600;
      letter-spacing: 0.09em;
      text-transform: uppercase;
      color: var(--text-tertiary);
      padding: 0.9rem 0.625rem 0.3rem;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.5rem 0.65rem;
      border-radius: var(--radius-md);
      font-size: 13px;
      font-weight: 400;
      color: var(--text-secondary);
      text-decoration: none;
      transition: background 0.12s, color 0.12s;
      white-space: nowrap;
      position: relative;
    }
    .nav-item svg {
      width: 15px; height: 15px;
      stroke: currentColor; fill: none;
      stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round;
      flex-shrink: 0;
    }
    .nav-item:hover {
      background: var(--bg);
      color: var(--text-primary);
    }
    .nav-item.active {
      background: var(--purple-dim);
      color: var(--purple);
      font-weight: 500;
    }
    .nav-item.active svg { stroke: var(--purple); }

    .nav-badge {
      margin-left: auto;
      background: var(--purple-dim);
      color: var(--purple);
      font-size: 10px;
      font-family: 'DM Mono', monospace;
      padding: 1px 7px;
      border-radius: 20px;
      font-weight: 500;
    }

    /* Footer / user */
    .sidebar-footer {
      padding: 0.875rem 1rem;
      border-top: 1px solid var(--border);
      flex-shrink: 0;
    }
    .sidebar-user { display:flex; align-items:center; gap:0.6rem; }
    .user-avatar {
      width: 28px; height: 28px;
      border-radius: 50%;
      background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 600; color: #fff;
      flex-shrink: 0; letter-spacing: 0;
    }
    .user-info { flex:1; min-width:0; }
    .user-name {
      font-size: 12px; font-weight: 500;
      color: var(--text-primary);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .user-role { font-size: 10.5px; color: var(--text-tertiary); }
    .logout-btn {
      background: none; border: none;
      color: var(--text-tertiary); cursor: pointer;
      padding: 5px; border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      transition: color 0.15s, background 0.15s;
      text-decoration: none; flex-shrink: 0;
    }
    .logout-btn:hover { color: #dc2626; background: rgba(220,38,38,0.06); }
    .logout-btn svg {
      width: 14px; height: 14px;
      stroke: currentColor; fill: none;
      stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round;
    }

    /* ─────────── Main area ─────────── */
    .app-main {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    /* Topbar */
    .app-topbar {
      height: var(--topbar-h);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center;
      padding: 0 1.5rem; gap: 1rem;
      background: var(--surface);
      position: sticky; top: 0; z-index: 100;
    }
    .topbar-hamburger {
      display: none;
      background: none; border: none; cursor: pointer;
      color: var(--text-secondary); padding: 4px;
      border-radius: var(--radius-sm);
    }
    .topbar-hamburger svg {
      width: 18px; height: 18px;
      stroke: currentColor; fill: none; stroke-width: 1.75;
      stroke-linecap: round; stroke-linejoin: round;
    }
    .topbar-title {
      font-size: 13.5px; font-weight: 500;
      color: var(--text-primary);
    }
    .topbar-right {
      margin-left: auto;
      display: flex; align-items: center; gap: 0.65rem;
    }
    .topbar-date {
      font-size: 11.5px;
      font-family: 'DM Mono', monospace;
      color: var(--text-tertiary);
    }
    .status-dot {
      width: 6px; height: 6px; border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 0 2px rgba(34,197,94,0.22);
      flex-shrink: 0;
    }

    /* Content */
    .app-content { padding: 1.75rem; flex: 1; }

    /* Mobile overlay */
    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.35);
      z-index: 199;
    }

    /* ─────────── Flash messages ─────────── */
    .flash-msg {
      padding: 0.7rem 1rem;
      border-radius: var(--radius-md);
      margin-bottom: 1.25rem;
      font-size: 13px;
      display: flex; align-items: center; gap: 0.55rem;
    }
    .flash-msg svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2; flex-shrink:0; }
    .flash-success { background:rgba(34,197,94,0.09); color:#166534; border:1px solid rgba(34,197,94,0.22); }
    .flash-error   { background:rgba(248,113,113,0.09); color:#991b1b; border:1px solid rgba(248,113,113,0.22); }
    .flash-errors  {
      background:rgba(248,113,113,0.07); color:#991b1b;
      border:1px solid rgba(248,113,113,0.2);
      padding:0.7rem 1rem; border-radius:var(--radius-md);
      margin-bottom:1.25rem; font-size:13px;
    }
    .flash-errors ul { margin:0.3rem 0 0 1.1rem; padding:0; }
    .flash-errors li { margin:0.1rem 0; }

    /* ─────────── Panels ─────────── */
    .panel { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; }
    .panel-head {
      display:flex; align-items:center; justify-content:space-between;
      padding:0.875rem 1.25rem; border-bottom:1px solid var(--border);
    }
    .panel-title  { font-size:13px; font-weight:500; color:var(--text-primary); }
    .panel-action { font-size:12px; color:var(--text-tertiary); text-decoration:none; transition:color 0.15s; }
    .panel-action:hover { color:var(--purple); }
    .panel-body   { padding:1.25rem; }

    /* ─────────── Page headers ─────────── */
    .page-header { margin-bottom:1.5rem; }
    .page-header h1 { font-size:20px; font-weight:500; letter-spacing:-0.025em; color:var(--text-primary); margin:0 0 0.2rem; }
    .page-header p  { font-size:13px; color:var(--text-tertiary); margin:0; }
    .page-header-row {
      display:flex; align-items:center; justify-content:space-between;
      gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;
    }
    .page-header-row h1 { font-size:20px; font-weight:500; letter-spacing:-0.025em; color:var(--text-primary); margin:0; }

    /* ─────────── Buttons ─────────── */
    .btn-pill {
      display:inline-flex; align-items:center; gap:0.4rem;
      padding:0.38rem 0.95rem;
      background:var(--purple); color:#fff;
      border:none; border-radius:20px;
      font-size:12.5px; font-weight:500; font-family:'DM Sans',sans-serif;
      cursor:pointer; text-decoration:none;
      transition:opacity 0.15s, box-shadow 0.15s;
    }
    .btn-pill:hover { opacity:0.88; box-shadow:0 2px 8px rgba(124,58,237,0.3); }

    .btn-ghost {
      display:inline-flex; align-items:center; gap:0.35rem;
      padding:0.38rem 0.85rem;
      background:transparent; color:var(--text-secondary);
      border:1px solid var(--border); border-radius:var(--radius-md);
      font-size:12.5px; font-weight:500; font-family:'DM Sans',sans-serif;
      cursor:pointer; text-decoration:none;
      transition:background 0.12s, border-color 0.12s;
    }
    .btn-ghost:hover { background:var(--bg); border-color:#d1d5db; color:var(--text-primary); }

    .btn-primary {
      display:inline-flex; align-items:center; gap:0.4rem;
      padding:0.55rem 1.2rem;
      background:var(--purple); color:#fff;
      border:none; border-radius:var(--radius-md);
      font-size:13.5px; font-weight:500; font-family:'DM Sans',sans-serif;
      cursor:pointer; text-decoration:none;
      transition:opacity 0.15s, box-shadow 0.15s;
    }
    .btn-primary:hover { opacity:0.88; box-shadow:0 2px 8px rgba(124,58,237,0.3); }

    .btn-secondary {
      display:inline-flex; align-items:center; gap:0.4rem;
      padding:0.55rem 1.2rem;
      background:var(--bg); color:var(--text-secondary);
      border:1px solid var(--border); border-radius:var(--radius-md);
      font-size:13.5px; font-weight:500; font-family:'DM Sans',sans-serif;
      cursor:pointer; text-decoration:none;
      transition:background 0.12s;
    }
    .btn-secondary:hover { background:#f3f4f6; }

    .btn-danger {
      display:inline-flex; align-items:center; gap:0.4rem;
      padding:0.55rem 1.2rem;
      background:rgba(220,38,38,0.08); color:#dc2626;
      border:1px solid rgba(220,38,38,0.18); border-radius:var(--radius-md);
      font-size:13.5px; font-weight:500; font-family:'DM Sans',sans-serif;
      cursor:pointer; text-decoration:none;
      transition:background 0.12s;
    }
    .btn-danger:hover { background:rgba(220,38,38,0.14); }

    /* ─────────── Data table ─────────── */
    .data-table { width:100%; border-collapse:collapse; }
    .data-table th {
      font-size:10.5px; font-weight:500;
      letter-spacing:0.06em; text-transform:uppercase;
      color:var(--text-tertiary); text-align:left;
      padding:0.7rem 1rem; border-bottom:1px solid var(--border);
      background:var(--bg); white-space:nowrap;
    }
    .data-table td {
      font-size:13px; color:var(--text-secondary);
      padding:0.75rem 1rem; border-bottom:1px solid var(--border);
    }
    .data-table tr:last-child td { border-bottom:none; }
    .data-table tr:hover td { background:rgba(0,0,0,0.012); }
    .data-table td.mono { font-family:'DM Mono',monospace; font-size:12px; }
    .mono { font-family:'DM Mono',monospace; }

    /* ─────────── Badges ─────────── */
    .badge { display:inline-block; font-size:10.5px; padding:2px 8px; border-radius:20px; font-weight:500; white-space:nowrap; }
    .badge-violet { background:rgba(124,58,237,0.12); color:#6d28d9; }
    .badge-green  { background:rgba(34,197,94,0.11);  color:#15803d; }
    .badge-amber  { background:rgba(245,158,11,0.13); color:#92400e; }
    .badge-red    { background:rgba(239,68,68,0.12);  color:#b91c1c; }
    .badge-gray   { background:rgba(100,116,139,0.1); color:#475569; }
    .badge-blue   { background:rgba(59,130,246,0.11); color:#0369a1; }

    /* ─────────── Forms ─────────── */
    .form-card {
      background:var(--surface); border:1px solid var(--border);
      border-radius:var(--radius-lg); padding:1.5rem 1.75rem;
      max-width:680px;
    }
    .form-group { margin-bottom:1.15rem; }
    .form-label {
      display:block; margin-bottom:0.35rem;
      font-size:13px; font-weight:500; color:var(--text-primary);
    }
    .form-label .req { color:#f87171; margin-left:2px; }
    .form-control {
      width:100%; padding:0.55rem 0.75rem;
      border:1px solid var(--border); border-radius:var(--radius-md);
      font-size:13.5px; font-family:'DM Sans',sans-serif;
      color:var(--text-primary); background:var(--surface);
      transition:border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus { outline:none; border-color:var(--purple); box-shadow:0 0 0 3px rgba(124,58,237,0.1); }
    .form-control.is-error { border-color:#f87171; background:rgba(248,113,113,0.04); }
    textarea.form-control { resize:vertical; min-height:80px; }
    select.form-control { cursor:pointer; }
    .form-hint   { font-size:11.5px; color:var(--text-tertiary); margin-top:0.28rem; }
    .field-error { font-size:11.5px; color:#dc2626; margin-top:0.28rem; }
    .form-check {
      display:flex; align-items:center; gap:0.5rem;
      font-size:13.5px; color:var(--text-secondary); cursor:pointer;
    }
    .form-check input[type="checkbox"] { width:15px; height:15px; cursor:pointer; accent-color:var(--purple); }
    .form-actions { display:flex; gap:0.65rem; margin-top:1.5rem; flex-wrap:wrap; }

    /* ─────────── Action links ─────────── */
    .action-link {
      display:inline-flex; align-items:center; gap:0.3rem;
      font-size:12px; font-weight:500;
      text-decoration:none; padding:0.28rem 0.6rem;
      border-radius:var(--radius-sm);
      border:none; cursor:pointer; transition:background 0.12s;
      font-family:'DM Sans',sans-serif;
    }
    .action-link svg { width:12px; height:12px; stroke:currentColor; fill:none; stroke-width:2; }
    .action-edit   { background:rgba(59,130,246,0.09); color:#0369a1; }
    .action-edit:hover   { background:rgba(59,130,246,0.17); }
    .action-delete { background:rgba(220,38,38,0.09); color:#dc2626; }
    .action-delete:hover { background:rgba(220,38,38,0.17); }
    .action-view   { background:rgba(124,58,237,0.09); color:#7c3aed; }
    .action-view:hover   { background:rgba(124,58,237,0.17); }
    .action-close  { background:rgba(34,197,94,0.09); color:#15803d; }
    .action-close:hover  { background:rgba(34,197,94,0.17); }
    .icon-sm { width:13px; height:13px; stroke:currentColor; fill:none; stroke-width:2; }

    /* ─────────── Misc ─────────── */
    .empty-state { text-align:center; padding:3rem 2rem; color:var(--text-tertiary); }
    .empty-state p { font-size:13px; margin:0.5rem 0 1.25rem; }

    .info-box {
      background:rgba(124,58,237,0.05);
      border-left:3px solid var(--purple);
      padding:0.75rem 1rem;
      border-radius:0 var(--radius-md) var(--radius-md) 0;
      margin-bottom:1.25rem;
      font-size:13px; color:var(--text-secondary);
    }

    /* ─────────── Responsive ─────────── */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
      .sidebar.open { transform: translateX(0); }
      .sidebar-overlay.open { display: block; }
      .app-main { margin-left: 0; }
      .topbar-hamburger { display: flex; }
      .app-content { padding: 1.25rem; }
    }
  </style>
  <?= $this->renderSection('page_styles') ?>
</head>
<body>
<?php
  $seg      = service('uri')->getSegment(1) ?: 'dashboard';
  $userName = esc($user['name'] ?? session('nombre') ?? 'Usuario');
  $userRole = esc($user['role'] ?? session('rol_nombre') ?? '');
  $initials = strtoupper(mb_substr(strip_tags($userName), 0, 2));
?>
<div class="app-layout">

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
        <span class="sidebar-logo-sub">Panel de gestión</span>
      </div>
    </a>

    <nav class="sidebar-nav">

      <a href="/dashboard" class="nav-item <?= $seg === 'dashboard' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>

      <a href="/mesas" class="nav-item <?= $seg === 'mesas' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
        Mesas
      </a>

      <a href="/pedidos" class="nav-item <?= $seg === 'pedidos' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Pedidos
      </a>

      <div class="nav-section">Catálogo</div>

      <a href="/productos" class="nav-item <?= $seg === 'productos' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        Productos
      </a>

      <a href="/categorias" class="nav-item <?= $seg === 'categorias' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
        Categorías
      </a>

      <a href="/inventario" class="nav-item <?= $seg === 'inventario' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Inventario
      </a>

    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="user-avatar"><?= $initials ?></div>
        <div class="user-info">
          <div class="user-name"><?= $userName ?></div>
          <div class="user-role"><?= $userRole ?></div>
        </div>
        <a href="/auth/logout" class="logout-btn" title="Cerrar sesión">
          <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </a>
      </div>
    </div>
  </aside>

  <!-- overlay móvil -->
  <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

  <!-- ─── Main ─── -->
  <main class="app-main">

    <div class="app-topbar">
      <button class="topbar-hamburger" onclick="openSidebar()" aria-label="Abrir menú">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <span class="topbar-title"><?= esc($titulo ?? '') ?></span>
      <div class="topbar-right">
        <div class="status-dot"></div>
        <span class="topbar-date"><?= date('d/m/Y · H:i') ?></span>
        <?= $this->renderSection('topbar_right') ?>
      </div>
    </div>

    <div class="app-content">

      <?php if ($msg = session()->getFlashdata('success')): ?>
        <div class="flash-msg flash-success">
          <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          <?= esc($msg) ?>
        </div>
      <?php endif; ?>

      <?php if ($msg = session()->getFlashdata('error')): ?>
        <div class="flash-msg flash-error">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <?= esc($msg) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errs = session()->getFlashdata('errors'))): ?>
        <div class="flash-errors">
          <strong>Corregí los siguientes errores:</strong>
          <ul>
            <?php foreach ((array)$errs as $e): ?>
              <li><?= esc($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?= $this->renderSection('content') ?>

    </div>
  </main>

</div>

<script>
function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebar-overlay').classList.add('open');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('open');
}
</script>
</body>
</html>
