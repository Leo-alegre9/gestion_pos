<?= $this->extend('layouts/app') ?>

<?= $this->section('page_styles') ?>
<style>
  .info-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(155px,1fr)); gap:1rem; margin-bottom:1.5rem; }
  .info-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1rem; }
  .info-card-label { font-size:10.5px; font-weight:500; letter-spacing:0.06em; text-transform:uppercase; color:var(--text-tertiary); margin-bottom:0.35rem; }
  .info-card-value { font-size:15px; font-weight:500; color:var(--text-primary); }
  .info-card-sub   { font-size:12px; color:var(--text-tertiary); margin-top:0.15rem; }
  .items-tfoot th  { font-size:14px; font-weight:600; color:var(--text-primary); padding:0.8rem 1rem; border-top:2px solid var(--border); }
  .pay-grid { display:grid; grid-template-columns:1fr 340px; gap:1.25rem; align-items:start; }
  @media (max-width:900px) { .pay-grid { grid-template-columns:1fr; } }
  .metodo-option {
    display:flex; align-items:center; gap:0.75rem;
    padding:0.75rem 1rem;
    border:2px solid var(--border); border-radius:var(--radius-md);
    cursor:pointer; transition:border-color .15s, background .15s;
    margin-bottom:0.5rem;
  }
  .metodo-option:last-child { margin-bottom:0; }
  .metodo-option input[type="radio"] { display:none; }
  .metodo-option-label { font-size:14px; font-weight:500; color:var(--text-secondary); }
  .metodo-option.selected {
    border-color:var(--purple);
    background:rgba(124,58,237,0.06);
  }
  .metodo-option.selected .metodo-option-label { color:var(--purple); }
  .monto-display { font-size:28px; font-weight:600; color:var(--purple); font-variant-numeric:tabular-nums; letter-spacing:-0.03em; }
  .vuelto-row { margin-top:0.75rem; padding:0.75rem 1rem; background:rgba(34,197,94,0.07); border-radius:var(--radius-md); display:none; }
  .vuelto-row.visible { display:flex; justify-content:space-between; align-items:center; }
</style>
<?= $this->endSection() ?>

<?= $this->section('topbar_right') ?>
<a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" class="btn-ghost">← Pedido</a>
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
      <div class="info-card-sub">Mesa <?= $pedido['numero_mesa'] ?></div>
    <?php endif; ?>
  </div>
  <div class="info-card">
    <div class="info-card-label">Cerrado</div>
    <div class="info-card-value"><?= date('d/m/Y', strtotime($pedido['fecha_cierre'])) ?></div>
    <div class="info-card-sub"><?= date('H:i', strtotime($pedido['fecha_cierre'])) ?></div>
  </div>
  <div class="info-card">
    <div class="info-card-label">Total a cobrar</div>
    <div class="info-card-value mono" style="font-size:18px;color:var(--purple)">$<?= number_format($total, 1, ',', '.') ?></div>
  </div>
</div>

