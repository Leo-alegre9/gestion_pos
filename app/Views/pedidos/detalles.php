<?= $this->extend('layouts/app') ?>

<?= $this->section('page_styles') ?>
<style>
  .info-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(155px,1fr)); gap:1rem; margin-bottom:1.5rem; }
  .info-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1rem; }
  .info-card-label { font-size:10.5px; font-weight:500; letter-spacing:0.06em; text-transform:uppercase; color:var(--text-tertiary); margin-bottom:0.35rem; }
  .info-card-value { font-size:15px; font-weight:500; color:var(--text-primary); }
  .info-card-sub   { font-size:12px; color:var(--text-tertiary); margin-top:0.15rem; }
  .items-tfoot th  { font-size:14px; font-weight:600; color:var(--text-primary); padding:0.8rem 1rem; border-top:2px solid var(--border); }

  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal-box { background:var(--surface); border-radius:var(--radius-lg); padding:1.75rem; width:100%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,0.2); position:relative; margin:1rem; }
  .modal-box h2 { font-size:15px; font-weight:600; margin:0 0 1.25rem; }
  .modal-close { position:absolute; top:1rem; right:1rem; background:none; border:none; cursor:pointer; color:var(--text-tertiary); font-size:20px; line-height:1; padding:0.25rem; }
  .modal-close:hover { color:var(--text-secondary); }
  #modal-precio-preview { font-size:12px; color:var(--text-tertiary); margin-top:0.25rem; min-height:16px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('topbar_right') ?>
<a href="/pedidos" class="btn-ghost">← Pedidos</a>
<?php if (!$pedido['fecha_cierre']): ?>
<button class="btn-pill" onclick="document.getElementById('modal-add').classList.add('open')">
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
  Añadir producto
</button>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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
      <div class="info-card-sub">Mesa <?= $pedido['numero_mesa'] ?><?= $pedido['capacidad_mesa'] ? ' · ' . $pedido['capacidad_mesa'] . ' pers.' : '' ?></div>
    <?php endif; ?>
  </div>
  <div class="info-card">
    <div class="info-card-label">Usuario</div>
    <div class="info-card-value"><?= esc($pedido['usuario_nombre']) ?></div>
    <div class="info-card-sub"><?= esc($pedido['usuario_rol'] ?? '') ?></div>
  </div>
  <div class="info-card">
    <div class="info-card-label">Estado</div>
    <div class="info-card-value"><?= esc($pedido['estado_nombre']) ?></div>
    <div class="info-card-sub">
      <?= $pedido['fecha_cierre']
        ? 'Cerrado ' . date('d/m/Y H:i', strtotime($pedido['fecha_cierre']))
        : 'Abierto' ?>
    </div>
  </div>
</div>

<?php if (!empty($pedido['observaciones'])): ?>
<div class="info-box" style="margin-bottom:1.5rem">
  <strong>Observaciones:</strong> <?= esc($pedido['observaciones']) ?>
</div>
<?php endif; ?>

<!-- Items del pedido -->
<div class="panel">
  <div class="panel-head">
    <span class="panel-title">Productos del pedido</span>
    <?php if (!$pedido['fecha_cierre']): ?>
    <button class="btn-pill" style="font-size:12px;padding:0.3rem 0.75rem" onclick="document.getElementById('modal-add').classList.add('open')">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Añadir
    </button>
    <?php endif; ?>
  </div>
  <?php $total = 0; ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>Producto</th>
        <th style="text-align:center">Cant.</th>
        <th style="text-align:right">P. Unit.</th>
        <th style="text-align:right">Subtotal</th>
        <?php if (!$pedido['fecha_cierre']): ?><th style="text-align:center;width:48px"></th><?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
          <?php $total += (float)$item['subtotal']; ?>
          <tr>
            <td>
              <div style="font-weight:500;color:var(--text-primary)"><?= esc($item['nombre']) ?></div>
              <?php if (!empty($item['observaciones'])): ?>
                <div style="font-size:11.5px;color:var(--text-tertiary)"><?= esc($item['observaciones']) ?></div>
              <?php endif; ?>
            </td>
            <td class="mono" style="text-align:center"><?= number_format((float)$item['cantidad'], 2, ',', '.') ?></td>
            <td class="mono" style="text-align:right;color:var(--text-secondary)">$<?= number_format((float)$item['precio_unitario'], 2, ',', '.') ?></td>
            <td class="mono" style="text-align:right;font-weight:500;color:var(--text-primary)">$<?= number_format((float)$item['subtotal'], 2, ',', '.') ?></td>
            <?php if (!$pedido['fecha_cierre']): ?>
            <td style="text-align:center">
              <form method="post" action="/pedidos/eliminar-detalle/<?= $pedido['id_pedido'] ?>/<?= $item['id_detalle_pedido'] ?>" onsubmit="return confirm('¿Quitar «<?= esc($item['nombre']) ?>» del pedido?')">
                <?= csrf_field() ?>
                <button type="submit" style="background:none;border:none;cursor:pointer;color:#dc2626;padding:0.2rem;" title="Quitar">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6V20a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                  </svg>
                </button>
              </form>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="<?= !$pedido['fecha_cierre'] ? 5 : 4 ?>" style="text-align:center;padding:2rem;color:var(--text-tertiary);font-size:13px">
            Sin productos agregados.
            <?php if (!$pedido['fecha_cierre']): ?>
              <a href="#" onclick="document.getElementById('modal-add').classList.add('open');return false" style="color:var(--purple,#7c3aed);margin-left:0.25rem">Añadir producto →</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
    <tfoot class="items-tfoot">
      <tr>
        <th colspan="<?= !$pedido['fecha_cierre'] ? 4 : 3 ?>" style="text-align:right;font-size:13px;color:var(--text-secondary);font-weight:500">Total</th>
        <th class="mono" style="text-align:right;font-size:20px;color:var(--purple,#7c3aed)">$<?= number_format($total, 2, ',', '.') ?></th>
      </tr>
    </tfoot>
  </table>
