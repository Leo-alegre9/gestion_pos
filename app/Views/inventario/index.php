<?= $this->extend('layouts/app') ?>

<?= $this->section('topbar_right') ?>
<a href="/inventario/alertas" class="btn-ghost">Ver alertas</a>
<a href="/inventario/crear" class="btn-pill">
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
  Nuevo stock
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header-row">
  <h1>Inventario</h1>
</div>

<div class="panel">
  <?php if (!empty($inventario)): ?>
  <table class="data-table">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Categoría</th>
        <th style="text-align:center">Disponible</th>
        <th style="text-align:center">Mínimo</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($inventario as $item):
        $disp = (float)$item['cantidad_disponible'];
        $min  = (float)$item['cantidad_minima'];
        if ($disp <= 0)           { $nivel = 'Crítico'; $cls = 'badge-red'; $barCls = '#ef4444'; }
        elseif ($disp < $min)     { $nivel = 'Bajo';    $cls = 'badge-amber'; $barCls = '#f59e0b'; }
        else                      { $nivel = 'Normal';  $cls = 'badge-green'; $barCls = '#22c55e'; }
        $base = $min > 0 ? $min * 2 : 1;
        $pct  = min(round(($disp / $base) * 100), 100);
      ?>
      <tr>
        <td style="font-weight:500;color:var(--text-primary)"><?= esc($item['producto_nombre']) ?></td>
        <td><span class="badge badge-violet"><?= esc($item['categoria_nombre'] ?? '—') ?></span></td>
        <td class="mono" style="text-align:center;font-weight:500"><?= number_format($disp, 2, ',', '.') ?></td>
        <td class="mono" style="text-align:center;color:var(--text-tertiary)"><?= number_format($min, 2, ',', '.') ?></td>
        <td>
          <span class="badge <?= $cls ?>"><?= $nivel ?></span>
          <div style="background:var(--border);height:4px;border-radius:2px;margin-top:5px;overflow:hidden">
            <div style="height:100%;width:<?= $pct ?>%;background:<?= $barCls ?>;border-radius:2px"></div>
          </div>
        </td>
        <td>
          <a href="/inventario/editar/<?= $item['id_stock'] ?>" class="action-link action-edit">
            <svg class="icon-sm" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Editar
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <div class="empty-state">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
      <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
      <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
    </svg>
    <p>No hay registros de stock todavía.</p>
    <a href="/inventario/crear" class="btn-pill">Agregar stock</a>
  </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