<div class="pay-grid">

  <!-- Resumen de items -->
  <div class="panel" style="margin-bottom:0">
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
            <td class="mono" style="text-align:center"><?= number_format((float)$item['cantidad'], 0, ',', '.') ?></td>
            <td class="mono" style="text-align:right;color:var(--text-secondary)">$<?= number_format((float)$item['precio_unitario'], 1, ',', '.') ?></td>
            <td class="mono" style="text-align:right;font-weight:500">$<?= number_format((float)$item['subtotal'], 1, ',', '.') ?></td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--text-tertiary);font-size:13px">Sin productos registrados.</td></tr>
        <?php endif; ?>
      </tbody>
      <tfoot class="items-tfoot">
        <tr>
          <th colspan="3" style="text-align:right;font-size:13px;color:var(--text-secondary);font-weight:500">Total</th>
          <th class="mono" style="text-align:right;font-size:20px;color:var(--purple)">$<?= number_format($total, 1, ',', '.') ?></th>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Formulario de pago -->
  <div>
    <div class="form-card" style="margin-bottom:0">
      <div style="font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-tertiary);margin-bottom:1.25rem">Registrar Pago</div>

      <?php if (empty($metodos)): ?>
        <div class="info-box" style="border-left-color:#f59e0b;background:rgba(245,158,11,0.06)">
          No hay métodos de pago configurados. Agregá al menos uno en la base de datos (<code>metodos_pago</code>) para continuar.
        </div>
      <?php else: ?>

      <form method="post" action="/pagos/registrar/<?= $pedido['id_pedido'] ?>" id="form-pago">
        <?= csrf_field() ?>

        <!-- Método de pago -->
        <div class="form-group">
          <label class="form-label">Método de pago <span class="req">*</span></label>
          <?php if (!empty(session()->getFlashdata('errors')['id_metodo_pago'])): ?>
            <div class="field-error"><?= esc(session()->getFlashdata('errors')['id_metodo_pago']) ?></div>
          <?php endif; ?>
          <div id="metodos-wrap">
            <?php foreach ($metodos as $i => $m): ?>
            <label class="metodo-option<?= $i === 0 ? ' selected' : '' ?>" id="metodo-label-<?= $m['id_metodo_pago'] ?>">
              <input type="radio" name="id_metodo_pago" value="<?= $m['id_metodo_pago'] ?>"
                <?= (old('id_metodo_pago', $i === 0 ? $m['id_metodo_pago'] : '') == $m['id_metodo_pago']) ? 'checked' : '' ?>
                onchange="selectMetodo(this)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
              </svg>
              <span class="metodo-option-label"><?= esc($m['nombre']) ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Monto recibido -->
        <div class="form-group">
          <label for="monto" class="form-label">Monto recibido <span class="req">*</span></label>
          <input type="number" id="monto" name="monto" class="form-control"
            value="<?= old('monto', number_format($total, 1, '.', '')) ?>"
            min="0.01" step="0.01" required
            oninput="calcVuelto()">
          <div style="font-size:11.5px;color:var(--text-tertiary);margin-top:0.25rem">
            Total del pedido: <strong>$<?= number_format($total, 1, ',', '.') ?></strong>
          </div>
          <?php if (!empty(session()->getFlashdata('errors')['monto'])): ?>
            <div class="field-error"><?= esc(session()->getFlashdata('errors')['monto']) ?></div>
          <?php endif; ?>
        </div>

        <!-- Vuelto -->
        <div id="vuelto-row" class="vuelto-row">
          <span style="font-size:13px;color:#15803d;font-weight:500">Vuelto a dar</span>
          <span id="vuelto-val" class="mono" style="font-size:15px;font-weight:600;color:#15803d">$0,00</span>
        </div>

        <div style="margin-top:1.25rem;display:flex;gap:0.65rem">
          <button type="submit" class="btn-primary" style="flex:1">
            <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Confirmar pago
          </button>
          <a href="/pedidos/detalles/<?= $pedido['id_pedido'] ?>" class="btn-secondary">Cancelar</a>
        </div>
      </form>

      <?php endif; ?>
    </div>
  </div>

</div>

<script>
var TOTAL = <?= json_encode((float)$total) ?>;

function selectMetodo(radio) {
  document.querySelectorAll('.metodo-option').forEach(function(el) {
    el.classList.remove('selected');
    el.querySelector('.metodo-option-label').style.color = '';
  });
  radio.closest('.metodo-option').classList.add('selected');
}

function calcVuelto() {
  var monto  = parseFloat(document.getElementById('monto').value) || 0;
  var vuelto = monto - TOTAL;
  var row    = document.getElementById('vuelto-row');
  var val    = document.getElementById('vuelto-val');
  if (vuelto > 0.001) {
    row.classList.add('visible');
    val.textContent = '$' + vuelto.toFixed(1).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  } else {
    row.classList.remove('visible');
  }
}

// Init: select first radio visually
document.addEventListener('DOMContentLoaded', function() {
  var first = document.querySelector('input[name="id_metodo_pago"]:checked');
  if (first) selectMetodo(first);
  calcVuelto();
});
</script>

<?= $this->endSection() ?>
