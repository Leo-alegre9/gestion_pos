<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/mesas/crear" class="btn-pill">
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
  Nueva Mesa
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header-row" style="margin-bottom:1.25rem">
  <h1>Mesas</h1>
</div>

<!-- Resumen -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;">
  <?php
    $summaryItems = [
      ['label' => 'Libres',    'key' => 'libre',    'color' => '#22c55e'],
      ['label' => 'Ocupadas',  'key' => 'ocupada',  'color' => '#7c3aed'],
      ['label' => 'Reservadas','key' => 'reservada','color' => '#f59e0b'],
      ['label' => 'Inactivas', 'key' => 'inactiva', 'color' => '#6b7280'],
    ];
    foreach ($summaryItems as $si):
  ?>
  <div class="panel" style="padding:1rem;border-top:3px solid <?= $si['color'] ?>">
    <div style="font-size:11px;font-weight:500;letter-spacing:0.05em;text-transform:uppercase;color:var(--text-tertiary);margin-bottom:0.35rem"><?= $si['label'] ?></div>
    <div style="font-size:22px;font-weight:600;color:var(--text-primary)"><?= $resumen[$si['key']] ?? 0 ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Grid de mesas -->
<?php if (!empty($mesas) && is_array($mesas)): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
  <?php foreach ($mesas as $mesa):
    $estado = $mesa['estado'] ?? 'libre';
    $estadoBadge = match($estado) {
      'libre'    => 'badge-green',
      'ocupada'  => 'badge-violet',
      'reservada'=> 'badge-amber',
      default    => 'badge-gray',
    };
  ?>
  <div class="panel" style="padding:1.1rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
      <span style="font-size:18px;font-weight:700;color:var(--text-primary)">Mesa <?= str_pad((string)($mesa['numero'] ?? 0), 2, '0', STR_PAD_LEFT) ?></span>
      <span class="badge <?= $estadoBadge ?>"><?= ucfirst($estado) ?></span>
    </div>

    <div style="font-size:12.5px;color:var(--text-tertiary);margin-bottom:0.75rem">
      Capacidad: <?= esc($mesa['capacidad'] ?? '—') ?> pers.
      <?php if (!empty($mesa['id_pedido'])): ?>
        · <a href="/pedidos/detalles/<?= $mesa['id_pedido'] ?>" style="color:var(--purple,#7c3aed);font-weight:500">Pedido #<?= str_pad($mesa['id_pedido'], 5, '0', STR_PAD_LEFT) ?></a>
      <?php endif; ?>
    </div>

    <!-- Cambiar estado -->
    <form method="post" action="/mesas/cambiar-estado/<?= $mesa['id_mesa'] ?>" style="display:flex;gap:0.5rem;margin-bottom:0.5rem;">
      <?= csrf_field() ?>
      <select name="estado" class="form-control" style="font-size:12.5px;padding:0.4rem 0.5rem;flex:1">
        <?php foreach (['libre','ocupada','reservada','inactiva'] as $opt): ?>
          <option value="<?= $opt ?>" <?= $estado === $opt ? 'selected' : '' ?>><?= ucfirst($opt) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-primary" style="padding:0.4rem 0.65rem;font-size:12px;white-space:nowrap">OK</button>
    </form>

    <!-- Eliminar -->
    <form method="post" action="/mesas/eliminar/<?= $mesa['id_mesa'] ?>" onsubmit="return confirm('¿Eliminar esta mesa?')">
      <?= csrf_field() ?>
      <input type="hidden" name="_method" value="DELETE">
      <button type="submit" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:12px;padding:0;display:flex;align-items:center;gap:0.3rem;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
        </svg>
        Eliminar
      </button>
    </form>
  </div>
  <?php endforeach; ?>
</div>

<?php else: ?>
<div class="panel">
  <div class="empty-state">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.25">
      <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
    </svg>
    <p>No hay mesas registradas.</p>
    <a href="/mesas/crear" class="btn-pill">Crear primera mesa</a>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
