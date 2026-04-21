<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/categorias" class="btn-ghost">Categorías</a>
<a href="/productos/crear" class="btn-pill">
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
  Nuevo producto
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header-row">
  <h1>Productos</h1>
</div>

<div class="panel">
  <?php if (!empty($productos)): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Categoría</th>
        <th style="text-align:right">Precio</th>
        <th style="text-align:center">Barra</th>
        <th style="text-align:center">Stock</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($productos as $p): ?>
      <tr>
        <td>
          <div style="font-weight:500;color:var(--text-primary)"><?= esc($p['nombre']) ?></div>
          <?php if (!empty($p['descripcion'])): ?>
            <div style="font-size:11.5px;color:var(--text-tertiary);margin-top:1px"><?= esc(substr($p['descripcion'], 0, 55)) ?><?= strlen($p['descripcion']) > 55 ? '…' : '' ?></div>
          <?php endif; ?>
        </td>
        <td><span class="badge badge-violet"><?= esc($p['categoria_nombre'] ?? '—') ?></span></td>
        <td class="mono" style="text-align:right;color:var(--text-primary);font-weight:500">$<?= number_format((float)$p['precio_venta'], 2, ',', '.') ?></td>
        <td style="text-align:center">
          <?= $p['se_vende_en_barra'] ? '<span class="badge badge-green">Sí</span>' : '<span style="color:var(--text-tertiary);font-size:12px">No</span>' ?>
        </td>
        <td style="text-align:center">
          <?= $p['controla_stock'] ? '<span class="badge badge-blue">Sí</span>' : '<span style="color:var(--text-tertiary);font-size:12px">No</span>' ?>
        </td>
        <td>
          <?= $p['activo'] ? '<span class="badge badge-green">Activo</span>' : '<span class="badge badge-gray">Inactivo</span>' ?>
        </td>
        <td>
          <div style="display:flex;gap:0.4rem;">
            <a href="/productos/editar/<?= $p['id_producto'] ?>" class="action-link action-edit">
              <svg class="icon-sm" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Editar
            </a>
            <?php if ($p['activo']): ?>
            <form method="post" action="/productos/desactivar/<?= $p['id_producto'] ?>" style="display:inline;" onsubmit="return confirm('¿Desactivar «<?= esc($p['nombre']) ?>»?')">
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
      <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
    </svg>
    <p>No hay productos en el catálogo todavía.</p>
    <a href="/productos/crear" class="btn-pill">Crear primer producto</a>
  </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
