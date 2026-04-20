<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BarPOS — Sistema de Gestión para Bar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    :root {
      --purple: #7c3aed;
      --purple-light: #f5f3ff;
      --purple-border: #ddd6fe;
      --text-primary: #111827;
      --text-secondary: #6b7280;
      --text-tertiary: #9ca3af;
      --bg: #f9fafb;
      --surface: #ffffff;
      --border: rgba(0,0,0,0.08);
      --border-strong: rgba(0,0,0,0.12);
      --green-bg: #EAF3DE; --green-text: #3B6D11; --green-border: #C0DD97;
      --amber-bg: #FAEEDA; --amber-text: #854F0B; --amber-border: #FAC775;
      --radius-md: 8px;
      --radius-lg: 12px;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text-primary);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }

    /* NAV */
    header {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      position: sticky; top: 0; z-index: 100;
    }
    nav {
      max-width: 1100px; margin: 0 auto;
      padding: 0 2rem; height: 56px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .logo { display: flex; align-items: center; gap: 8px; text-decoration: none; color: var(--text-primary); font-weight: 500; font-size: 15px; }
    .logo-mark {
      width: 28px; height: 28px; border-radius: 8px;
      background: var(--purple);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: 13px; font-weight: 500;
    }
    .nav-links { display: flex; align-items: center; gap: 1.5rem; }
    .nav-links a { font-size: 13px; color: var(--text-secondary); text-decoration: none; transition: color 0.15s; }
    .nav-links a:hover { color: var(--text-primary); }
    .btn-outline {
      background: transparent; color: var(--text-secondary);
      padding: 6px 16px; border-radius: 20px;
      font-size: 13px; font-weight: 500; text-decoration: none;
      border: 1px solid var(--border-strong); transition: background 0.15s;
    }
    .btn-outline:hover { background: var(--bg); }
    .btn-pill {
      background: var(--purple); color: white !important;
      padding: 6px 16px; border-radius: 20px;
      font-size: 13px; font-weight: 500; text-decoration: none;
      border: none; cursor: pointer; transition: background 0.15s;
    }
    .btn-pill:hover { background: #6d28d9; }

    /* HERO */
    .hero {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      padding: 4.5rem 2rem 3.5rem;
      text-align: center;
    }
    .badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--purple-light); color: var(--purple);
      padding: 4px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 500; margin-bottom: 1.5rem;
      border: 1px solid var(--purple-border);
    }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--purple); display: inline-block; }
    .hero h1 {
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: 400; line-height: 1.15;
      letter-spacing: -0.025em; margin-bottom: 1rem;
    }
    .hero h1 strong { font-weight: 500; color: var(--purple); }
    .hero p {
      font-size: 1rem; color: var(--text-secondary);
      max-width: 440px; margin: 0 auto 2rem; line-height: 1.6;
    }
    .hero-ctas { display: flex; gap: 10px; justify-content: center; align-items: center; margin-bottom: 3rem; }
    .btn-lg { padding: 10px 24px; font-size: 14px; }

    /* PREVIEW MOCKUP */
    .preview-wrap {
      max-width: 620px; margin: 0 auto;
      background: var(--surface);
      border: 1px solid var(--border-strong);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    }
    .preview-titlebar {
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      padding: 10px 14px;
      display: flex; align-items: center; gap: 6px;
    }
    .wb { width: 8px; height: 8px; border-radius: 50%; }
    .wb-r { background: #f09595; }
    .wb-y { background: #EF9F27; }
    .wb-g { background: #97C459; }
    .preview-titlebar .title-text {
      font-family: 'DM Mono', monospace;
      font-size: 11px; color: var(--text-tertiary);
      margin-left: auto;
    }
    .preview-body { padding: 1.25rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .table-chip {
      border-radius: 10px; padding: 12px 8px; text-align: center;
      border: 1px solid transparent;
    }
    .table-chip .tnum { font-size: 18px; font-weight: 500; display: block; margin-bottom: 2px; }
    .table-chip .tlabel { font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500; }
    .t-libre { background: var(--green-bg); color: var(--green-text); border-color: var(--green-border); }
    .t-ocupada { background: var(--purple-light); color: var(--purple); border-color: var(--purple-border); }
    .t-reservada { background: var(--amber-bg); color: var(--amber-text); border-color: var(--amber-border); }
    .t-inactiva { background: var(--bg); color: var(--text-tertiary); border-color: var(--border); }

    /* METRICS */
    .metrics-row {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      padding: 2.5rem 2rem;
    }
    .metrics-inner {
      max-width: 680px; margin: 0 auto;
      display: grid; grid-template-columns: repeat(4, 1fr);
      border: 1px solid var(--border);
      border-radius: var(--radius-md); overflow: hidden;
    }
    .metric {
      padding: 1.5rem 1rem; text-align: center;
      border-right: 1px solid var(--border);
    }
    .metric:last-child { border-right: none; }
    .metric-val { font-size: 1.75rem; font-weight: 500; color: var(--purple); }
    .metric-lbl { font-size: 12px; color: var(--text-secondary); margin-top: 3px; }

    /* FEATURES */
    .features-section { padding: 3.5rem 2rem; max-width: 720px; margin: 0 auto; }
    .section-eyebrow { font-family: 'DM Mono', monospace; font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
    .section-heading { font-size: 1.5rem; font-weight: 400; margin-bottom: 0.5rem; letter-spacing: -0.01em; }
    .section-sub { font-size: 14px; color: var(--text-secondary); margin-bottom: 2rem; line-height: 1.6; }

    .features-list {
      border: 1px solid var(--border);
      border-radius: var(--radius-lg); overflow: hidden;
    }
    .feature-row {
      background: var(--surface);
      padding: 1.25rem 1.5rem;
      display: flex; align-items: flex-start; gap: 1rem;
      border-bottom: 1px solid var(--border);
      transition: background 0.15s;
    }
    .feature-row:last-child { border-bottom: none; }
    .feature-row:hover { background: var(--bg); }
    .feat-icon {
      width: 36px; height: 36px; flex-shrink: 0;
      border-radius: 10px;
      background: var(--purple-light);
      border: 1px solid var(--purple-border);
      display: flex; align-items: center; justify-content: center;
    }
    .feat-icon svg { width: 16px; height: 16px; stroke: var(--purple); fill: none; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }
    .feat-text h3 { font-size: 14px; font-weight: 500; margin-bottom: 3px; }
    .feat-text p { font-size: 13px; color: var(--text-secondary); line-height: 1.5; }

    /* PAYMENTS */
    .payments-section {
      background: var(--surface);
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      padding: 3rem 2rem;
    }
    .payments-inner { max-width: 720px; margin: 0 auto; }
    .pay-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 1.25rem; }
    .pay-chip {
      background: var(--bg); color: var(--text-secondary);
      border: 1px solid var(--border);
      border-radius: 20px; padding: 6px 14px; font-size: 13px;
    }
    .pay-chip.active {
      background: var(--purple-light); color: var(--purple);
      border-color: var(--purple-border);
    }

    /* CTA */
    .cta-section {
      padding: 4rem 2rem; text-align: center;
      max-width: 720px; margin: 0 auto;
    }
    .cta-section h2 { font-size: 1.75rem; font-weight: 400; margin-bottom: 0.75rem; letter-spacing: -0.01em; }
    .cta-section p { font-size: 14px; color: var(--text-secondary); margin-bottom: 1.75rem; max-width: 360px; margin-left: auto; margin-right: auto; line-height: 1.6; }
    .cta-btns { display: flex; gap: 10px; justify-content: center; }

    /* FOOTER */
    footer {
      background: var(--surface);
      border-top: 1px solid var(--border);
      padding: 1.5rem 2rem;
    }
    .footer-inner {
      max-width: 1100px; margin: 0 auto;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 1rem;
    }
    .footer-links { display: flex; gap: 1.25rem; }
    .footer-links a { font-size: 12px; color: var(--text-tertiary); text-decoration: none; }
    .footer-links a:hover { color: var(--text-secondary); }
    .footer-copy { font-size: 12px; color: var(--text-tertiary); }

    /* RESPONSIVE */
    @media (max-width: 640px) {
      .nav-links a:not(.btn-pill):not(.btn-outline) { display: none; }
      .btn-outline { display: none; }
      .metrics-inner { grid-template-columns: repeat(2, 1fr); }
      .metric:nth-child(2) { border-right: none; }
      .metric:nth-child(3) { border-top: 1px solid var(--border); }
      .metric:nth-child(4) { border-top: 1px solid var(--border); }
      .preview-body { grid-template-columns: repeat(2, 1fr); }
      .hero-ctas { flex-direction: column; }
      .cta-btns { flex-direction: column; align-items: center; }
      .footer-inner { flex-direction: column; align-items: flex-start; }
    }
  </style>
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