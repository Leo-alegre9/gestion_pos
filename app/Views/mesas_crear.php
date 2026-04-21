<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/mesas" class="btn-ghost">← Volver</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $formErrors = session()->getFlashdata('errors') ?? []; ?>

<div class="page-header">
  <h1>Nueva Mesa</h1>
  <p>Agregá una nueva mesa al sistema.</p>
</div>

<div class="form-card">
  <div class="info-box" style="margin-bottom:1.25rem">
    La mesa iniciará con estado <strong>Libre</strong> automáticamente.
  </div>

  <form method="post" action="/mesas/guardar">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="numero" class="form-label">Número de Mesa <span class="req">*</span></label>
      <input
        type="number"
        id="numero"
        name="numero"
        class="form-control <?= !empty($formErrors['numero']) ? 'is-error' : '' ?>"
        value="<?= old('numero', $proximoNumero ?? '') ?>"
        min="1"
        max="999"
        required
      >
      <div class="form-hint">Identificador único de la mesa (ej: 1, 2, 3…)</div>
      <?php if (!empty($formErrors['numero'])): ?>
        <div class="field-error"><?= esc($formErrors['numero']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="capacidad" class="form-label">Capacidad</label>
      <select id="capacidad" name="capacidad" class="form-control <?= !empty($formErrors['capacidad']) ? 'is-error' : '' ?>">
        <option value="">— Opcional —</option>
        <?php foreach ([2,4,6,8,10,12] as $cap): ?>
          <option value="<?= $cap ?>" <?= old('capacidad') == $cap ? 'selected' : '' ?>><?= $cap ?> personas</option>
        <?php endforeach; ?>
      </select>
      <div class="form-hint">Cantidad de personas que puede acomodar.</div>
      <?php if (!empty($formErrors['capacidad'])): ?>
        <div class="field-error"><?= esc($formErrors['capacidad']) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">
        <svg class="icon-sm" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Crear Mesa
      </button>
      <a href="/mesas" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<?= $this->endSection() ?>