</div>

<!-- Acciones del pedido -->
<?php if (!$pedido['fecha_cierre']): ?>
<div style="margin-top:1.25rem;display:flex;gap:0.75rem;align-items:center">
  <form method="post" action="/pedidos/cerrar/<?= $pedido['id_pedido'] ?>" onsubmit="return confirm('¿Cerrar el pedido #<?= str_pad($pedido['id_pedido'],5,'0',STR_PAD_LEFT) ?>? No podrás agregar más productos.')">
    <?= csrf_field() ?>
    <button type="submit" class="btn-primary">
      <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      Cerrar Pedido
    </button>
  </form>
  <a href="/pedidos" class="btn-secondary">Volver</a>
</div>
<?php else: ?>

<!-- Sección de pago -->
<?php if ($pago): ?>
<div style="margin-top:1.5rem">
  <div class="info-box" style="border-left-color:#22c55e;background:rgba(34,197,94,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem">
    <div>
      <div style="font-weight:600;color:#15803d;margin-bottom:0.15rem">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-2px;margin-right:0.3rem"><polyline points="20 6 9 17 4 12"/></svg>
        Pago registrado
      </div>
      <div style="font-size:12px;color:var(--text-tertiary)">
        $<?= number_format((float)$pago['monto'], 2, ',', '.') ?> · <?= esc($pago['metodo_nombre']) ?> · <?= date('d/m/Y H:i', strtotime($pago['fecha_pago'])) ?>
      </div>
    </div>
    <a href="/pagos/recibo/<?= $pago['id_pago'] ?>" class="action-link action-view">
      <svg class="icon-sm" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      Ver comprobante
    </a>
  </div>
</div>
<?php else: ?>
<div style="margin-top:1.5rem">
  <div class="info-box" style="border-left-color:#f59e0b;background:rgba(245,158,11,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem">
    <div style="font-size:13px;color:#92400e">
      <strong>Pago pendiente.</strong> Este pedido está cerrado pero aún no tiene pago registrado.
    </div>
    <a href="/pagos/pagar/<?= $pedido['id_pedido'] ?>" class="btn-primary" style="font-size:13px;padding:0.45rem 0.9rem">
      <svg class="icon-sm" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      Registrar pago
    </a>
  </div>
</div>
<?php endif; ?>

<div style="margin-top:0.75rem">
  <a href="/pedidos/historial?fecha=<?= date('Y-m-d', strtotime($pedido['fecha_cierre'])) ?>" class="btn-ghost" style="margin-right:0.5rem">Ver historial del día</a>
  <a href="/pedidos" class="btn-secondary">← Volver a pedidos</a>
</div>
<?php endif; ?>

<!-- Modal agregar producto -->
<?php if (!$pedido['fecha_cierre']): ?>
<div id="modal-add" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box">
    <button class="modal-close" onclick="document.getElementById('modal-add').classList.remove('open')">&times;</button>
    <h2>Añadir producto al pedido</h2>

    <form method="post" action="/pedidos/agregar-detalle/<?= $pedido['id_pedido'] ?>">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="modal-id-producto" class="form-label">Producto <span class="req">*</span></label>
        <select name="id_producto" id="modal-id-producto" class="form-control" required onchange="modalPrecioPreview()">
          <option value="">— Seleccioná un producto —</option>
          <?php foreach ($productosDisponibles ?? [] as $prod): ?>
            <option value="<?= $prod['id_producto'] ?>" data-precio="<?= $prod['precio_venta'] ?>">
              <?= esc($prod['nombre']) ?> — $<?= number_format((float)$prod['precio_venta'], 2, ',', '.') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div id="modal-precio-preview"></div>
      </div>

      <div class="form-group">
        <label for="modal-cantidad" class="form-label">Cantidad <span class="req">*</span></label>
        <input type="number" name="cantidad" id="modal-cantidad" class="form-control" value="1" min="0.5" step="0.5" required oninput="modalPrecioPreview()">
      </div>

      <div class="form-group" style="margin-bottom:0">
        <label for="modal-obs" class="form-label">Observaciones</label>
        <input type="text" name="observaciones" id="modal-obs" class="form-control" placeholder="Ej: Sin sal, extra hielo…" maxlength="255">
      </div>

      <div style="display:flex;gap:0.75rem;margin-top:1.25rem;">
        <button type="submit" class="btn-primary" style="flex:1">Añadir</button>
        <button type="button" class="btn-secondary" onclick="document.getElementById('modal-add').classList.remove('open')">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
function modalPrecioPreview() {
  var sel  = document.getElementById('modal-id-producto');
  var qty  = parseFloat(document.getElementById('modal-cantidad').value) || 1;
  var prev = document.getElementById('modal-precio-preview');
  if (!sel.value) { prev.textContent = ''; return; }
  var precio   = parseFloat(sel.options[sel.selectedIndex].getAttribute('data-precio'));
  var subtotal = precio * qty;
  prev.textContent = '$' + precio.toFixed(2).replace('.', ',') + ' c/u · Subtotal: $' + subtotal.toFixed(2).replace('.', ',');
}
</script>
<?php endif; ?>

<?= $this->endSection() ?>
