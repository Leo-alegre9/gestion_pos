<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión — Gestion_POS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

  <header>
    <nav>
      <a href="/" class="logo">
        <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="sphereGradientLogin" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1" />
              <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1" />
            </radialGradient>
            <radialGradient id="sphereShineLogin" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6" />
              <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0" />
            </radialGradient>
          </defs>
          <circle cx="20" cy="20" r="18" fill="url(#sphereGradientLogin)" />
          <circle cx="14" cy="12" r="8" fill="url(#sphereShineLogin)" />
          <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3" />
        </svg>
        <span class="logo-text">Gestion_POS</span>
      </a>
      <div class="nav-links">
        <a href="/" style="color: var(--text-secondary); font-size: 13px;">Volver a inicio</a>
      </div>
    </nav>
  </header>

  <section class="auth-container">
    <div class="auth-box animate__animated animate__fadeIn animate__duration-700ms">
      <div class="auth-header animate__animated animate__slideInDown animate__duration-600ms">
        <h1>Bienvenido de nuevo</h1>
        <p>Inicia sesión para acceder a tu cuenta de Gestion_POS</p>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
          <svg class="alert-icon" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
          <svg class="alert-icon" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>

      <form action="/auth/authenticate" method="POST" class="auth-form animate__animated animate__slideInUp animate__duration-600ms" style="animation-delay: 0.1s;">
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="admin@gestion-pos.com"
            value="<?= old('email') ?>"
            required
          >
          <small class="form-hint">Ej: admin@gestion-pos.com</small>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Contraseña</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-input" 
            placeholder="••••••••"
            value="<?= old('password') ?>"
            required
          >
          <small class="form-hint">Ej: 123456</small>
        </div>

        <div class="form-checkbox">
          <input type="checkbox" id="remember" name="remember" value="1">
          <label for="remember">Recuérdame en este dispositivo</label>
        </div>

        <button type="submit" class="btn-pill btn-lg btn-block">Iniciar sesión</button>
      </form>

      <div class="auth-divider">
        <span>o</span>
      </div>

      <div class="auth-footer">
        <p>¿No tienes cuenta? <a href="/auth/register" class="link-primary">Regístrate aquí</a></p>
        <p><a href="#" class="link-secondary">¿Olvidaste tu contraseña?</a></p>
      </div>

      <div class="auth-credentials animate__animated animate__fadeIn animate__duration-700ms" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border); text-align: center; animation-delay: 0.2s;">
        <p style="font-size: 12px; color: var(--text-tertiary); margin-bottom: 0.75rem;">Credenciales de demo:</p>
        <div style="background: var(--bg); padding: 1rem; border-radius: var(--radius-md); font-family: 'DM Mono', monospace; font-size: 12px;">
          <div><strong>Email:</strong> admin@gestion-pos.com</div>
          <div><strong>Contraseña:</strong> 123456</div>
        </div>
      </div>
    </div>
  </section>

  <footer style="margin-top: 4rem;">
    <div class="footer-inner">
      <div class="footer-copy">© 2025 Gestion_POS. Todos los derechos reservados.</div>
      <div class="footer-links">
        <a href="#">Términos</a>
        <a href="#">Privacidad</a>
        <a href="#">Ayuda</a>
      </div>
    </div>
  </footer>

</body>
</html>
