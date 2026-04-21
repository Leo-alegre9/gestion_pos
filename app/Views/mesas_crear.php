<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Mesa — Gestion_POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        /* ── Estilos base ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f9fafb;
            font-family: 'DM Sans', sans-serif;
            color: #111827;
        }

        /* ── Header/Navbar ── */
        header {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 2rem;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: #111827;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-links a {
            color: #4b5563;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #7c3aed;
        }

        /* ── Contenedor principal ── */
        .container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .form-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-header h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .form-header p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        /* ── Grupos de formulario ── */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.95rem;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-hint {
            display: block;
            margin-top: 0.35rem;
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }

        .form-group.has-error .form-input,
        .form-group.has-error .form-select {
            border-color: #dc2626;
            background-color: #fef2f2;
        }

        /* ── Botones ── */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        button,
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #7c3aed;
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            flex: 1;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        /* ── Alertas ── */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .alert-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-error .alert-icon {
            stroke: #dc2626;
            fill: none;
            stroke-width: 2;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-success .alert-icon {
            stroke: #22c55e;
            fill: none;
            stroke-width: 2;
        }

        /* ── Info Box ── */
        .info-box {
            background: #f3f4f6;
            border-left: 4px solid #7c3aed;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #374151;
        }

        .info-box strong {
            display: block;
            margin-bottom: 0.25rem;
            color: #111827;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            header {
                padding: 1rem;
            }

            nav {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                width: 100%;
            }

            .nav-links a {
                width: 100%;
                text-align: center;
            }

            .container {
                margin: 1rem auto;
            }

            .form-box {
                padding: 1.5rem;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            button,
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<header>
    <nav>
        <a href="/dashboard" class="logo">
            <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="sphereGradientCreate" cx="35%" cy="35%">
                        <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1" />
                    </radialGradient>
                    <radialGradient id="sphereShineCreate" cx="35%" cy="35%">
                        <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6" />
                        <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0" />
                    </radialGradient>
                </defs>
                <circle cx="20" cy="20" r="18" fill="url(#sphereGradientCreate)" />
                <circle cx="14" cy="12" r="8" fill="url(#sphereShineCreate)" />
                <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3" />
            </svg>
            <span class="logo-text">Gestion_POS</span>
        </a>
        <div class="nav-links">
            <span style="color: #6b7280; font-size: 0.9rem;">Hola, 👋 <?= esc($user['name'] ?? 'Usuario') ?></span>
            <a href="/mesas">Volver a Mesas</a>
            <a href="/auth/logout">Cerrar Sesión</a>
        </div>
    </nav>
</header>

<div class="container">
    <div class="form-box">
        <div class="form-header">
            <h1>Crear Nueva Mesa</h1>
            <p>Agregá una nueva mesa a tu sistema</p>
        </div>

        <!-- Mostrar errores generales -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <svg class="alert-icon" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <!-- Mostrar éxito -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <svg class="alert-icon" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <!-- Información para el usuario -->
        <div class="info-box">
            <strong>💡 Información:</strong>
            La mesa iniciará con estado <strong>"Libre"</strong> automáticamente. Podés cambiar su estado después de crearla.
        </div>

        <!-- Formulario de creación de mesa -->
        <form method="post" action="/mesas/guardar" class="create-form">
            <?= csrf_field() ?>

            <!-- Campo: Número de Mesa -->
            <div class="form-group <?= isset($errors['numero']) ? 'has-error' : '' ?>">
                <label for="numero" class="form-label">Número de Mesa *</label>
                <input 
                    type="number" 
                    id="numero" 
                    name="numero" 
                    class="form-input" 
                    placeholder="<?= $proximoNumero ?>"
                    value="<?= old('numero', $proximoNumero) ?>"
                    min="1"
                    max="999"
                    required
                >
                <small class="form-hint">Identificador único para cada mesa (ej: 1, 2, 3...)</small>
                <?php if (isset($errors['numero'])): ?>
                    <div class="error-message">
                        <strong>Error:</strong> <?= $errors['numero'] ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Campo: Capacidad -->
            <div class="form-group <?= isset($errors['capacidad']) ? 'has-error' : '' ?>">
                <label for="capacidad" class="form-label">Capacidad (Personas)</label>
                <select id="capacidad" name="capacidad" class="form-select">
                    <option value="">-- Seleccionar (opcional) --</option>
                    <option value="2" <?= old('capacidad') == 2 ? 'selected' : '' ?>>2 personas</option>
                    <option value="4" <?= old('capacidad') == 4 ? 'selected' : '' ?>>4 personas</option>
                    <option value="6" <?= old('capacidad') == 6 ? 'selected' : '' ?>>6 personas</option>
                    <option value="8" <?= old('capacidad') == 8 ? 'selected' : '' ?>>8 personas</option>
                    <option value="10" <?= old('capacidad') == 10 ? 'selected' : '' ?>>10 personas</option>
                    <option value="12" <?= old('capacidad') == 12 ? 'selected' : '' ?>>12 personas</option>
                </select>
                <small class="form-hint">Capacidad de personas que puede acomodar la mesa</small>
                <?php if (isset($errors['capacidad'])): ?>
                    <div class="error-message">
                        <strong>Error:</strong> <?= $errors['capacidad'] ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Botones de acción -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="12 5 12 19"></polyline>
                        <polyline points="5 12 19 12"></polyline>
                    </svg>
                    Crear Mesa
                </button>
                <a href="/mesas" class="btn btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
