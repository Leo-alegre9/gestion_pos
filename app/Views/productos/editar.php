<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/productos" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header">
  <h1>Editar Producto</h1>
  <p><?= esc($producto['nombre']) ?></p>
</div>

<div class="form-card">
  <form method="post" action="/productos/actualizar/<?= $producto['id_producto'] ?>">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="nombre" class="form-label">Nombre <span class="req">*</span></label>
      <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control <?= !empty($formErrors['nombre']) ? 'is-error' : '' ?>"
        value="<?= old('nombre', $producto['nombre']) ?>"
        maxlength="120"
        required
      >
      <?php if (!empty($formErrors['nombre'])): ?>
        <div class="field-error"><?= esc($formErrors['nombre']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="id_categoria" class="form-label">Categoría <span class="req">*</span></label>
      <select id="id_categoria" name="id_categoria" class="form-control <?= !empty($formErrors['id_categoria']) ? 'is-error' : '' ?>" required>
        <option value="">— Seleccioná una categoría —</option>
        <?php foreach ($categorias ?? [] as $cat): ?>
          <option value="<?= $cat['id_categoria'] ?>" <?= old('id_categoria', $producto['id_categoria']) == $cat['id_categoria'] ? 'selected' : '' ?>>
            <?= esc($cat['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($formErrors['id_categoria'])): ?>
        <div class="field-error"><?= esc($formErrors['id_categoria']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="precio_venta" class="form-label">Precio de venta ($) <span class="req">*</span></label>
      <input
        type="number"
        id="precio_venta"
        name="precio_venta"
        class="form-control <?= !empty($formErrors['precio_venta']) ? 'is-error' : '' ?>"
        value="<?= old('precio_venta', $producto['precio_venta']) ?>"
        step="0.01"
        min="0.01"
        required
      >
      <?php if (!empty($formErrors['precio_venta'])): ?>
        <div class="field-error"><?= esc($formErrors['precio_venta']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="descripcion" class="form-label">Descripción</label>
      <textarea
        id="descripcion"
        name="descripcion"
        class="form-control"
        maxlength="255"
      ><?= old('descripcion', $producto['descripcion']) ?></textarea>
    </div>

    <div class="form-group">
      <label class="form-check">
        <input type="checkbox" name="se_vende_en_barra" value="1" <?= old('se_vende_en_barra', $producto['se_vende_en_barra']) ? 'checked' : '' ?>>
        Se vende en barra
      </label>
    </div>

    <div class="form-group">
      <label class="form-check">
        <input type="checkbox" name="controla_stock" value="1" <?= old('controla_stock', $producto['controla_stock']) ? 'checked' : '' ?>>
        Controla stock
      </label>
    </div>

    <div class="form-group">
      <label class="form-check">
        <input type="checkbox" name="activo" value="1" <?= old('activo', $producto['activo']) ? 'checked' : '' ?>>
        Producto activo
      </label>
      <div class="form-hint" style="margin-left:1.5rem">Los productos inactivos no aparecen en la toma de pedidos.</div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">
        <svg class="icon-sm" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Guardar Cambios
      </button>
      <a href="/productos" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
