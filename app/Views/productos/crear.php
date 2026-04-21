<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/productos" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header">
  <h1>Nuevo Producto</h1>
  <p>Agregá un nuevo ítem al catálogo del bar.</p>
</div>

<div class="form-card">

  <?php if (empty($categorias)): ?>
  <div class="info-box" style="border-left-color:#f59e0b;background:rgba(245,158,11,0.06)">
    No hay categorías activas. <a href="/categorias/crear" style="color:#7c3aed;font-weight:500">Creá una categoría</a> antes de agregar productos.
  </div>
  <?php endif; ?>

  <form method="post" action="/productos/guardar">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="nombre" class="form-label">Nombre <span class="req">*</span></label>
      <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control <?= !empty($formErrors['nombre']) ? 'is-error' : '' ?>"
        value="<?= old('nombre') ?>"
        placeholder="Ej: Cerveza IPA 1L"
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
          <option value="<?= $cat['id_categoria'] ?>" <?= old('id_categoria') == $cat['id_categoria'] ? 'selected' : '' ?>>
            <?= esc($cat['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($formErrors['id_categoria'])): ?>
        <div class="field-error"><?= esc($formErrors['id_categoria']) ?></div>
      <?php endif; ?>
      <div class="form-hint">¿Falta una categoría? <a href="/categorias/crear" style="color:#7c3aed">Crear nueva →</a></div>
    </div>

    <div class="form-group">
      <label for="precio_venta" class="form-label">Precio de venta ($) <span class="req">*</span></label>
      <input
        type="number"
        id="precio_venta"
        name="precio_venta"
        class="form-control <?= !empty($formErrors['precio_venta']) ? 'is-error' : '' ?>"
        value="<?= old('precio_venta') ?>"
        placeholder="0.00"
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
        placeholder="Descripción opcional del producto"
        maxlength="255"
      ><?= old('descripcion') ?></textarea>
      <div class="form-hint">Máx. 255 caracteres.</div>
    </div>

    <div class="form-group">
      <label class="form-check">
        <input type="checkbox" name="se_vende_en_barra" value="1" <?= old('se_vende_en_barra') ? 'checked' : '' ?>>
        Se vende en barra
      </label>
      <div class="form-hint" style="margin-left:1.5rem">Marcá si este producto se despacha desde la barra.</div>
    </div>

    <div class="form-group">
      <label class="form-check">
        <input type="checkbox" name="controla_stock" value="1" <?= old('controla_stock', 1) ? 'checked' : '' ?>>
        Controla stock
      </label>
      <div class="form-hint" style="margin-left:1.5rem">Desmarcá si el producto no requiere seguimiento de inventario.</div>
    </div>

    <div class="info-box">
      El producto se creará como <strong>Activo</strong> de manera automática.
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">
        <svg class="icon-sm" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Crear Producto
      </button>
      <a href="/productos" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
