<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/inventario" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header">
  <h1>Nuevo Registro de Stock</h1>
  <p>Asigná un nivel de stock inicial a un producto.</p>
</div>

<div class="form-card">

  <?php if (empty($productos)): ?>
  <div class="info-box" style="border-left-color:#f59e0b;background:rgba(245,158,11,0.06)">
    No hay productos con control de stock disponibles para registrar.
  </div>
  <?php endif; ?>

  <form method="post" action="/inventario/guardar">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="id_producto" class="form-label">Producto <span class="req">*</span></label>
      <select id="id_producto" name="id_producto" class="form-control <?= !empty($formErrors['id_producto']) ? 'is-error' : '' ?>" required>
        <option value="">— Seleccioná un producto —</option>
        <?php foreach ($productos ?? [] as $prod): ?>
          <option value="<?= $prod['id_producto'] ?>" <?= old('id_producto') == $prod['id_producto'] ? 'selected' : '' ?>>
            <?= esc($prod['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($formErrors['id_producto'])): ?>
        <div class="field-error"><?= esc($formErrors['id_producto']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="cantidad_disponible" class="form-label">Cantidad disponible <span class="req">*</span></label>
      <input
        type="number"
        id="cantidad_disponible"
        name="cantidad_disponible"
        class="form-control <?= !empty($formErrors['cantidad_disponible']) ? 'is-error' : '' ?>"
        value="<?= old('cantidad_disponible', '0') ?>"
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
        value="<?= old('cantidad_minima', '0') ?>"
        min="0"
        step="1"
        required
      >
      <div class="form-hint">Umbral de alerta. Si el stock disponible cae por debajo de este valor, se genera una alerta.</div>
      <?php if (!empty($formErrors['cantidad_minima'])): ?>
        <div class="field-error"><?= esc($formErrors['cantidad_minima']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">
        <svg class="icon-sm" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Crear Registro
      </button>
      <a href="/inventario" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
