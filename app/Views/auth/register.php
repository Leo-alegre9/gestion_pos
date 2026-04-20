<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Cuenta — BarPOS</title>
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
        <div class="logo-mark">B</div>
        BarPOS
      </a>
      <div class="nav-links">
        <a href="/" style="color: var(--text-secondary); font-size: 13px;">Volver a inicio</a>
      </div>
    </nav>
  </header>

  <section class="auth-container">
    <div class="auth-box animate__animated animate__fadeIn animate__duration-700ms">
      <div class="auth-header animate__animated animate__slideInDown animate__duration-600ms">
        <h1>Crea tu cuenta</h1>
        <p>Únete a BarPOS y gestiona tu bar profesionalmente</p>
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

      <form action="/auth/store-register" method="POST" class="auth-form animate__animated animate__slideInUp animate__duration-600ms" style="animation-delay: 0.1s;">
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="name" class="form-label">Nombre completo</label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            class="form-input" 
            placeholder="Juan Pérez"
            value="<?= old('name') ?>"
            required
          >
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="tu@email.com"
            value="<?= old('email') ?>"
            required
          >
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Contraseña</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-input" 
            placeholder="••••••••"
            required
          >
          <small class="form-hint">Mínimo 8 caracteres</small>
        </div>

        <div class="form-group">
          <label for="password_confirm" class="form-label">Confirmar contraseña</label>
          <input 
            type="password" 
            id="password_confirm" 
            name="password_confirm" 
            class="form-input" 
            placeholder="••••••••"
            required
          >
        </div>

        <div class="form-checkbox">
          <input type="checkbox" id="terms" name="terms" value="1" required>
          <label for="terms">Acepto los términos y condiciones de servicio</label>
        </div>

        <button type="submit" class="btn-pill btn-lg btn-block">Crear cuenta</button>
      </form>

      <div class="auth-divider">
        <span>o</span>
      </div>

      <div class="auth-footer animate__animated animate__fadeIn animate__duration-700ms" style="animation-delay: 0.2s;">
        <p>¿Ya tienes cuenta? <a href="/auth/login" class="link-primary">Inicia sesión aquí</a></p>
      </div>
    </div>
  </section>

  <footer style="margin-top: 4rem;">
    <div class="footer-inner">
      <div class="footer-copy">© 2025 BarPOS. Todos los derechos reservados.</div>
      <div class="footer-links">
        <a href="#">Términos</a>
        <a href="#">Privacidad</a>
        <a href="#">Ayuda</a>
      </div>
    </div>
  </footer>

</body>
</html>
