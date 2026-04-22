<?= $this->extend('layouts/app') ?>

<?= $this->section('page_styles') ?>
<style>
  .fac-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;
  }
  .fac-header-left { display: flex; align-items: center; gap: 1rem; }
  .fac-icon {
    width: 52px; height: 52px; border-radius: 50%;
    background: rgba(124,58,237,.1); display: flex;
    align-items: center; justify-content: center; flex-shrink: 0;
  }
  .fac-num   { font-size: 22px; font-weight: 600; letter-spacing: -.02em; color: var(--text-primary); }
  .fac-date  { font-size: 13px; color: var(--text-tertiary); margin-top: .2rem; }
  .fac-badge { display: inline-flex; align-items: center; gap: .4rem; background: rgba(34,197,94,.1); color: #15803d; font-size: 12px; font-weight: 500; padding: 4px 12px; border-radius: 20px; }

  .info-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(155px,1fr)); gap: 1rem; margin-bottom: 1.5rem; }
  .info-card  { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1rem; }
  .info-card-label { font-size: 10.5px; font-weight: 500; letter-spacing: .06em; text-transform: uppercase; color: var(--text-tertiary); margin-bottom: .35rem; }
  .info-card-value { font-size: 15px; font-weight: 500; color: var(--text-primary); }
  .info-card-sub   { font-size: 12px; color: var(--text-tertiary); margin-top: .15rem; }

  .items-tfoot th { font-size: 14px; font-weight: 600; padding: .8rem 1rem; border-top: 2px solid var(--border); }

  .fac-totals {
    margin-top: 1.25rem;
    display: flex; justify-content: flex-end;
  }
  .fac-totals-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    min-width: 260px;
  }
  .total-line {
    display: flex; justify-content: space-between; align-items: center;
    padding: .4rem 0;
    font-size: 13px; color: var(--text-secondary);
  }
  .total-line.main {
    border-top: 2px solid var(--border); margin-top: .4rem; padding-top: .75rem;
    font-size: 16px; font-weight: 600; color: var(--text-primary);
  }
  .total-line.main .mono { color: var(--purple); font-size: 20px; }
  .total-line.vuelto { color: #15803d; font-size: 13px; }
  .total-line.vuelto .mono { color: #15803d; }

  @media print {
    .sidebar, .app-topbar, .no-print { display: none !important; }
    .app-main { margin-left: 0 !important; }
    .app-content { padding: 1rem !important; }
    .fac-totals { justify-content: flex-end; }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('topbar_right') ?>
<a href="/facturacion?fecha=<?= date('Y-m-d', strtotime($pago['fecha_pago'])) ?>" class="btn-ghost no-print">← Facturación</a>
<button onclick="window.print()" class="btn-ghost no-print">
  <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
  Imprimir
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Encabezado de factura -->
<div class="fac-header">
  <div class="fac-header-left">
    <div class="fac-icon">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
      </svg>
    </div>
    <div>
      <div class="fac-num">FAC-<?= str_pad($pago['id_pago'], 6, '0', STR_PAD_LEFT) ?></div>
      <div class="fac-date"><?= date('d \d\e F \d\e Y · H:i', strtotime($pago['fecha_pago'])) ?></div>
    </div>
  </div>
  <div>
    <span class="fac-badge">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      Pagada
    </span>
  </div>
</div>

<!-- Info cards -->
<div class="info-cards">
  <div class="info-card">
    <div class="info-card-label">Pedido</div>
    <div class="info-card-value mono">
      <a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" style="color:var(--purple);text-decoration:none">
        #<?= str_pad($pedido['id_pedido'], 5, '0', STR_PAD_LEFT) ?>
      </a>
    </div>
    <div class="info-card-sub"><?= date('d/m/Y H:i', strtotime($pedido['fecha_apertura'])) ?></div>
  </div>

  <div class="info-card">
    <div class="info-card-label">Tipo / Mesa</div>
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
    <?php if ($pedido['tipo_pedido'] === 'mesa' && !empty($pedido['numero_mesa']) && $pedido['numero_mesa'] !== 'N/A'): ?>
      <div class="info-card-sub">Mesa <?= $pedido['numero_mesa'] ?><?= $pedido['capacidad_mesa'] ? ' · ' . $pedido['capacidad_mesa'] . ' pers.' : '' ?></div>
    <?php endif; ?>
  </div>

  <div class="info-card">
    <div class="info-card-label">Atendió</div>
    <div class="info-card-value"><?= esc($pedido['usuario_nombre']) ?></div>
    <div class="info-card-sub"><?= esc($pedido['usuario_rol'] ?? '') ?></div>
  </div>

  <div class="info-card">
    <div class="info-card-label">Método de pago</div>
    <div class="info-card-value"><?= esc($pago['metodo_nombre']) ?></div>
    <div class="info-card-sub"><?= date('H:i', strtotime($pago['fecha_pago'])) ?></div>
  </div>
</div>

<!-- Detalle de productos -->
<div class="panel">
  <div class="panel-head">
    <span class="panel-title">Detalle de productos</span>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Producto</th>
        <th style="text-align:center">Cantidad</th>
        <th style="text-align:right">P. Unitario</th>
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
          <td class="mono" style="text-align:center"><?= number_format((float)$item['cantidad'], 0, ',', '.') ?></td>
          <td class="mono" style="text-align:right;color:var(--text-secondary)">$<?= number_format((float)$item['precio_unitario'], 1, ',', '.') ?></td>
          <td class="mono" style="text-align:right;font-weight:500;color:var(--text-primary)">$<?= number_format((float)$item['subtotal'], 1, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--text-tertiary);font-size:13px">Sin productos registrados.</td></tr>
      <?php endif; ?>
    </tbody>
    <tfoot class="items-tfoot">
      <tr>
        <th colspan="3" style="text-align:right;font-size:13px;color:var(--text-secondary);font-weight:500">Subtotal pedido</th>
        <th class="mono" style="text-align:right;color:var(--text-primary)">$<?= number_format($total, 1, ',', '.') ?></th>
      </tr>
    </tfoot>
  </table>
</div>

<!-- Totales -->
<div class="fac-totals">
  <div class="fac-totals-box">
    <div class="total-line">
      <span>Subtotal</span>
      <span class="mono">$<?= number_format($total, 1, ',', '.') ?></span>
    </div>
    <div class="total-line">
      <span>Método de pago</span>
      <span><?= esc($pago['metodo_nombre']) ?></span>
    </div>
    <div class="total-line">
      <span>Monto recibido</span>
      <span class="mono">$<?= number_format((float)$pago['monto'], 1, ',', '.') ?></span>
    </div>
    <div class="total-line main">
      <span>Total cobrado</span>
      <span class="mono">$<?= number_format((float)$pago['monto'], 1, ',', '.') ?></span>
    </div>
    <?php if ($vuelto > 0): ?>
    <div class="total-line vuelto">
      <span>Vuelto entregado</span>
      <span class="mono">$<?= number_format($vuelto, 1, ',', '.') ?></span>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Acciones -->
<div class="no-print" style="margin-top:1.25rem;display:flex;gap:.75rem;align-items:center;flex-wrap:wrap">
  <a href="/facturacion?fecha=<?= date('Y-m-d', strtotime($pago['fecha_pago'])) ?>" class="btn-secondary">← Volver a facturación</a>
  <a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" class="btn-ghost">Ver pedido</a>
  <button onclick="window.print()" class="btn-ghost" style="margin-left:auto">
    <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Imprimir factura
  </button>
</div>

<?= $this->endSection() ?>
