<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/inventario" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header">
  <h1>Editar Stock</h1>
  <p><?= esc($stock['producto_nombre']) ?></p>
</div>

<div class="form-card">
  <form method="post" action="/inventario/actualizar/<?= $stock['id_stock'] ?>">
    <?= csrf_field() ?>

    <div class="form-group">
      <label class="form-label">Producto</label>
      <input type="text" class="form-control" value="<?= esc($stock['producto_nombre']) ?>" disabled style="background:var(--bg);color:var(--text-tertiary)">
    </div>

    <div class="form-group">
      <label for="cantidad_disponible" class="form-label">Cantidad disponible <span class="req">*</span></label>
      <input
        type="number"
        id="cantidad_disponible"
        name="cantidad_disponible"
        class="form-control <?= !empty($formErrors['cantidad_disponible']) ? 'is-error' : '' ?>"
        value="<?= old('cantidad_disponible', (int)$stock['cantidad_disponible']) ?>"
        min="0"
        step="1"
        required
      >
      <div class="form-hint">Unidades actuales en existencia.</div>
      <?php if (!empty($formErrors['cantidad_disponible'])): ?>
        <div class="field-error"><?= esc($formErrors['cantidad_disponible']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="cantidad_minima" class="form-label">Cantidad mínima <span class="req">*</span></label>
      <input
        type="number"
        id="cantidad_minima"
        name="cantidad_minima"
        class="form-control <?= !empty($formErrors['cantidad_minima']) ? 'is-error' : '' ?>"
        value="<?= old('cantidad_minima', (int)$stock['cantidad_minima']) ?>"
        min="0"
        step="1"
        required
      >
      <div class="form-hint">Umbral de alerta de stock bajo.</div>
      <?php if (!empty($formErrors['cantidad_minima'])): ?>
        <div class="field-error"><?= esc($formErrors['cantidad_minima']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">
        <svg class="icon-sm" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Guardar Cambios
      </button>
      <a href="/inventario" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
