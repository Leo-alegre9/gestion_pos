<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/pedidos" class="btn-ghost">← Pedidos activos</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
  <h1>Historial de Pedidos</h1>
  <p>Pedidos cerrados filtrados por fecha.</p>
</div>

<!-- Filtro de fecha -->
<div class="panel" style="margin-bottom:1.25rem;">
  <div class="panel-body" style="padding:1rem 1.25rem;">
    <form method="get" action="/pedidos/historial" style="display:flex;align-items:flex-end;gap:0.75rem;flex-wrap:wrap;">
      <div>
        <label for="fecha" style="display:block;font-size:12px;color:var(--text-tertiary);margin-bottom:0.3rem">Fecha</label>
        <input type="date" id="fecha" name="fecha" value="<?= esc($fecha) ?>" class="form-control" style="width:auto;">
      </div>
      <button type="submit" class="btn-primary">Filtrar</button>
    </form>
  </div>
</div>

<!-- Tabla de historial -->
<div class="panel">
  <?php if (!empty($pedidos)): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Tipo</th>
        <th>Mesa</th>
        <th>Usuario</th>
        <th>Apertura</th>
        <th>Cierre</th>
        <th>Pago</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pedidos as $pedido): ?>
      <tr>
        <td class="mono">#<?= str_pad($pedido['id_pedido'], 5, '0', STR_PAD_LEFT) ?></td>
        <td>
          <?php
            $tipoBadge = match($pedido['tipo_pedido']) {
              'mesa'     => 'badge-blue',
              'barra'    => 'badge-violet',
              'take_away'=> 'badge-green',
              default    => 'badge-gray',
            };
            $tipoLabel = $pedido['tipo_pedido'] === 'take_away' ? 'Para llevar' : ucfirst($pedido['tipo_pedido']);
          ?>
          <span class="badge <?= $tipoBadge ?>"><?= $tipoLabel ?></span>
        </td>
        <td><?= esc($pedido['numero_mesa']) ?></td>
        <td><?= esc($pedido['usuario_nombre']) ?></td>
        <td class="mono" style="font-size:12px"><?= date('d/m/Y H:i', strtotime($pedido['fecha_apertura'])) ?></td>
        <td class="mono" style="font-size:12px"><?= $pedido['fecha_cierre'] ? date('d/m/Y H:i', strtotime($pedido['fecha_cierre'])) : '—' ?></td>
        <td>
          <?php if (!empty($pedido['id_pago'])): ?>
            <div style="font-size:12px;color:#15803d;font-weight:500">
              $<?= number_format((float)$pedido['monto_pagado'], 2, ',', '.') ?>
            </div>
            <div style="font-size:11px;color:var(--text-tertiary)"><?= esc($pedido['metodo_pago_nombre']) ?></div>
          <?php else: ?>
            <span style="font-size:12px;color:#b45309;font-weight:500">Pendiente</span>
          <?php endif; ?>
        </td>
        <td>
          <div style="display:flex;gap:0.4rem">
            <a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" class="action-link action-view">
              <svg class="icon-sm" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              Ver
            </a>
            <?php if (empty($pedido['id_pago'])): ?>
            <a href="/pagos/pagar/<?= $pedido['id_pedido'] ?>" class="action-link" style="background:rgba(245,158,11,0.1);color:#b45309">
              <svg class="icon-sm" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
              Cobrar
            </a>
            <?php else: ?>
            <a href="/pagos/recibo/<?= $pedido['id_pago'] ?>" class="action-link action-close">
              <svg class="icon-sm" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              Recibo
            </a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state">
    <p>No hay pedidos cerrados para el <?= date('d/m/Y', strtotime($fecha)) ?>.</p>
  </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
