<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/categorias" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header">
  <h1>Nueva Categoría</h1>
  <p>Agrega una nueva categoría para organizar los productos del catálogo.</p>
</div>

<div class="form-card">
  <form method="post" action="/categorias/guardar">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="nombre" class="form-label">Nombre <span class="req">*</span></label>
      <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control <?= !empty($formErrors['nombre']) ? 'is-error' : '' ?>"
        value="<?= old('nombre') ?>"
        placeholder="Ej: Bebidas, Comidas, Cócteles"
        maxlength="100"
        required
      >
      <?php if (!empty($formErrors['nombre'])): ?>
        <div class="field-error"><?= esc($formErrors['nombre']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="descripcion" class="form-label">Descripción</label>
      <textarea
        id="descripcion"
        name="descripcion"
        class="form-control <?= !empty($formErrors['descripcion']) ? 'is-error' : '' ?>"
        placeholder="Descripción opcional de la categoría"
        maxlength="255"
      ><?= old('descripcion') ?></textarea>
      <div class="form-hint">Máx. 255 caracteres. Campo opcional.</div>
      <?php if (!empty($formErrors['descripcion'])): ?>
        <div class="field-error"><?= esc($formErrors['descripcion']) ?></div>
      <?php endif; ?>
    </div>

    <div class="info-box">
      La categoría se creará como <strong>Activa</strong> y estará disponible para asignar productos de inmediato.
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">
        <svg class="icon-sm" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Crear Categoría
      </button>
      <a href="/categorias" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
