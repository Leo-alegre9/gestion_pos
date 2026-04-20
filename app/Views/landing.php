<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BarPOS — Sistema de Gestión para Bar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

  <header>
    <nav>
      <a href="#" class="logo">
        <div class="logo-mark">B</div>
        Gestion_POS
      </a>
      <div class="nav-links">
        <a href="#features">Características</a>
        <a href="#pagos">Pagos</a>
        <a href="#contacto">Contacto</a>
        <a href="#" class="btn-outline">Iniciar sesión</a>
        <a href="#" class="btn-pill">Empezar gratis</a>
      </div>
    </nav>
  </header>

  <section class="hero">
    <div class="badge"><span class="badge-dot"></span> Potenciado con CodeIgniter 4</div>
    <h1>Gestiona tu bar<br>como un <strong>profesional</strong></h1>
    <p>Controlá mesas, pedidos, inventario y ventas desde una sola plataforma. Ideal para bares, pubs y restaurantes.</p>
    <div class="hero-ctas">
      <a href="#" class="btn-pill btn-lg">Empezar gratis</a>
      <a href="#features" class="btn-outline btn-lg">Ver características</a>
    </div>

    <div class="preview-wrap">
      <div class="preview-titlebar">
        <div class="wb wb-r"></div>
        <div class="wb wb-y"></div>
        <div class="wb wb-g"></div>
        <span class="title-text">Vista de mesas · Turno noche</span>
      </div>
      <div class="preview-body">
        <div class="table-chip t-ocupada"><span class="tnum">01</span><span class="tlabel">Ocupada</span></div>
        <div class="table-chip t-libre"><span class="tnum">02</span><span class="tlabel">Libre</span></div>
        <div class="table-chip t-reservada"><span class="tnum">03</span><span class="tlabel">Reservada</span></div>
        <div class="table-chip t-ocupada"><span class="tnum">04</span><span class="tlabel">Ocupada</span></div>
        <div class="table-chip t-libre"><span class="tnum">05</span><span class="tlabel">Libre</span></div>
        <div class="table-chip t-ocupada"><span class="tnum">06</span><span class="tlabel">Ocupada</span></div>
        <div class="table-chip t-inactiva"><span class="tnum">07</span><span class="tlabel">Inactiva</span></div>
        <div class="table-chip t-libre"><span class="tnum">08</span><span class="tlabel">Libre</span></div>
      </div>
    </div>
  </section>

  <section class="metrics-row">
    <div class="metrics-inner">
      <div class="metric"><div class="metric-val">500+</div><div class="metric-lbl">Pedidos / día</div></div>
      <div class="metric"><div class="metric-val">50+</div><div class="metric-lbl">Mesas soportadas</div></div>
      <div class="metric"><div class="metric-val">100%</div><div class="metric-lbl">Tiempo real</div></div>
      <div class="metric"><div class="metric-val">24/7</div><div class="metric-lbl">Soporte</div></div>
    </div>
  </section>

  <section class="features-section" id="features">
    <div class="section-eyebrow">Funcionalidades</div>
    <div class="section-heading">Todo para tu bar en un lugar</div>
    <div class="section-sub">Gestioná cada aspecto de tu operación desde una sola plataforma, sin apps adicionales.</div>

    <div class="features-list">
      <div class="feature-row">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        </div>
        <div class="feat-text">
          <h3>Gestión de mesas</h3>
          <p>Controlá el estado de cada mesa en tiempo real: libre, ocupada, reservada o inactiva. Asigná pedidos directamente.</p>
        </div>
      </div>
      <div class="feature-row">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="feat-text">
          <h3>Pedidos por mesa, barra o take away</h3>
          <p>Creá pedidos desde el mostrador, una mesa específica o para llevar. El sistema registra todo automáticamente.</p>
        </div>
      </div>
      <div class="feature-row">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <div class="feat-text">
          <h3>Catálogo de productos</h3>
          <p>Gestioná tragos, cervezas, comidas y postres. Organizá por categorías y controlá disponibilidad al instante.</p>
        </div>
      </div>
      <div class="feature-row">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 3H8v4h8V3z"/></svg>
        </div>
        <div class="feat-text">
          <h3>Control de stock</h3>
          <p>Inventario de bebidas y productos con alertas automáticas cuando el stock está bajo. Entradas y salidas registradas.</p>
        </div>
      </div>
      <div class="feature-row">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="feat-text">
          <h3>Reportes y analytics</h3>
          <p>Visualizá ventas diarias, productos más vendidos y rotación de mesas. Todo lo que necesitás saber de tu negocio.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="payments-section" id="pagos">
    <div class="payments-inner">
      <div class="section-eyebrow">Métodos de pago</div>
      <div class="section-heading">Cobrá como quieras</div>
      <div class="section-sub" style="margin-bottom:0;">Aceptá múltiples formas de pago en un mismo pedido.</div>
      <div class="pay-chips">
        <span class="pay-chip active">Efectivo</span>
        <span class="pay-chip active">Débito</span>
        <span class="pay-chip active">Crédito</span>
        <span class="pay-chip active">Transferencia</span>
        <span class="pay-chip">Pago combinado</span>
        <span class="pay-chip">Mercado Pago</span>
      </div>
    </div>
  </section>

  <section class="cta-section" id="contacto">
    <h2>¿Listo para ordenar?</h2>
    <p>Simplificá la gestión de tu bar. Controlá mesas, pedidos y stock desde un solo lugar.</p>
    <div class="cta-btns">
      <a href="#" class="btn-pill btn-lg">Crear cuenta gratis</a>
      <a href="#" class="btn-outline btn-lg">Hablar con ventas</a>
    </div>
  </section>

  <footer>
    <div class="footer-inner">
      <div class="logo">
        <div class="logo-mark" style="width:22px;height:22px;font-size:11px;">B</div>
        BarPOS
      </div>
      <div class="footer-links">
        <a href="#">Términos</a>
        <a href="#">Privacidad</a>
        <a href="#">Ayuda</a>
      </div>
      <div class="footer-copy">© 2025 BarPOS. Todos los derechos reservados.</div>
    </div>
  </footer>

</body>
</html>