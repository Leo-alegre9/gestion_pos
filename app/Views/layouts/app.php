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
      transition: opacity 0.15s;
    }
    .sidebar-logo:hover {
      opacity: 0.8;
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
      padding: 1rem 0.625rem 0.35rem;
      margin-top: 0.5rem;
      border-top: 1px solid rgba(0,0,0,0.04);
    }

    .nav-item {
      padding-left: 2.5rem !important;
      width: 100%;
      display: flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.5rem 0.65rem;
      border-radius: var(--radius-md);
      font-size: 13px;
      font-weight: 400;
      color: var(--text-secondary);
      text-decoration: none;
      transition: background 0.12s, color 0.12s, border-color 0.12s;
      white-space: nowrap;
      position: relative;
      border-left: 2px solid transparent;
      margin-bottom: 0.25rem;
    }
    .nav-item svg {
      width: 15px; height: 15px;
      stroke: currentColor; fill: none;
      stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round;
      flex-shrink: 0;
      opacity: 0.75;
      transition: opacity 0.12s;
    }
    .nav-item:hover {
      background: var(--bg);
      color: var(--text-primary);
      border-left-color: #7c3aed4d;
    }
    .nav-item:hover svg {
      opacity: 1;
    }
    .nav-item.active {
      background: var(--purple-dim);
      color: var(--purple);
      font-weight: 500;
      border-left-color: var(--purple);
    }
    .nav-item.active svg { 
      stroke: var(--purple);
      opacity: 1;
    }

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
      box-shadow: 0 1px 0 rgba(0,0,0,0.02);
    }
    .topbar-hamburger {
      display: none;
      background: none; border: none; cursor: pointer;
      color: var(--text-secondary); padding: 4px;
      border-radius: var(--radius-sm);
      transition: color 0.15s, background 0.15s;
    }
    .topbar-hamburger:hover {
      background: var(--bg);
      color: var(--text-primary);
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
      transition:opacity 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .btn-pill:hover { opacity:0.88; box-shadow:0 2px 8px rgba(124,58,237,0.3); }
    .btn-pill:active { transform: scale(0.98); }

    .btn-ghost {
      display:inline-flex; align-items:center; gap:0.35rem;
      padding:0.38rem 0.85rem;
      background:transparent; color:var(--text-secondary);
      border:1px solid var(--border); border-radius:var(--radius-md);
      font-size:12.5px; font-weight:500; font-family:'DM Sans',sans-serif;
      cursor:pointer; text-decoration:none;
      transition:background 0.12s, border-color 0.12s, color 0.12s;
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
<div class="app-layout">

  <!-- ─── Sidebar Unificado ─── -->
  <?= $this->include('components/sidebar') ?>

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
