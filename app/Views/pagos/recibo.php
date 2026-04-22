<?= $this->extend('layouts/app') ?>

<?= $this->section('page_styles') ?>
<style>
  .info-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(155px,1fr)); gap:1rem; margin-bottom:1.5rem; }
  .info-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1rem; }
  .info-card-label { font-size:10.5px; font-weight:500; letter-spacing:0.06em; text-transform:uppercase; color:var(--text-tertiary); margin-bottom:0.35rem; }
  .info-card-value { font-size:15px; font-weight:500; color:var(--text-primary); }
  .info-card-sub   { font-size:12px; color:var(--text-tertiary); margin-top:0.15rem; }
  .items-tfoot th  { font-size:14px; font-weight:600; color:var(--text-primary); padding:0.8rem 1rem; border-top:2px solid var(--border); }
  .recibo-header { display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem; }
  .recibo-icon { width:48px; height:48px; border-radius:50%; background:rgba(34,197,94,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .recibo-title { font-size:20px; font-weight:600; color:var(--text-primary); letter-spacing:-0.02em; }
  .recibo-sub { font-size:13px; color:var(--text-tertiary); margin-top:0.2rem; }
  @media print {
    .sidebar, .app-topbar, .no-print { display:none !important; }
    .app-main { margin-left:0 !important; }
    .app-content { padding:1rem !important; }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('topbar_right') ?>
<a href="/pedidos/historial?fecha=<?= date('Y-m-d', strtotime($pago['fecha_pago'])) ?>" class="btn-ghost">Historial</a>
<button onclick="window.print()" class="btn-ghost no-print">
  <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
  Imprimir
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Encabezado de confirmación -->
<div class="recibo-header">
  <div class="recibo-icon">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
  </div>
  <div>
    <div class="recibo-title">Pago registrado</div>
    <div class="recibo-sub">Comprobante #<?= str_pad($pago['id_pago'], 5, '0', STR_PAD_LEFT) ?> · <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?></div>
  </div>
</div>

<!-- Info cards -->
<div class="info-cards">
  <div class="info-card">
    <div class="info-card-label">Pedido</div>
    <div class="info-card-value mono">#<?= str_pad($pedido['id_pedido'], 5, '0', STR_PAD_LEFT) ?></div>
    <div class="info-card-sub"><?= date('d/m/Y H:i', strtotime($pedido['fecha_apertura'])) ?></div>
  </div>
  <div class="info-card">
    <div class="info-card-label">Tipo</div>
    <?php
      $tipoLabel = match($pedido['tipo_pedido']) {
        'barra'    => 'Barra',
        'take_away'=> 'Para llevar',
        default    => 'Mesa',
      };
      $tipoBadge = match($pedido['tipo_pedido']) {
        'barra'    => 'badge-violet',
        'take_away'=> 'badge-green',
        default    => 'badge-blue',
      };
    ?>
    <div class="info-card-value">
      <span class="badge <?= $tipoBadge ?>" style="font-size:12px"><?= $tipoLabel ?></span>
    </div>
    <?php if ($pedido['tipo_pedido'] === 'mesa' && $pedido['numero_mesa'] !== 'N/A'): ?>
      <div class="info-card-sub">Mesa <?= $pedido['numero_mesa'] ?></div>
    <?php endif; ?>
  </div>
  <div class="info-card">
    <div class="info-card-label">Método de pago</div>
    <div class="info-card-value"><?= esc($pago['metodo_nombre']) ?></div>
    <div class="info-card-sub"><?= date('H:i', strtotime($pago['fecha_pago'])) ?></div>
  </div>
  <div class="info-card">
    <div class="info-card-label">Monto cobrado</div>
    <div class="info-card-value mono" style="font-size:18px;color:#15803d">$<?= number_format((float)$pago['monto'], 2, ',', '.') ?></div>
    <?php $vuelto = (float)$pago['monto'] - $total; ?>
    <?php if ($vuelto > 0.005): ?>
      <div class="info-card-sub" style="color:#15803d">Vuelto: $<?= number_format($vuelto, 2, ',', '.') ?></div>
    <?php endif; ?>
  </div>
</div>

<!-- Detalle de items -->
<div class="panel">
  <div class="panel-head">
    <span class="panel-title">Detalle del pedido</span>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Producto</th>
        <th style="text-align:center">Cant.</th>
        <th style="text-align:right">P. Unit.</th>
        <th style="text-align:right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
        <tr>
          <td>
            <div style="font-weight:500;color:var(--text-primary)"><?= esc($item['nombre']) ?></div>
            <?php if (!empty($item['observaciones'])): ?>
              <div style="font-size:11.5px;color:var(--text-tertiary)"><?= esc($item['observaciones']) ?></div>
            <?php endif; ?>
          </td>
          <td class="mono" style="text-align:center"><?= number_format((float)$item['cantidad'], 2, ',', '.') ?></td>
          <td class="mono" style="text-align:right;color:var(--text-secondary)">$<?= number_format((float)$item['precio_unitario'], 2, ',', '.') ?></td>
          <td class="mono" style="text-align:right;font-weight:500">$<?= number_format((float)$item['subtotal'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--text-tertiary);font-size:13px">Sin productos registrados.</td></tr>
      <?php endif; ?>
    </tbody>
    <tfoot class="items-tfoot">
      <tr>
        <th colspan="3" style="text-align:right;font-size:13px;color:var(--text-secondary);font-weight:500">Total pedido</th>
        <th class="mono" style="text-align:right;font-size:20px;color:var(--purple)">$<?= number_format($total, 2, ',', '.') ?></th>
      </tr>
    </tfoot>
  </table>
</div>

<!-- Acciones -->
<div class="no-print" style="margin-top:1.25rem;display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap">
  <a href="/pedidos" class="btn-primary">
    <svg class="icon-sm" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Ir a pedidos
  </a>
  <a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" class="btn-secondary">Ver pedido</a>
  <button onclick="window.print()" class="btn-ghost">
    <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Imprimir comprobante
  </button>
</div>

<?= $this->endSection() ?>
