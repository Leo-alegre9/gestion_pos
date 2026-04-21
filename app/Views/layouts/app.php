<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($titulo ?? 'Gestion_POS') ?> — Gestion_POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; }

    :root {
      --border: #e5e7eb;
      --radius-md: 8px;
      --radius-lg: 12px;
    }

    body { margin: 0; background: var(--bg); font-family: 'DM Sans', sans-serif; color: var(--text-primary); }

    /* ── Layout base ── */
    .dash-layout { display: flex; min-height: 100vh; }

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
      top: 0; left: 0;
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
      overflow-y: auto;
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
    }
    .nav-item svg {
      width: 16px; height: 16px;
      stroke: currentColor; fill: none;
      stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round;
      flex-shrink: 0; opacity: 0.7;
    }
    .nav-item:hover { background: var(--bg); color: var(--text-primary); }
    .nav-item:hover svg { opacity: 1; }
    .nav-item.active { background: rgba(124,58,237,0.15); color: #a78bfa; }
    .nav-item.active svg { opacity: 1; }

    .nav-badge {
      margin-left: auto;
      background: rgba(124,58,237,0.25);
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
    .sidebar-user { display: flex; align-items: center; gap: 0.65rem; }
    .user-avatar {
      width: 30px; height: 30px; border-radius: 50%;
      background: linear-gradient(135deg,#7c3aed,#9f7aea);
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 500; color: #fff; flex-shrink: 0;
    }
    .user-info { flex: 1; min-width: 0; }
    .user-name {
      font-size: 12.5px; font-weight: 500; color: var(--text-primary);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .user-role { font-size: 11px; color: var(--text-tertiary); }
    .logout-btn {
      background: none; border: none;
      color: var(--text-tertiary); cursor: pointer;
      padding: 4px; border-radius: 6px;
      display: flex; transition: color 0.15s; text-decoration: none;
    }
    .logout-btn:hover { color: var(--text-secondary); }
    .logout-btn svg {
      width: 15px; height: 15px;
      stroke: currentColor; fill: none;
      stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round;
    }

    /* ── Main ── */
    .dash-main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; }

    .dash-topbar {
      height: 56px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center;
      padding: 0 1.75rem; gap: 1rem;
      background: var(--surface);
      position: sticky; top: 0; z-index: 50;
    }
    .topbar-title { font-size: 14px; font-weight: 500; color: var(--text-primary); }
    .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 0.75rem; }
    .topbar-date {
      font-size: 12px;
      font-family: 'DM Mono', monospace;
      color: var(--text-tertiary);
    }
    .status-dot {
      width: 6px; height: 6px; border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 0 2px rgba(34,197,94,0.2);
    }

    .dash-content { padding: 1.75rem; flex: 1; }

    /* ── Flash messages ── */
    .flash-msg {
      padding: 0.8rem 1rem;
      border-radius: var(--radius-md);
      margin-bottom: 1.25rem;
      font-size: 13.5px;
      display: flex; align-items: center; gap: 0.6rem;
    }
    .flash-msg svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }
    .flash-success { background: rgba(34,197,94,0.1); color: #166534; border: 1px solid rgba(34,197,94,0.2); }
    .flash-error   { background: rgba(248,113,113,0.1); color: #991b1b; border: 1px solid rgba(248,113,113,0.2); }
    .flash-errors  { background: rgba(248,113,113,0.08); color: #991b1b; border: 1px solid rgba(248,113,113,0.2); padding: 0.8rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 13.5px; }
    .flash-errors ul { margin: 0.3rem 0 0 1rem; padding: 0; }
    .flash-errors li { margin: 0.15rem 0; }

    /* ── Panels ── */
    .panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      overflow: hidden;
    }
    .panel-head {
      display: flex; align-items: center; justify-content: space-between;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--border);
    }
    .panel-title { font-size: 13px; font-weight: 500; color: var(--text-primary); }
    .panel-action { font-size: 12px; color: var(--text-tertiary); text-decoration: none; transition: color 0.15s; }
    .panel-action:hover { color: #a78bfa; }
    .panel-body { padding: 1.25rem; }

    /* ── Page header ── */
    .page-header { margin-bottom: 1.5rem; }
    .page-header h1 {
      font-size: 20px; font-weight: 500;
      letter-spacing: -0.02em; color: var(--text-primary);
      margin: 0 0 0.25rem;
    }
    .page-header p { font-size: 13px; color: var(--text-tertiary); margin: 0; }
    .page-header-row {
      display: flex; align-items: center;
      justify-content: space-between; gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }
    .page-header-row h1 {
      font-size: 20px; font-weight: 500;
      letter-spacing: -0.02em; color: var(--text-primary); margin: 0;
    }

    /* ── Buttons ── */
    .btn-pill {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.4rem 1rem;
      background: var(--purple, #7c3aed); color: #fff;
      border: none; border-radius: 20px;
      font-size: 12.5px; font-weight: 500;
      cursor: pointer; text-decoration: none;
      transition: opacity 0.15s, transform 0.15s;
    }
    .btn-pill:hover { opacity: 0.88; transform: translateY(-1px); }

    .btn-ghost {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.4rem 0.9rem;
      background: var(--border); color: var(--text-secondary);
      border: none; border-radius: var(--radius-md);
      font-size: 12.5px; font-weight: 500;
      cursor: pointer; text-decoration: none;
      transition: background 0.15s;
    }
    .btn-ghost:hover { background: #d1d5db; }

    /* ── Data table ── */
    .data-table {
      width: 100%; border-collapse: collapse;
    }
    .data-table th {
      font-size: 10.5px; font-weight: 500;
      letter-spacing: 0.06em; text-transform: uppercase;
      color: var(--text-tertiary); text-align: left;
      padding: 0.75rem 1rem; border-bottom: 1px solid var(--border);
      background: var(--bg);
    }
    .data-table td {
      font-size: 13px; color: var(--text-secondary);
      padding: 0.8rem 1rem; border-bottom: 1px solid var(--border);
    }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: rgba(0,0,0,0.01); }
    .data-table td.mono { font-family: 'DM Mono', monospace; font-size: 12px; }

    /* ── Badges ── */
    .badge {
      display: inline-block; font-size: 10.5px; padding: 2px 8px;
      border-radius: 20px; font-weight: 500;
    }
    .badge-violet  { background: rgba(124,58,237,0.15); color: #7c3aed; }
    .badge-green   { background: rgba(34,197,94,0.12);  color: #166534; }
    .badge-amber   { background: rgba(251,191,36,0.15); color: #92400e; }
    .badge-red     { background: rgba(248,113,113,0.15); color: #991b1b; }
    .badge-gray    { background: rgba(100,116,139,0.12); color: #475569; }
    .badge-blue    { background: rgba(59,130,246,0.12); color: #0369a1; }

    /* ── Form styles ── */
    .form-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-lg); padding: 1.75rem;
      max-width: 640px;
    }
    .form-group { margin-bottom: 1.25rem; }
    .form-label {
      display: block; margin-bottom: 0.4rem;
      font-size: 13px; font-weight: 500; color: var(--text-primary);
    }
    .form-label .req { color: #f87171; margin-left: 2px; }
    .form-control {
      width: 100%; padding: 0.6rem 0.8rem;
      border: 1px solid var(--border); border-radius: var(--radius-md);
      font-size: 13.5px; font-family: 'DM Sans', sans-serif;
      color: var(--text-primary); background: var(--surface);
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus {
      outline: none;
      border-color: #7c3aed;
      box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
    }
    .form-control.is-error { border-color: #f87171; background: rgba(248,113,113,0.04); }
    textarea.form-control { resize: vertical; min-height: 90px; }
    .form-hint { font-size: 11.5px; color: var(--text-tertiary); margin-top: 0.3rem; }
    .field-error { font-size: 11.5px; color: #dc2626; margin-top: 0.3rem; }
    .form-check {
      display: flex; align-items: center; gap: 0.5rem;
      font-size: 13.5px; color: var(--text-secondary); cursor: pointer;
    }
    .form-check input[type="checkbox"] {
      width: 15px; height: 15px; cursor: pointer; accent-color: #7c3aed;
    }
    .form-actions { display: flex; gap: 0.75rem; margin-top: 1.75rem; }

    .btn-primary {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.6rem 1.25rem;
      background: #7c3aed; color: #fff;
      border: none; border-radius: var(--radius-md);
      font-size: 13.5px; font-weight: 500;
      cursor: pointer; text-decoration: none;
      transition: opacity 0.15s, transform 0.15s;
    }
    .btn-primary:hover { opacity: 0.88; transform: translateY(-1px); }
    .btn-secondary {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.6rem 1.25rem;
      background: var(--border); color: var(--text-secondary);
      border: none; border-radius: var(--radius-md);
      font-size: 13.5px; font-weight: 500;
      cursor: pointer; text-decoration: none;
      transition: background 0.15s;
    }
    .btn-secondary:hover { background: #d1d5db; }
    .btn-danger {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.6rem 1.25rem;
      background: rgba(248,113,113,0.12); color: #dc2626;
      border: none; border-radius: var(--radius-md);
      font-size: 13.5px; font-weight: 500;
      cursor: pointer; text-decoration: none;
      transition: background 0.15s;
    }
    .btn-danger:hover { background: rgba(248,113,113,0.2); }

    /* ── Actions inline ── */
    .action-link {
      display: inline-flex; align-items: center; gap: 0.3rem;
      font-size: 12.5px; font-weight: 500;
      text-decoration: none; padding: 0.3rem 0.65rem;
      border-radius: var(--radius-md);
      border: none; cursor: pointer; transition: background 0.15s;
    }
    .action-link svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }
    .action-edit   { background: rgba(59,130,246,0.1); color: #0369a1; }
    .action-edit:hover   { background: rgba(59,130,246,0.18); }
    .action-delete { background: rgba(248,113,113,0.1); color: #dc2626; }
    .action-delete:hover { background: rgba(248,113,113,0.18); }
    .action-view   { background: rgba(124,58,237,0.1); color: #7c3aed; }
    .action-view:hover   { background: rgba(124,58,237,0.18); }
    .action-close  { background: rgba(34,197,94,0.1); color: #166534; }
    .action-close:hover  { background: rgba(34,197,94,0.18); }

    /* ── Empty state ── */
    .empty-state {
      text-align: center; padding: 3rem 2rem;
      color: var(--text-tertiary);
    }
    .empty-state p { font-size: 13px; margin: 0.5rem 0 1.5rem; }

    /* ── Info box ── */
    .info-box {
      background: rgba(124,58,237,0.06);
      border-left: 3px solid #7c3aed;
      padding: 0.8rem 1rem;
      border-radius: 0 var(--radius-md) var(--radius-md) 0;
      margin-bottom: 1.25rem;
      font-size: 13px; color: var(--text-secondary);
    }

    /* ── Inline action svgs ── */
    .icon-sm { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }
  </style>
  <?= $this->renderSection('page_styles') ?>
</head>
<body>
<?php
  $activeSeg = service('uri')->getSegment(1) ?: 'home';
  $userName  = esc($user['name'] ?? session('nombre') ?? 'Usuario');
  $userRole  = esc($user['role'] ?? session('rol_nombre') ?? '');
  $userInit  = strtoupper(mb_substr(strip_tags($userName), 0, 1));
?>
<div class="dash-layout">

  <!-- ── Sidebar ── -->
  <aside class="sidebar">
    <a href="/dashboard" class="sidebar-logo">
      <svg width="28" height="28" viewBox="0 0 40 40" fill="none">
        <defs>
          <radialGradient id="sgL" cx="35%" cy="35%">
            <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1"/>
            <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1"/>
            <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1"/>
          </radialGradient>
          <radialGradient id="ssL" cx="35%" cy="35%">
            <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6"/>
            <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0"/>
          </radialGradient>
        </defs>
        <circle cx="20" cy="20" r="18" fill="url(#sgL)"/>
        <circle cx="14" cy="12" r="8" fill="url(#ssL)"/>
        <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3"/>
      </svg>
      <span class="logo-text">Gestion_POS</span>
    </a>

    <nav class="sidebar-nav">
      <a href="/dashboard" class="nav-item <?= $activeSeg === 'dashboard' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>
      <a href="/mesas" class="nav-item <?= $activeSeg === 'mesas' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
        Mesas
      </a>
      <a href="/pedidos" class="nav-item <?= $activeSeg === 'pedidos' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Pedidos
      </a>

      <div class="nav-section-label">Catálogo</div>

      <a href="/productos" class="nav-item <?= $activeSeg === 'productos' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        Productos
      </a>
      <a href="/categorias" class="nav-item <?= $activeSeg === 'categorias' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
        Categorías
      </a>
      <a href="/inventario" class="nav-item <?= $activeSeg === 'inventario' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Inventario
      </a>

      <div class="nav-section-label">Reportes</div>

      <a href="/reportes/ventas" class="nav-item <?= $activeSeg === 'reportes' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Ventas
      </a>
      <a href="/reportes/caja" class="nav-item">
        <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        Caja
      </a>

      <div class="nav-section-label">Config</div>

      <a href="/configuracion" class="nav-item <?= $activeSeg === 'configuracion' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        Configuración
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="user-avatar"><?= $userInit ?></div>
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

  <!-- ── Main ── -->
  <main class="dash-main">

    <!-- Topbar -->
    <div class="dash-topbar">
      <span class="topbar-title"><?= esc($titulo ?? '') ?></span>
      <div class="topbar-right">
        <div class="status-dot"></div>
        <span class="topbar-date"><?= date('d/m/Y · H:i') ?></span>
        <?= $this->renderSection('topbar_right') ?>
      </div>
    </div>

    <!-- Content -->
    <div class="dash-content">

      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash-msg flash-success">
          <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash-msg flash-error">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <?php $flashErrors = session()->getFlashdata('errors'); if (!empty($flashErrors)): ?>
        <div class="flash-errors">
          <strong>Por favor corregí los siguientes errores:</strong>
          <ul>
            <?php foreach ((array)$flashErrors as $err): ?>
              <li><?= esc($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?= $this->renderSection('content') ?>

    </div>
  </main>

</div>
</body>
</html>
