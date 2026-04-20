<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse — Gestion_POS</title>
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
            <radialGradient id="sphereGradientRegister" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1" />
              <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1" />
              <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1" />
            </radialGradient>
            <radialGradient id="sphereShineRegister" cx="35%" cy="35%">
              <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6" />
              <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0" />
            </radialGradient>
          </defs>
          <circle cx="20" cy="20" r="18" fill="url(#sphereGradientRegister)" />
          <circle cx="14" cy="12" r="8" fill="url(#sphereShineRegister)" />
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
        <h1>Crear Cuenta</h1>
        <p>Regístrate en Gestion_POS para comenzar</p>
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

      <!-- Mostrar errores de validación -->
      <?php if (session()->has('errores')): ?>
        <div class="alert alert-error">
          <svg class="alert-icon" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <div>
            <?php foreach (session()->get('errores') as $error): ?>
              <div><?= $error ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <form action="/auth/store" method="POST" class="auth-form animate__animated animate__slideInUp animate__duration-600ms" style="animation-delay: 0.1s;">
        <?= csrf_field() ?>

        <!-- Nombre y Apellido -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label for="nombre" class="form-label">Nombre *</label>
            <input 
              type="text" 
              id="nombre" 
              name="nombre" 
              class="form-input" 
              placeholder="Juan"
              value="<?= old('nombre') ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="apellido" class="form-label">Apellido</label>
            <input 
              type="text" 
              id="apellido" 
              name="apellido" 
              class="form-input" 
              placeholder="Pérez"
              value="<?= old('apellido') ?>"
            >
          </div>
        </div>

        <!-- DNI y Fecha de Nacimiento -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label for="dni" class="form-label">DNI *</label>
            <input 
              type="number" 
              id="dni" 
              name="dni" 
              class="form-input" 
              placeholder="12345678"
              value="<?= old('dni') ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="f_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input 
              type="date" 
              id="f_nacimiento" 
              name="f_nacimiento" 
              class="form-input"
              value="<?= old('f_nacimiento') ?>"
            >
          </div>
        </div>

        <!-- Username -->
        <div class="form-group">
          <label for="username" class="form-label">Nombre de Usuario *</label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            class="form-input" 
            placeholder="juan.perez123"
            value="<?= old('username') ?>"
            minlength="3"
            maxlength="50"
            required
          >
          <small class="form-hint">Mínimo 3 caracteres, solo letras, números y guiones</small>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">Email *</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="juan@gestion-pos.com"
            value="<?= old('email') ?>"
            required
          >
          <small class="form-hint">Ej: juan@gestion-pos.com</small>
        </div>

        <!-- Password y Confirmación -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label for="password" class="form-label">Contraseña *</label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input" 
              placeholder="••••••••"
              minlength="6"
              required
            >
            <small class="form-hint">Mínimo 6 caracteres</small>
          </div>

          <div class="form-group">
            <label for="password_confirm" class="form-label">Confirmar Contraseña *</label>
            <input 
              type="password" 
              id="password_confirm" 
              name="password_confirm" 
              class="form-input" 
              placeholder="••••••••"
              minlength="6"
              required
            >
          </div>
        </div>

        <!-- Términos -->
        <div class="form-checkbox">
          <input type="checkbox" id="terminos" name="terminos" required>
          <label for="terminos">Acepto los <a href="/terminos" target="_blank" class="link-primary">términos y condiciones</a></label>
        </div>

        <button type="submit" class="btn-pill btn-lg btn-block">Crear Cuenta</button>
      </form>

      <div class="auth-divider">
        <span>o</span>
      </div>

      <div class="auth-footer">
        <p>¿Ya tienes cuenta? <a href="/auth/login" class="link-primary">Inicia sesión aquí</a></p>
      </div>
    </div>
  </section>

  <footer style="margin-top: 4rem;">
    <div class="footer-inner">
      <div class="footer-copy">© 2025 Gestion_POS. Todos los derechos reservados.</div>
      <div class="footer-links">
        <a href="/terminos">Términos</a>
        <a href="/privacidad">Privacidad</a>
        <a href="https://wa.me/5493781401440" target="_blank">Ayuda</a>
      </div>
    </div>
  </footer>

  <script>
    // Validación de contraseña en el cliente (complementaria)
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const passwordConfirm = document.getElementById('password_confirm').value;
      
      if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
      }
    });
  </script>

</body>
</html>
