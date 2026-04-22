<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/dashboard" class="btn-ghost">← Dashboard</a>
<a href="/categorias/crear" class="btn-pill">
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
  Nueva categoría
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header-row">
  <h1>Categorías de Productos</h1>
  <p style="margin-top:0.25rem;font-size:12px;color:var(--text-tertiary)">Administra las categorías disponibles para los productos</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><polyline points="20 6 9 17 4 12"/></svg>
  <span><?= session()->getFlashdata('success') ?></span>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-error">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <span><?= session()->getFlashdata('error') ?></span>
</div>
<?php endif; ?>

<div class="panel">
  <?php if (!empty($categorias)): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Descripción</th>
        <th style="text-align:center">Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categorias as $cat): ?>
      <tr>
        <td>
          <div style="font-weight:500;color:var(--text-primary)"><?= esc($cat['nombre']) ?></div>
        </td>
        <td>
          <div style="font-size:12px;color:var(--text-tertiary);">
            <?= !empty($cat['descripcion']) ? esc(substr($cat['descripcion'], 0, 60)) . (strlen($cat['descripcion']) > 60 ? '…' : '') : '—' ?>
          </div>
        </td>
        <td style="text-align:center">
          <?= $cat['activa'] ? '<span class="badge badge-green">Activa</span>' : '<span class="badge badge-gray">Inactiva</span>' ?>
        </td>
        <td>
          <div style="display:flex;gap:0.4rem;align-items:center;">
            <a href="/categorias/editar/<?= $cat['id_categoria'] ?>" class="action-link action-edit">
              <svg class="icon-sm" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Editar
            </a>
            <?php if ($cat['activa']): ?>
            <form method="post" action="/categorias/desactivar/<?= $cat['id_categoria'] ?>" style="display:inline;" onsubmit="return confirm('¿Desactivar la categoría «<?= esc($cat['nombre']) ?>»?')">
              <?= csrf_field() ?>
              <button type="submit" class="action-link action-delete">
                <svg class="icon-sm" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Desactivar
              </button>
            </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
      <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
    </svg>
    <p>No hay categorías registradas todavía.</p>
    <a href="/categorias/crear" class="btn-pill">Crear primera categoría</a>
  </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
