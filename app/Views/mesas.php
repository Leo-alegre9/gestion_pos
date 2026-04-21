<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'Mesas') ?> - Gestion_POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .page-wrap {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Estilos del Navbar (similares a login.php) */
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

        .nav-links a {
            color: #4b5563;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-left: 1.5rem;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #7c3aed;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card,
        .mesa-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1rem;
        }

        .mesa-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .mesa-num {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .mesa-estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            margin: 8px 0;
        }

        .estado-libre { background: #dcfce7; color: #166534; }
        .estado-ocupada { background: #ede9fe; color: #6d28d9; }
        .estado-reservada { background: #fef3c7; color: #92400e; }
        .estado-inactiva { background: #e5e7eb; color: #374151; }

        .acciones {
            margin-top: 1rem;
        }

        .acciones form {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }

        select {
            padding: .55rem .75rem;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            color: #111827;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        select:hover {
            border-color: #7c3aed;
        }

        select:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        button {
            padding: 0.55rem 0.75rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        button:active {
            transform: translateY(0);
        }

        /* Botón para agregar mesa */
        .btn-add-mesa {
            background: rgba(80, 15, 190, 0.64);
            border: 1px solid #130035;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
            backdrop-filter: blur(10px);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
        }

        .btn-add-mesa:hover {
            background: rgba(108, 40, 217, 0.64);
            box-shadow: 0 6px 16px rgba(124, 58, 237, 0.35);
            transform: translateY(-2px);
        }

        .btn-add-mesa:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
        }

        .btn-add-mesa svg {
            stroke-width: 2.5;
        }

        /* Botón de actualizar estado */
        button[type="submit"]:not(.btn-delete) {
            background: rgba(109, 40, 217, 0.85);
            color: #ffffff;
            font-weight: 600;
            padding: 0.55rem 1rem;
            box-shadow: 0 2px 8px rgba(109, 40, 217, 0.25);
            backdrop-filter: blur(10px);
        }

        button[type="submit"]:not(.btn-delete):hover {
            background: rgba(91, 33, 182, 0.92);
            box-shadow: 0 6px 16px rgba(109, 40, 217, 0.35);
        }

        button[type="submit"]:not(.btn-delete):active {
            box-shadow: 0 2px 8px rgba(109, 40, 217, 0.25);
        }

        /* Botón de eliminar */
        .btn-delete {
            background: rgba(220, 38, 38, 0.85);
            color: #ffffff;
            padding: 0.55rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
            backdrop-filter: blur(10px);
        }

        .btn-delete:hover {
            background: rgba(185, 28, 28, 0.92);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
        }

        .btn-delete:active {
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
        }

        .btn-delete svg {
            stroke-width: 2.5;
        }

        .actions-row {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .flash {
            padding: .9rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .flash.success {
            background: #dcfce7;
            color: #166534;
        }

        .flash.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            background: #fff;
            border: 1px dashed #d1d5db;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            color: #6b7280;
        }

        @media (max-width: 1200px) {
            .summary-grid,
            .mesa-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .page-wrap {
                margin: 0;
                padding: 1rem;
            }
            
            nav {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }

            .nav-links a {
                margin-left: 0;
            }

            .summary-grid,
            .mesa-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body style="background: #f9fafb; margin: 0; font-family: 'DM Sans', sans-serif;">

<header>
    <nav>
        <a href="/dashboard" class="logo">
            <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="sphereGradientMesas" cx="35%" cy="35%">
                        <stop offset="0%" style="stop-color:#9f7aea;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#7c3aed;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#5b21b6;stop-opacity:1" />
                    </radialGradient>
                    <radialGradient id="sphereShineMesas" cx="35%" cy="35%">
                        <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.6" />
                        <stop offset="50%" style="stop-color:#ffffff;stop-opacity:0" />
                    </radialGradient>
                </defs>
                <circle cx="20" cy="20" r="18" fill="url(#sphereGradientMesas)" />
                <circle cx="14" cy="12" r="8" fill="url(#sphereShineMesas)" />
                <circle cx="20" cy="20" r="18" fill="none" stroke="#4c1d95" stroke-width="1" opacity="0.3" />
            </svg>
            <span class="logo-text">Gestion_POS</span>
        </a>
        <div class="nav-links">
            <span style="color: #6b7280; font-size: 0.9rem;">Hola, 👋 <?= esc($user['name'] ?? 'Usuario') ?></span>
            <a href="/dashboard">Volver al Dashboard</a>
            <a href="/auth/logout">Cerrar Sesión</a>
        </div>
    </nav>
</header>

<div class="page-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1>Gestión de Mesas</h1>
            <p>Administrá el estado actual de cada mesa.</p>
        </div>
        <a href="/mesas/crear" class="btn-add-mesa">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva Mesa
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="summary-grid">
        <div class="summary-card">
            <strong>Libres</strong>
            <div><?= esc($resumen['libre'] ?? 0) ?></div>
        </div>
        <div class="summary-card">
            <strong>Ocupadas</strong>
            <div><?= esc($resumen['ocupada'] ?? 0) ?></div>
        </div>
        <div class="summary-card">
            <strong>Reservadas</strong>
            <div><?= esc($resumen['reservada'] ?? 0) ?></div>
        </div>
        <div class="summary-card">
            <strong>Inactivas</strong>
            <div><?= esc($resumen['inactiva'] ?? 0) ?></div>
        </div>
    </div>

    <?php if (!empty($mesas) && is_array($mesas)): ?>
        <div class="mesa-grid">
            <?php foreach ($mesas as $mesa): ?>
                <div class="mesa-card">
                    <div class="mesa-num">
                        Mesa <?= str_pad((string) ($mesa['numero'] ?? 0), 2, '0', STR_PAD_LEFT) ?>
                    </div>

                    <div>Capacidad: <?= esc($mesa['capacidad'] ?? '-') ?></div>

                    <div class="mesa-estado estado-<?= esc($mesa['estado'] ?? 'libre') ?>">
                        <?= ucfirst(esc($mesa['estado'] ?? 'libre')) ?>
                    </div>

                    <?php if (!empty($mesa['id_pedido'])): ?>
                        <div style="font-size: 0.9rem; color: #666; margin: 0.5rem 0;">
                            Pedido activo: <strong>#<?= esc($mesa['id_pedido']) ?></strong>
                        </div>
                    <?php else: ?>
                        <div style="font-size: 0.9rem; color: #999; margin: 0.5rem 0;">
                            Sin pedido activo
                        </div>
                    <?php endif; ?>

                    <div class="actions-row">
                        <form method="post" action="<?= base_url('/mesas/cambiar-estado/' . $mesa['id_mesa']) ?>" style="flex: 1; display: flex; gap: 0.5rem;">
                            <?= csrf_field() ?>
                            <select name="estado" required style="flex: 1;">
                                <option value="libre" <?= (($mesa['estado'] ?? '') === 'libre') ? 'selected' : '' ?>>Libre</option>
                                <option value="ocupada" <?= (($mesa['estado'] ?? '') === 'ocupada') ? 'selected' : '' ?>>Ocupada</option>
                                <option value="reservada" <?= (($mesa['estado'] ?? '') === 'reservada') ? 'selected' : '' ?>>Reservada</option>
                                <option value="inactiva" <?= (($mesa['estado'] ?? '') === 'inactiva') ? 'selected' : '' ?>>Inactiva</option>
                            </select>
                            <button type="submit">Actualizar</button>
                        </form>

                        <!-- Formulario de eliminación con validación de seguridad -->
                        <form method="post" action="<?= base_url('/mesas/eliminar/' . $mesa['id_mesa']) ?>" style="display: flex;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta mesa? Esta acción no se puede deshacer.');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn-delete">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3>No hay mesas registradas</h3>
            <p>Cargá mesas en la base de datos para verlas acá.</p>
            <a href="/mesas/crear" class="btn-add-mesa" style="margin-top: 1rem;">+ Crear Primera Mesa</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>