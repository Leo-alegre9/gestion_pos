<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/pedidos/historial" class="btn-ghost">Historial</a>
<a href="/pedidos/crear" class="btn-pill">
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
  Nuevo pedido
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (!empty($resumen)): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem;">
  <?php foreach ($resumen as $item): ?>
  <div class="panel" style="overflow:visible;">
    <div class="panel-body" style="padding:1rem;">
      <div style="font-size:11px;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.3rem">
        <?= $item['tipo_pedido'] === 'take_away' ? 'Para llevar' : ucfirst($item['tipo_pedido']) ?>
      </div>
      <div style="font-size:26px;font-weight:500;letter-spacing:-0.03em;color:var(--text-primary);line-height:1"><?= $item['total'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="panel">
  <?php if (!empty($pedidos)): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Tipo / Mesa</th>
        <th>Usuario</th>
        <th>Apertura</th>
        <th>Estado</th>
        <th>Acciones</th>
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
          <?php if ($pedido['tipo_pedido'] === 'mesa'): ?>
            <span style="font-size:12px;color:var(--text-secondary);margin-left:0.3rem">Mesa <?= $pedido['numero_mesa'] ?></span>
          <?php endif; ?>
        </td>
        <td><?= esc($pedido['usuario_nombre']) ?></td>
        <td class="mono" style="font-size:12px"><?= date('d/m/Y H:i', strtotime($pedido['fecha_apertura'])) ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:0.4rem">
            <span style="width:7px;height:7px;border-radius:50%;background:<?= $pedido['fecha_cierre'] ? '#9ca3af' : '#22c55e' ?>;display:inline-block"></span>
            <span style="font-size:13px"><?= esc($pedido['estado_nombre']) ?></span>
          </div>
        </td>
        <td>
          <div style="display:flex;gap:0.4rem">
            <a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" class="action-link action-view">
              <svg class="icon-sm" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              Ver
            </a>
            <?php if (!$pedido['fecha_cierre']): ?>
            <form method="post" action="/pedidos/cerrar/<?= $pedido['id_pedido'] ?>" style="display:inline;" onsubmit="return confirm('¿Cerrar el pedido #<?= $pedido['id_pedido'] ?>?')">
              <?= csrf_field() ?>
              <button type="submit" class="action-link action-close">
                <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Cerrar
              </button>
            </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
      <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <p>No hay pedidos activos en este momento.</p>
    <a href="/pedidos/crear" class="btn-pill">Crear primer pedido</a>
  </div>
  <?php endif; ?>
</div>

<?php if (!empty($pendientesPago)): ?>
<div style="margin-top:1.5rem">
  <div style="font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:#b45309;margin-bottom:0.75rem;display:flex;align-items:center;gap:0.5rem">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Pendientes de pago (<?= count($pendientesPago) ?>)
  </div>
  <div class="panel">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Tipo / Mesa</th>
          <th>Usuario</th>
          <th>Cerrado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pendientesPago as $p): ?>
        <tr>
          <td class="mono">#<?= str_pad($p['id_pedido'], 5, '0', STR_PAD_LEFT) ?></td>
          <td>
            <?php
              $tipoBadge = match($p['tipo_pedido']) {
                'mesa'     => 'badge-blue',
                'barra'    => 'badge-violet',
                'take_away'=> 'badge-green',
                default    => 'badge-gray',
              };
              $tipoLabel = $p['tipo_pedido'] === 'take_away' ? 'Para llevar' : ucfirst($p['tipo_pedido']);
            ?>
            <span class="badge <?= $tipoBadge ?>"><?= $tipoLabel ?></span>
            <?php if ($p['tipo_pedido'] === 'mesa'): ?>
              <span style="font-size:12px;color:var(--text-secondary);margin-left:0.3rem">Mesa <?= $p['numero_mesa'] ?></span>
            <?php endif; ?>
          </td>
          <td><?= esc($p['usuario_nombre']) ?></td>
          <td class="mono" style="font-size:12px"><?= date('d/m/Y H:i', strtotime($p['fecha_cierre'])) ?></td>
          <td>
            <a href="/pagos/pagar/<?= $p['id_pedido'] ?>" class="action-link" style="background:rgba(245,158,11,0.1);color:#b45309">
              <svg class="icon-sm" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
              Cobrar
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
