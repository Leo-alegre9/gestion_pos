<?= $this->extend('layouts/app') ?>

<?= $this->section('page_styles') ?>
<style>
  .crear-grid { display:grid; grid-template-columns:340px 1fr; gap:1.25rem; align-items:start; }
  @media (max-width:900px) { .crear-grid { grid-template-columns:1fr; } }

  .cart-row { display:flex; align-items:center; gap:0.5rem; padding:0.6rem 0.75rem; border-bottom:1px solid var(--border); }
  .cart-row:last-child { border-bottom:none; }
  .cart-row-name { flex:1; font-weight:500; font-size:13.5px; color:var(--text-primary); }
  .cart-row-qty  { width:60px; text-align:center; font-variant-numeric:tabular-nums; font-size:13px; color:var(--text-secondary); }
  .cart-row-sub  { width:90px; text-align:right; font-size:13.5px; font-weight:500; color:var(--text-primary); font-variant-numeric:tabular-nums; }
  .cart-row-del  { flex-shrink:0; }

  #prod-precio-preview { font-size:12px; color:var(--text-tertiary); min-height:18px; margin-top:0.25rem; }
  .cart-empty-msg { text-align:center; padding:1.75rem 0; color:var(--text-tertiary); font-size:13px; }
  .total-bar { display:flex; justify-content:space-between; align-items:center; padding:0.75rem 0.75rem 0; border-top:2px solid var(--border); margin-top:0.25rem; }
  .total-bar span { font-size:12px; color:var(--text-secondary); font-weight:500; text-transform:uppercase; letter-spacing:0.05em; }
  .total-bar strong { font-size:18px; font-weight:600; color:var(--purple,#7c3aed); font-variant-numeric:tabular-nums; }
</style>
<?= $this->endSection() ?>

<?= $this->section('topbar_right') ?>
<a href="/pedidos" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header" style="margin-bottom:1.25rem">
  <h1>Nuevo Pedido</h1>
  <p>Completá el tipo de pedido, la mesa si corresponde, y sumá los productos antes de confirmar.</p>
</div>

<form method="post" action="/pedidos/guardar" id="form-pedido">
  <?= csrf_field() ?>

  <div class="crear-grid">

    <!-- ── Panel izquierdo: cabecera del pedido ── -->
    <div class="form-card" style="margin-bottom:0">
      <div style="font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-tertiary);margin-bottom:1rem">Información</div>

      <div class="form-group">
        <label class="form-label">Tipo de pedido <span class="req">*</span></label>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem">
          <?php
            $tipos = [
              'mesa'     => ['label'=>'Mesa',       'icon'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>'],
              'barra'    => ['label'=>'Barra',      'icon'=>'<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
              'take_away'=> ['label'=>'Para llevar','icon'=>'<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>'],
            ];
            $oldTipo = old('tipo_pedido');
          ?>
          <?php foreach ($tipos as $val => $t): ?>
          <label style="display:flex;flex-direction:column;align-items:center;gap:0.3rem;padding:0.65rem 0.5rem;border:2px solid var(--border);border-radius:var(--radius-md);cursor:pointer;transition:border-color .15s,background .15s;font-size:12.5px;font-weight:500;color:var(--text-secondary)" class="tipo-label" data-val="<?= $val ?>">
            <input type="radio" name="tipo_pedido" value="<?= $val ?>" style="display:none" <?= $oldTipo === $val ? 'checked' : '' ?> onchange="onTipoChange()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><?= $t['icon'] ?></svg>
            <?= $t['label'] ?>
          </label>
          <?php endforeach; ?>
        </div>
        <?php if (!empty($formErrors['tipo_pedido'])): ?>
          <div class="field-error"><?= esc($formErrors['tipo_pedido']) ?></div>
        <?php endif; ?>
      </div>

      <div id="mesa-section" class="form-group" style="display:none">
        <label for="id_mesa" class="form-label">Mesa <span class="req">*</span></label>
        <select id="id_mesa" name="id_mesa" class="form-control <?= !empty($formErrors['id_mesa']) ? 'is-error' : '' ?>">
          <option value="">— Seleccioná —</option>
          <?php foreach ($mesas ?? [] as $m): ?>
            <option value="<?= $m['id_mesa'] ?>" <?= old('id_mesa') == $m['id_mesa'] ? 'selected' : '' ?>>
              Mesa <?= $m['numero'] ?><?= $m['capacidad'] ? ' · ' . $m['capacidad'] . ' pers.' : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($mesas)): ?>
          <div class="form-hint" style="color:#f59e0b">Sin mesas libres. <a href="/mesas" style="color:#7c3aed">Gestionar →</a></div>
        <?php endif; ?>
        <?php if (!empty($formErrors['id_mesa'])): ?>
          <div class="field-error"><?= esc($formErrors['id_mesa']) ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group" style="margin-bottom:0">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea id="observaciones" name="observaciones" class="form-control" rows="3" maxlength="255" placeholder="Notas del pedido (opcional)"><?= esc(old('observaciones')) ?></textarea>
      </div>
    </div>

    <!-- ── Panel derecho: productos ── -->
    <div class="form-card" style="margin-bottom:0">
      <div style="font-size:12px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--text-tertiary);margin-bottom:1rem">Productos</div>

      <?php if (empty($productos)): ?>
        <div class="info-box" style="border-left-color:#f59e0b;background:rgba(245,158,11,0.06)">
          Sin productos activos. <a href="/productos/crear" style="color:#7c3aed;font-weight:500">Crear producto →</a>
        </div>
      <?php else: ?>

      <!-- Selector de producto -->
      <div style="display:grid;grid-template-columns:1fr 72px auto;gap:0.5rem;align-items:end;margin-bottom:0.25rem">
        <div>
          <label class="form-label" style="margin-bottom:0.3rem">Producto</label>
          <select id="prod-select" class="form-control" onchange="onProdChange()">
            <option value="">— Seleccioná —</option>
            <?php foreach ($productos as $p): ?>
              <option
                value="<?= $p['id_producto'] ?>"
                data-precio="<?= $p['precio_venta'] ?>"
                data-nombre="<?= esc($p['nombre']) ?>"
                data-cat="<?= esc($p['categoria_nombre'] ?? '') ?>">
                <?= esc($p['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label" style="margin-bottom:0.3rem">Cant.</label>
          <input type="number" id="prod-qty" class="form-control" value="1" min="0.5" step="0.5" style="text-align:center">
        </div>
        <div style="padding-bottom:1px">
          <label class="form-label" style="margin-bottom:0.3rem;visibility:hidden">+</label>
          <button type="button" class="btn-primary" onclick="agregarItem()" style="padding:0.5rem 0.9rem;white-space:nowrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Añadir
          </button>
        </div>
      </div>
      <div id="prod-precio-preview"></div>

      <!-- Carrito -->
      <div style="margin-top:1rem;border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;min-height:80px" id="cart-wrap">
        <div id="cart-empty-msg" class="cart-empty-msg">
          Aún no agregaste productos al pedido.
        </div>
        <div id="cart-list" style="display:none"></div>
        <div id="cart-total-bar" class="total-bar" style="display:none">
          <span>Total estimado</span>
          <strong id="cart-total">$0,00</strong>
        </div>
      </div>

      <!-- Inputs ocultos generados por JS -->
      <div id="cart-inputs"></div>

      <?php endif; ?>
    </div>
  </div>

  <!-- Acciones -->
  <div class="form-actions" style="margin-top:1.25rem">
    <button type="submit" class="btn-primary" id="btn-submit">
      <svg class="icon-sm" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Confirmar Pedido
    </button>
    <a href="/pedidos" class="btn-secondary">Cancelar</a>
  </div>
</form>

<script>
// ── Datos de productos desde PHP ──
var PRODUCTOS = <?= json_encode(
  array_map(fn($p) => [
    'id'       => (int)$p['id_producto'],
    'nombre'   => $p['nombre'],
    'precio'   => (float)$p['precio_venta'],
    'categoria'=> $p['categoria_nombre'] ?? '',
  ], $productos ?? []),
  JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;

var cart = []; // [{id, nombre, precio, cantidad, subtotal}]

// ── Tipo de pedido ──
function onTipoChange() {
  var radios = document.querySelectorAll('input[name="tipo_pedido"]');
  var val = '';
  radios.forEach(function(r) {
    var lbl = r.closest('.tipo-label');
    if (r.checked) {
      val = r.value;
      lbl.style.borderColor = 'var(--purple,#7c3aed)';
      lbl.style.background  = 'rgba(124,58,237,0.06)';
      lbl.style.color       = 'var(--purple,#7c3aed)';
    } else {
      lbl.style.borderColor = 'var(--border)';
      lbl.style.background  = '';
      lbl.style.color       = 'var(--text-secondary)';
    }
  });
  var sec = document.getElementById('mesa-section');
  var sel = document.getElementById('id_mesa');
  if (val === 'mesa') {
    sec.style.display = 'block';
    sel.required = true;
  } else {
    sec.style.display = 'none';
    sel.required = false;
    sel.value = '';
  }
}

// ── Preview precio al seleccionar producto ──
function onProdChange() {
  var sel  = document.getElementById('prod-select');
  var prev = document.getElementById('prod-precio-preview');
  if (!sel.value) { prev.textContent = ''; return; }
  var opt  = sel.options[sel.selectedIndex];
  var precio = parseFloat(opt.getAttribute('data-precio'));
  prev.textContent = '$' + fmtNum(precio) + ' c/u' + (opt.getAttribute('data-cat') ? ' · ' + opt.getAttribute('data-cat') : '');
}

// ── Agregar item al carrito ──
function agregarItem() {
  var sel = document.getElementById('prod-select');
  var qty = parseFloat(document.getElementById('prod-qty').value);

  if (!sel.value) { sel.focus(); return; }
  if (!(qty > 0)) { document.getElementById('prod-qty').focus(); return; }

  var opt    = sel.options[sel.selectedIndex];
  var id     = parseInt(sel.value);
  var nombre = opt.getAttribute('data-nombre');
  var precio = parseFloat(opt.getAttribute('data-precio'));

  var existing = cart.find(function(i) { return i.id === id; });
  if (existing) {
    existing.cantidad += qty;
    existing.subtotal  = roundDec(existing.cantidad * existing.precio);
  } else {
    cart.push({ id: id, nombre: nombre, precio: precio, cantidad: qty, subtotal: roundDec(precio * qty) });
  }

  renderCart();
  sel.value = '';
  document.getElementById('prod-qty').value = 1;
  document.getElementById('prod-precio-preview').textContent = '';
}

// ── Quitar item ──
function quitarItem(id) {
  cart = cart.filter(function(i) { return i.id !== id; });
  renderCart();
}

// ── Renderizar carrito ──
function renderCart() {
  var list   = document.getElementById('cart-list');
  var empty  = document.getElementById('cart-empty-msg');
  var inputs = document.getElementById('cart-inputs');
  var totBar = document.getElementById('cart-total-bar');
  var totEl  = document.getElementById('cart-total');

  list.innerHTML  = '';
  inputs.innerHTML = '';

  if (cart.length === 0) {
    list.style.display   = 'none';
    empty.style.display  = 'block';
    totBar.style.display = 'none';
    return;
  }

  var total = 0;
  cart.forEach(function(item, idx) {
    total += item.subtotal;
    var row = document.createElement('div');
    row.className = 'cart-row';
    row.innerHTML =
      '<span class="cart-row-name">' + escHtml(item.nombre) + '</span>' +
      '<span class="cart-row-qty">× ' + fmtCant(item.cantidad) + '</span>' +
      '<span class="cart-row-sub">$' + fmtNum(item.subtotal) + '</span>' +
      '<span class="cart-row-del"><button type="button" onclick="quitarItem(' + item.id + ')" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:18px;line-height:1;padding:2px 4px" title="Quitar">×</button></span>';
    list.appendChild(row);

    inputs.innerHTML +=
      '<input type="hidden" name="items[' + idx + '][id_producto]" value="' + item.id + '">' +
      '<input type="hidden" name="items[' + idx + '][cantidad]" value="' + item.cantidad + '">';
  });

  totEl.textContent    = '$' + fmtNum(total);
  list.style.display   = 'block';
  empty.style.display  = 'none';
  totBar.style.display = 'flex';
}

// ── Helpers ──
function fmtNum(n) {
  return n.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function fmtCant(n) {
  return n % 1 === 0 ? n.toFixed(0) : n.toFixed(2).replace('.', ',');
}
function roundDec(n) { return Math.round(n * 100) / 100; }
function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Init ──
onTipoChange();
</script>

<?= $this->endSection() ?>
