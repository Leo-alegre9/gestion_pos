<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion_POS — Sistema de Gestión para tu Bar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css">

  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <style>
    /* GRID DE FONDO */
    .grid-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: 
        linear-gradient(rgba(124, 58, 237, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(124, 58, 237, 0.03) 1px, transparent 1px);
      background-size: 50px 50px;
      pointer-events: none;
      z-index: 0;
    }

    /* PARTÍCULAS FLOTANTES */
    .floating-particles {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
    }

    .particle {
      position: absolute;
      width: 4px;
      height: 4px;
      background: var(--purple);
      border-radius: 50%;
      opacity: 0.1;
      animation: float 8s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0) translateX(0);
        opacity: 0.05;
      }
      25% {
        opacity: 0.15;
      }
      50% {
        transform: translateY(-30px) translateX(15px);
        opacity: 0.2;
      }
      75% {
        opacity: 0.1;
      }
    }

    header {
      position: relative;
      z-index: 100;
    }

    main {
      position: relative;
      z-index: 2;
    }

    footer {
      position: relative;
      z-index: 2;
    }

    body {
      background: var(--bg);
    }

    /* MENU TOGGLE MÓVIL */
    .nav-menu-toggle {
      display: none;
      flex-direction: column;
      gap: 5px;
      cursor: pointer;
      z-index: 101;
    }

    .nav-menu-toggle span {
      width: 24px;
      height: 2px;
      background: var(--text-primary);
      transition: all 0.3s ease;
      border-radius: 1px;
    }

    .nav-menu-toggle.active span:nth-child(1) {
      transform: rotate(45deg) translateY(11px);
    }

    .nav-menu-toggle.active span:nth-child(2) {
      opacity: 0;
    }

    .nav-menu-toggle.active span:nth-child(3) {
      transform: rotate(-45deg) translateY(-11px);
    }

    @media (max-width: 768px) {
      .nav-menu-toggle {
        display: flex;
      }

      .nav-links {
        position: absolute;
        top: 56px;
        left: 0;
        right: 0;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        flex-direction: column;
        padding: 1rem;
        gap: 0.75rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
      }

      .nav-links.active {
        max-height: 300px;
      }

      .nav-links a {
        padding: 0.75rem 0;
        display: block;
      }
    }
  </style>
</head>
<body>

  <div class="grid-background"></div>
  <div class="floating-particles" id="particles"></div>

  <header>
    <nav>
      <a href="/" class="logo">
        <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="sphereGradientHeader" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1" />
              <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1" />
            </radialGradient>
            <radialGradient id="sphereShineHeader" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6" />
              <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0" />
            </radialGradient>
          </defs>
          <circle cx="20" cy="20" r="18" fill="url(#sphereGradientHeader)" />
          <circle cx="14" cy="12" r="8" fill="url(#sphereShineHeader)" />
          <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3" />
        </svg>
        <span class="logo-text">Gestion_POS</span>
      </a>
      <div class="nav-menu-toggle" id="menuToggle">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <div class="nav-links" id="navLinks">
        <a href="#features">Características</a>
        <a href="#pagos">Pagos</a>
        <a href="#contacto">Contacto</a>
        <a href="/auth/login" class="btn-pill">Iniciar sesión</a>
      </div>
    </nav>
  </header>

  <main>
    <section class="hero">
      <div class="badge"><span class="badge-dot"></span> Gestión profesional para tu bar</div>
      <h1>Controla tu bar<br>con <strong>precisión</strong></h1>
      <p>Mesas, pedidos, inventario y ventas en un solo lugar. Todo lo que necesitás para operar como un profesional.</p>
      <div class="hero-ctas">
        <a href="/auth/login" class="btn-pill btn-lg">Ir al sistema</a>
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
        <div class="metric"><div class="metric-val">100%</div><div class="metric-lbl">Tiempo real</div></div>
        <div class="metric"><div class="metric-val">50+</div><div class="metric-lbl">Mesas</div></div>
        <div class="metric"><div class="metric-val">∞</div><div class="metric-lbl">Productos</div></div>
        <div class="metric"><div class="metric-val">24/7</div><div class="metric-lbl">Disponible</div></div>
      </div>
    </section>

    <section class="features-section" id="features">
      <div class="section-eyebrow">Funcionalidades</div>
      <div class="section-heading">Todo para tu bar en un lugar</div>
      <div class="section-sub">Gestioná cada aspecto de tu operación desde una sola plataforma, sin complicaciones.</div>

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
      <h2>¿Necesitás ayuda?</h2>
      <p>¿Preguntas sobre el sistema? Contactá directamente con nosotros.</p>
      <div class="cta-btns">
        <a href="/auth/login" class="btn-pill btn-lg">Ir al sistema</a>
        <a href="https://wa.me/5493781401440" class="btn-outline btn-lg">WhatsApp</a>
      </div>
    </section>
  </main>

  <footer>
    <div class="footer-inner">
      <div class="logo">
        <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="sphereGradientFooter" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1" />
              <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1" />
            </radialGradient>
            <radialGradient id="sphereShineFooter" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6" />
              <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0" />
            </radialGradient>
          </defs>
          <circle cx="20" cy="20" r="18" fill="url(#sphereGradientFooter)" />
          <circle cx="14" cy="12" r="8" fill="url(#sphereShineFooter)" />
          <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3" />
        </svg>
        <span class="logo-text">Gestion_POS</span>
      </div>
      <div class="footer-links">
        <a href="/terminos">Términos</a>
        <a href="/privacidad">Privacidad</a>
        <a href="https://wa.me/5493781401440" target="_blank">Ayuda</a>
      </div>
      <div class="footer-copy">© 2025 Gestion_POS. Todos los derechos reservados.</div>
    </div>
  </footer>

  <script>
    // Generar partículas flotantes
    function createParticles() {
      const container = document.getElementById('particles');
      const particleCount = 15;

      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const randomX = Math.random() * 100;
        const randomY = Math.random() * 100;
        const randomDelay = Math.random() * 4;
        const randomDuration = 8 + Math.random() * 4;
        
        particle.style.left = randomX + '%';
        particle.style.top = randomY + '%';
        particle.style.animationDelay = randomDelay + 's';
        particle.style.animationDuration = randomDuration + 's';
        
        container.appendChild(particle);
      }
    }

    createParticles();

    // Menu móvil toggle
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    menuToggle.addEventListener('click', () => {
      menuToggle.classList.toggle('active');
      navLinks.classList.toggle('active');
    });

    // Cerrar menú al hacer click en un link
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        menuToggle.classList.remove('active');
        navLinks.classList.remove('active');
      });
    });
  </script>

</body>
</html>