<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/inventario" class="btn-ghost">← Inventario</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header-row">
  <h1>Alertas de Stock</h1>
</div>

<?php if (!empty($alertas)): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;">
  <?php foreach ($alertas as $alerta):
    $disp      = (float)$alerta['cantidad_disponible'];
    $min       = (float)$alerta['cantidad_minima'];
    $isCritico = $disp <= 0;
    $borderClr = $isCritico ? '#ef4444' : '#f59e0b';
    $badgeCls  = $isCritico ? 'badge-red' : 'badge-amber';
    $nivel     = $isCritico ? 'Crítico' : 'Bajo';
    $base      = $min > 0 ? $min * 2 : 1;
    $pct       = min(round(($disp / $base) * 100), 100);
  ?>
  <div class="panel" style="border-left:3px solid <?= $borderClr ?>;padding:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
      <div>
        <div style="font-weight:500;color:var(--text-primary);margin-bottom:0.25rem"><?= esc($alerta['producto_nombre']) ?></div>
        <span class="badge badge-violet"><?= esc($alerta['categoria_nombre'] ?? '—') ?></span>
      </div>
      <span class="badge <?= $badgeCls ?>"><?= $nivel ?></span>
    </div>

    <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text-secondary);margin-bottom:0.4rem;">
      <span>Disponible</span>
      <span class="mono" style="font-weight:500;color:var(--text-primary)"><?= number_format($disp, 2, ',', '.') ?></span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text-secondary);margin-bottom:0.75rem;">
      <span>Mínimo</span>
      <span class="mono"><?= number_format($min, 2, ',', '.') ?></span>
    </div>

    <div style="background:var(--border);height:5px;border-radius:3px;overflow:hidden;margin-bottom:1rem;">
      <div style="height:100%;width:<?= $pct ?>%;background:<?= $borderClr ?>;border-radius:3px"></div>
    </div>

    <a href="/inventario/editar/<?= $alerta['id_stock'] ?>" class="btn-primary" style="display:flex;align-items:center;justify-content:center;gap:0.4rem;font-size:13px;padding:0.55rem 1rem;text-decoration:none;border-radius:var(--radius-md);">
      <svg class="icon-sm" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Actualizar Stock
    </a>
  </div>
  <?php endforeach; ?>
</div>

<?php else: ?>
<div class="panel">
  <div class="empty-state">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
    <p style="color:var(--text-secondary)">Todo en orden. No hay productos con stock bajo o crítico.</p>
    <a href="/inventario" class="btn-ghost" style="margin-top:0.5rem">Ver inventario</a>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
