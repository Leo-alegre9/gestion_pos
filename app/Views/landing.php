<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarPOS - Sistema de Gestión para Bar</title>
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            line-height: 1.6;
            background-color: #f9fafb;
        }
        header {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        nav {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #7c3aed;
            text-decoration: none;
        }
        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-links a:hover {
            color: #7c3aed;
        }
        .btn-primary {
            background: #7c3aed;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: #6d28d9;
        }
        .hero {
            margin-top: 72px;
            background: linear-gradient(135deg, #581c87 0%, #7c3aed 100%);
            padding: 6rem 2rem;
            text-align: center;
            color: white;
        }
        .hero-content {
            max-width: 900px;
            margin: 0 auto;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        .hero p {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .btn-secondary {
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.3);
            transition: background 0.2s;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.25);
        }
        .features {
            padding: 5rem 2rem;
            max-width: 1280px;
            margin: 0 auto;
        }
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .section-title p {
            color: #6b7280;
            font-size: 1.125rem;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            background: #f5f3ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: #7c3aed;
            font-size: 1.5rem;
        }
        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .feature-card p {
            color: #6b7280;
        }
        .stats {
            background: #581c87;
            padding: 4rem 2rem;
        }
        .stats-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            text-align: center;
            color: white;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
        }
        .stat-label {
            opacity: 0.8;
            font-size: 1rem;
        }
        .cta {
            padding: 5rem 2rem;
            background: white;
            text-align: center;
        }
        .cta-content {
            max-width: 600px;
            margin: 0 auto;
        }
        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .cta p {
            color: #6b7280;
            font-size: 1.125rem;
            margin-bottom: 2rem;
        }
        footer {
            background: #111827;
            color: white;
            padding: 3rem 2rem;
        }
        .footer-content {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: 700;
        }
        .footer-links {
            display: flex;
            gap: 1.5rem;
        }
        .footer-links a {
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: white;
        }
        .footer-copy {
            color: #6b7280;
            font-size: 0.875rem;
            width: 100%;
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #374151;
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            .nav-links {
                display: none;
            }
            .hero-buttons {
                flex-direction: column;
            }
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="#" class="logo">
                <div class="logo-icon">B</div>
                BarPOS
            </a>
            <div class="nav-links">
                <a href="#features">Características</a>
                <a href="#stats">Estadísticas</a>
                <a href="#contact">Contacto</a>
                <a href="#" class="btn-primary">Iniciar Sesión</a>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Gestiona tu bar como un profesional</h1>
            <p>Controla mesas, pedidos, inventario y ventas desde una sola plataforma. Ideal para bares, pubs y restaurantes. Potenciado con CodeIgniter 4.</p>
            <div class="hero-buttons">
                <a href="#" class="btn-primary">Empezar Ahora</a>
                <a href="#features" class="btn-secondary">Ver Características</a>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-title">
            <h2>Todo para tu bar</h2>
            <p>Gestiona cada aspecto de tu bar desde una sola plataforma</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🍺</div>
                <h3>Gestión de Mesas</h3>
                <p>Controla el estado de cada mesa en tiempo real: libre, ocupada, reservada o inactiva. Asigna pedidos directamente a cada mesa.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Pedidos por Mesa, Barra o Take Away</h3>
                <p>Crea pedidos desde el mostrador, desde una mesa específica o para llevar. El sistema registra todo automáticamente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🍸</div>
                <h3>Catálogo de Productos</h3>
                <p>Gestiona tragos, cervezas, comidas y postres. Organiza por categorías y controla disponibilidad en tiempo real.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3>Control de Stock</h3>
                <p>Lleva el inventario de bebidas y productos. Alertas automáticas cuando el stock está bajo. Entradas y salidas registradas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3>Múltiples Métodos de Pago</h3>
                <p>Acepta efectivo, tarjetas de débito, crédito y transferencias. Registra pagos combinados en un solo pedido.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Reportes y Analytics</h3>
                <p>Visualiza ventas diarias, productos más vendidos, rotación de mesas y más. Todo lo que necesitas saber.</p>
            </div>
        </div>
    </section>

    <section class="stats" id="stats">
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Pedidos/Día</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Mesas Soportadas</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Tiempo Real</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Soporte</div>
            </div>
        </div>
    </section>

    <section class="cta" id="contact">
        <div class="cta-content">
            <h2>Listo para ordenar?</h2>
            <p>Simplifica la gestión de tu bar. Controla mesas, pedidos y stock desde un solo lugar. Pruébalo gratis.</p>
            <a href="#" class="btn-primary">Crear Cuenta Gratis</a>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <div class="logo-icon">B</div>
                BarPOS
            </div>
            <div class="footer-links">
                <a href="#">Términos</a>
                <a href="#">Privacidad</a>
                <a href="#">Ayuda</a>
            </div>
            <div class="footer-copy">
                © 2025 BarPOS. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>