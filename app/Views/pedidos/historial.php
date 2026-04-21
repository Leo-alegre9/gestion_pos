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
        <th>Estado</th>
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
        <td><span class="badge badge-gray"><?= esc($pedido['estado_nombre']) ?></span></td>
        <td>
          <a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" class="action-link action-view">
            <svg class="icon-sm" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Ver
          </a>
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
