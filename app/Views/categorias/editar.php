<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/categorias" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header">
  <h1>Editar Categoría</h1>
  <p>Modificá los datos de la categoría <strong><?= esc($categoria['nombre']) ?></strong>.</p>
</div>

<div class="form-card">
  <form method="post" action="/categorias/actualizar/<?= $categoria['id_categoria'] ?>">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="nombre" class="form-label">Nombre <span class="req">*</span></label>
      <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control <?= !empty($formErrors['nombre']) ? 'is-error' : '' ?>"
        value="<?= old('nombre', $categoria['nombre']) ?>"
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
      ><?= old('descripcion', $categoria['descripcion']) ?></textarea>
      <div class="form-hint">Máx. 255 caracteres. Campo opcional.</div>
      <?php if (!empty($formErrors['descripcion'])): ?>
        <div class="field-error"><?= esc($formErrors['descripcion']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label class="form-check">
        <input
          type="checkbox"
          name="activa"
          value="1"
          <?= old('activa', $categoria['activa']) ? 'checked' : '' ?>
        >
        Categoría activa
      </label>
      <div class="form-hint">Las categorías inactivas no aparecen al crear o editar productos.</div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">
        <svg class="icon-sm" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Guardar Cambios
      </button>
      <a href="/categorias" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
