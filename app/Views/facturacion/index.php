<?= $this->extend('layouts/app') ?>

<?= $this->section('page_styles') ?>
<style>
  /* ── KPI cards ── */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  @media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2,1fr); } }
  @media (max-width: 640px)  { .kpi-grid { grid-template-columns: 1fr; } }

  .kpi-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
  }
  .kpi-card:hover { border-color: rgba(124,58,237,.35); box-shadow: 0 2px 8px rgba(0,0,0,.04); }
  .kpi-label { font-size: 11.5px; color: var(--text-tertiary); margin-bottom: .45rem; }
  .kpi-value { font-size: 26px; font-weight: 500; letter-spacing: -.03em; color: var(--text-primary); line-height: 1; }
  .kpi-sub   { font-size: 11.5px; color: var(--text-tertiary); margin-top: .4rem; display:flex; align-items:center; gap:.3rem; }
  .kpi-sub.up   { color: #22c55e; }
  .kpi-sub.down { color: #f87171; }
  .kpi-icon {
    position: absolute; right: 1.25rem; top: 1.25rem;
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
  }
  .kpi-icon svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:1.75; stroke-linecap:round; stroke-linejoin:round; }
  .kpi-icon.violet { background: rgba(124,58,237,.15); color: #a78bfa; }
  .kpi-icon.green  { background: rgba(34,197,94,.12);  color: #4ade80; }
  .kpi-icon.amber  { background: rgba(251,191,36,.12); color: #fbbf24; }
  .kpi-icon.blue   { background: rgba(59,130,246,.12); color: #60a5fa; }

  /* ── Main grid ── */
  .fact-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1rem;
    align-items: start;
  }
  @media (max-width: 1024px) { .fact-grid { grid-template-columns: 1fr; } }

  /* ── Método breakdown ── */
  .metodo-row { padding: .65rem 0; border-top: 1px solid var(--border); }
  .metodo-row:first-child { border-top: none; padding-top: 0; }
  .metodo-name  { font-size: 13px; color: var(--text-secondary); margin-bottom: .35rem; display:flex; justify-content:space-between; }
  .metodo-name strong { color: var(--text-primary); font-weight: 500; }
  .metodo-bar-bg { height: 5px; background: var(--border); border-radius: 3px; overflow: hidden; }
  .metodo-bar    { height: 100%; background: linear-gradient(90deg, #7c3aed, #9f7aea); border-radius: 3px; transition: width .4s; }
  .metodo-meta   { font-size: 11px; color: var(--text-tertiary); margin-top: .25rem; }

  /* ── Filtro ── */
  .filter-bar {
    display: flex; align-items: flex-end; gap: .75rem; flex-wrap: wrap;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
  }
  .filter-bar label { font-size: 12px; color: var(--text-tertiary); display:block; margin-bottom:.3rem; }

  /* ── Badges tipo ── */
  .tipo-tag { font-size: 11px; padding: 2px 7px; border-radius: 20px; font-weight:500; }
  .tipo-mesa     { background: rgba(59,130,246,.12); color: #0369a1; }
  .tipo-barra    { background: rgba(124,58,237,.12); color: #7c3aed; }
  .tipo-take_away{ background: rgba(34,197,94,.12);  color: #15803d; }

  @media print {
    .sidebar, .app-topbar, .no-print { display: none !important; }
    .app-main { margin-left: 0 !important; }
    .app-content { padding: 1rem !important; }
    .fact-grid { grid-template-columns: 1fr !important; }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('topbar_right') ?>
<button onclick="window.print()" class="btn-ghost no-print">
  <svg class="icon-sm" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
  Exportar
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Filtro de fecha -->
<div class="filter-bar no-print">
  <div>
    <label for="fecha">Fecha de consulta</label>
    <input type="date" id="fecha" name="fecha" value="<?= esc($fecha) ?>" class="form-control" style="width:auto" onchange="this.form.submit()">
  </div>
  <form method="get" action="/facturacion" id="filter-form" style="display:contents">
    <input type="hidden" name="fecha" id="fecha-hidden" value="<?= esc($fecha) ?>">
    <button type="submit" class="btn-primary">Filtrar</button>
  </form>
  <div style="margin-left:auto;font-size:12px;color:var(--text-tertiary)">
    Mostrando: <strong style="color:var(--text-primary)"><?= date('d/m/Y', strtotime($fecha)) ?></strong>
    <?php if ($fecha === date('Y-m-d')): ?><span style="background:rgba(124,58,237,.1);color:var(--purple);padding:1px 8px;border-radius:20px;font-size:11px;margin-left:.5rem">Hoy</span><?php endif; ?>
  </div>
</div>

<script>
document.getElementById('fecha').addEventListener('change', function() {
  document.getElementById('fecha-hidden').value = this.value;
  document.getElementById('filter-form').submit();
});
</script>

<!-- KPIs -->
<div class="kpi-grid">

  <div class="kpi-card">
    <div class="kpi-icon violet">
      <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
    </div>
    <div class="kpi-label">Total facturado</div>
    <div class="kpi-value">$<?= number_format($total_dia, 2, ',', '.') ?></div>
    <div class="kpi-sub <?= $variacion === null ? '' : ($variacion >= 0 ? 'up' : 'down') ?>">
      <?php if ($variacion !== null): ?>
        <?= $variacion >= 0 ? '↑' : '↓' ?> <?= abs($variacion) ?>% vs ayer ($<?= number_format($total_ayer, 0, ',', '.') ?>)
      <?php elseif ($total_ayer == 0 && $fecha !== date('Y-m-d', strtotime('yesterday'))): ?>
        Sin ventas ayer para comparar
      <?php else: ?>
        <?= $total_dia > 0 ? 'Ingresos del día' : 'Sin ventas registradas' ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon green">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <div class="kpi-label">Facturas emitidas</div>
    <div class="kpi-value"><?= $count_pagos ?></div>
    <div class="kpi-sub"><?= $count_pagos === 1 ? '1 pago procesado' : $count_pagos . ' pagos procesados' ?></div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon amber">
      <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
    </div>
    <div class="kpi-label">Ticket promedio</div>
    <div class="kpi-value">$<?= $count_pagos > 0 ? number_format($ticket_promedio, 2, ',', '.') : '0,00' ?></div>
    <div class="kpi-sub">Por factura emitida</div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon blue">
      <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </div>
    <div class="kpi-label">Método más usado</div>
    <div class="kpi-value" style="font-size:18px"><?= esc($metodo_top) ?></div>
    <?php if (!empty($por_metodo)): ?>
    <div class="kpi-sub"><?= $por_metodo[0]['count'] ?> de <?= $count_pagos ?> transacciones</div>
    <?php else: ?>
    <div class="kpi-sub">Sin datos</div>
    <?php endif; ?>
  </div>

</div>

<!-- Grid principal -->
<div class="fact-grid">

  <!-- Tabla de pagos -->
  <div class="panel">
    <div class="panel-head">
      <span class="panel-title">Pagos del <?= date('d/m/Y', strtotime($fecha)) ?></span>
      <?php if ($count_pagos > 0): ?>
      <span style="font-size:11.5px;color:var(--text-tertiary)"><?= $count_pagos ?> registro<?= $count_pagos !== 1 ? 's' : '' ?></span>
      <?php endif; ?>
    </div>
    <?php if (!empty($pagos)): ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>Factura</th>
          <th>Pedido</th>
          <th>Tipo</th>
          <th>Hora</th>
          <th>Método</th>
          <th style="text-align:center">Items</th>
          <th style="text-align:right">Monto</th>
          <th style="text-align:center;width:48px" class="no-print"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pagos as $p): ?>
        <tr>
          <td class="mono" style="font-size:12px;color:var(--text-tertiary)">
            FAC-<?= str_pad($p['id_pago'], 6, '0', STR_PAD_LEFT) ?>
          </td>
          <td class="mono" style="font-size:12px">
            <a href="/pedidos/detalles/<?= $p['id_pedido'] ?>" style="color:var(--purple);text-decoration:none">
              #<?= str_pad($p['id_pedido'], 5, '0', STR_PAD_LEFT) ?>
            </a>
          </td>
          <td>
            <?php
              $tipoClass = match($p['tipo_pedido']) {
                'barra'    => 'tipo-barra',
                'take_away'=> 'tipo-take_away',
                default    => 'tipo-mesa',
              };
              $tipoLabel = match($p['tipo_pedido']) {
                'barra'    => 'Barra',
                'take_away'=> 'Para llevar',
                default    => 'Mesa ' . ($p['numero_mesa'] ?? '?'),
              };
            ?>
            <span class="tipo-tag <?= $tipoClass ?>"><?= $tipoLabel ?></span>
          </td>
          <td class="mono" style="font-size:12px"><?= date('H:i', strtotime($p['fecha_pago'])) ?></td>
          <td style="font-size:13px"><?= esc($p['metodo_nombre']) ?></td>
          <td class="mono" style="text-align:center;font-size:12px;color:var(--text-tertiary)"><?= $p['items_count'] ?></td>
          <td class="mono" style="text-align:right;font-weight:500;color:var(--text-primary)">
            $<?= number_format((float)$p['monto'], 2, ',', '.') ?>
          </td>
          <td style="text-align:center" class="no-print">
            <a href="/facturacion/detalle/<?= $p['id_pago'] ?>" class="action-link action-view">
              <svg class="icon-sm" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              Ver
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6" style="text-align:right;padding:.8rem 1rem;font-size:13px;font-weight:500;color:var(--text-secondary);border-top:2px solid var(--border)">Total del día</td>
          <td class="mono" style="text-align:right;padding:.8rem 1rem;font-size:18px;font-weight:600;color:var(--purple);border-top:2px solid var(--border)">
            $<?= number_format($total_dia, 2, ',', '.') ?>
          </td>
          <td style="border-top:2px solid var(--border)" class="no-print"></td>
        </tr>
      </tfoot>
    </table>
    <?php else: ?>
    <div class="empty-state">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--text-tertiary)" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
      </svg>
      <p>No hay pagos registrados para el <?= date('d/m/Y', strtotime($fecha)) ?>.</p>
      <a href="/pedidos" class="btn-ghost">Ir a pedidos →</a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Panel lateral: desglose por método -->
  <div style="display:flex;flex-direction:column;gap:1rem;">

    <!-- Resumen por método de pago -->
    <div class="panel">
      <div class="panel-head">
        <span class="panel-title">Por método de pago</span>
      </div>
      <div class="panel-body" style="padding-top:.75rem">
        <?php if (!empty($por_metodo)): ?>
          <?php foreach ($por_metodo as $m): ?>
          <div class="metodo-row">
            <div class="metodo-name">
              <span><?= esc($m['metodo']) ?></span>
              <strong>$<?= number_format($m['total'], 2, ',', '.') ?></strong>
            </div>
            <div class="metodo-bar-bg">
              <div class="metodo-bar" style="width:<?= $max_metodo_total > 0 ? round($m['total'] / $max_metodo_total * 100) : 0 ?>%"></div>
            </div>
            <div class="metodo-meta">
              <?= $m['count'] ?> transacción<?= $m['count'] !== 1 ? 'es' : '' ?> ·
              <?= $total_dia > 0 ? round($m['total'] / $total_dia * 100) : 0 ?>%
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="text-align:center;padding:1.25rem 0;color:var(--text-tertiary);font-size:13px">Sin datos</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Resumen rápido -->
    <div class="panel">
      <div class="panel-head">
        <span class="panel-title">Resumen del día</span>
      </div>
      <div class="panel-body">
        <?php
          $tipos = ['mesa' => 0, 'barra' => 0, 'take_away' => 0];
          foreach ($pagos as $p) {
            $t = $p['tipo_pedido'] ?? 'mesa';
            if (isset($tipos[$t])) $tipos[$t]++;
          }
          $labels = ['mesa' => 'Mesa', 'barra' => 'Barra', 'take_away' => 'Para llevar'];
          $colors = ['mesa' => 'tipo-mesa', 'barra' => 'tipo-barra', 'take_away' => 'tipo-take_away'];
        ?>
        <div style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:1rem">
          <?php foreach ($tipos as $tipo => $cnt): if ($cnt === 0) continue; ?>
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span class="tipo-tag <?= $colors[$tipo] ?>"><?= $labels[$tipo] ?></span>
            <span class="mono" style="font-size:13px;color:var(--text-secondary)"><?= $cnt ?> factura<?= $cnt !== 1 ? 's' : '' ?></span>
          </div>
          <?php endforeach; ?>
          <?php if (array_sum($tipos) === 0): ?>
            <span style="font-size:13px;color:var(--text-tertiary)">Sin ventas</span>
          <?php endif; ?>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:.75rem">
          <div style="display:flex;justify-content:space-between;margin-bottom:.4rem">
            <span style="font-size:12px;color:var(--text-tertiary)">Total pedidos</span>
            <span class="mono" style="font-size:12px">$<?= number_format(array_sum(array_column($pagos, 'total_pedido')), 2, ',', '.') ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:.4rem">
            <span style="font-size:12px;color:var(--text-tertiary)">Total cobrado</span>
            <span class="mono" style="font-size:12px;color:var(--purple);font-weight:500">$<?= number_format($total_dia, 2, ',', '.') ?></span>
          </div>
          <?php $vueltoTotal = array_sum(array_column($pagos, 'monto')) - array_sum(array_column($pagos, 'total_pedido')); ?>
          <?php if ($vueltoTotal > 0.005): ?>
          <div style="display:flex;justify-content:space-between">
            <span style="font-size:12px;color:var(--text-tertiary)">Vuelto entregado</span>
            <span class="mono" style="font-size:12px">$<?= number_format($vueltoTotal, 2, ',', '.') ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
