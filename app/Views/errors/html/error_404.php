<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?= esc(lang('Errors.pageNotFound')) ?> | Gestion_POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --text: #111827;
            --muted: #6b7280;
            --border: #ececf3;
            --white: #ffffff;
            --soft: #f6f1ff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            min-height: 100%;
        }

        body {
            background: linear-gradient(180deg, #fcfcff 0%, #f8f8fc 100%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            padding: 24px;
        }

        .wrap {
            max-width: 1180px;
            margin: 40px auto;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(17, 24, 39, 0.06);
        }

        .topbar {
            height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: var(--text);
            text-decoration: none;
        }

        .brand-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #9f67ff, var(--primary));
            box-shadow: 0 0 18px rgba(124, 58, 237, 0.35);
        }

        .nav {
            display: flex;
            gap: 14px;
        }

        .nav a {
            text-decoration: none;
            color: #6b7280;
            font-size: 14px;
            padding: 10px 14px;
            border-radius: 999px;
            transition: .25s ease;
        }

        .nav a:hover {
            color: var(--primary);
            background: var(--soft);
        }

        .hero {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            align-items: center;
            gap: 20px;
            padding: 70px 56px 56px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            background: var(--soft);
            color: var(--primary);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
        }

        .badge span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary);
        }

        .error-code {
            font-size: 110px;
            line-height: .95;
            font-weight: 800;
            letter-spacing: -3px;
            color: var(--primary);
            margin-bottom: 18px;
        }

        h1 {
            font-size: 56px;
            line-height: 1.08;
            font-weight: 700;
            letter-spacing: -1.6px;
            margin-bottom: 18px;
        }

        h1 strong {
            color: var(--primary);
        }

        .description {
            font-size: 19px;
            line-height: 1.75;
            color: var(--muted);
            max-width: 580px;
            margin-bottom: 28px;
        }

        .message-box {
            background: #fcfcff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px 20px;
            color: #5b6472;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            border-radius: 999px;
            padding: 14px 24px;
            font-size: 15px;
            font-weight: 700;
            transition: .25s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 12px 25px rgba(124, 58, 237, 0.25);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #fff;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            color: var(--primary);
            border-color: #d8c7ff;
            transform: translateY(-2px);
        }

        .visual {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .illustration {
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
            border-radius: 24px;
            background: linear-gradient(180deg, #faf7ff 0%, #f4efff 100%);
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.08);
            text-align: center;
        }

        .glass {
            font-size: 110px;
            line-height: 1;
            margin-bottom: 18px;
        }

        .visual h2 {
            font-size: 28px;
            color: var(--primary);
            font-weight: 800;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border-top: 1px solid var(--border);
        }

        .stat {
            padding: 28px 16px;
            text-align: center;
            border-right: 1px solid var(--border);
        }

        .stat:last-child {
            border-right: none;
        }

        .stat strong {
            display: block;
            font-size: 22px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .stat span {
            font-size: 13px;
            color: #8b93a1;
        }

        .footer {
            padding: 22px 32px 28px;
            border-top: 1px solid var(--border);
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }

        @media (max-width: 980px) {
            .hero {
                grid-template-columns: 1fr;
                padding: 46px 28px 32px;
            }

            .nav {
                display: none;
            }

            .error-code {
                font-size: 82px;
            }

            h1 {
                font-size: 40px;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .stat:nth-child(2) {
                border-right: none;
            }
        }

        @media (max-width: 580px) {
            body {
                padding: 12px;
            }

            .wrap {
                margin: 10px auto;
                border-radius: 20px;
            }

            .hero {
                padding: 34px 18px 24px;
            }

            .error-code {
                font-size: 64px;
            }

            h1 {
                font-size: 31px;
            }

            .description {
                font-size: 16px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>

<div class="wrap">

    <header class="topbar">
        <a class="brand" href="<?= base_url('/') ?>">
            <span class="brand-dot"></span>
            <span>Gestion_POS</span>
        </a>

        <nav class="nav">
            <a href="<?= base_url('/') ?>">Inicio</a>
            <a href="<?= base_url('caracteristicas') ?>">Características</a>
            <a href="<?= base_url('contacto') ?>">Contacto</a>
            <a href="<?= base_url('login') ?>">Iniciar sesión</a>
        </nav>
    </header>

    <main class="hero">

        <section>
            <div class="badge">
                <span></span>
                Error del sistema · Página no encontrada
            </div>

            <div class="error-code">404</div>

            <h1>Página no <strong>encontrada</strong></h1>

            <p class="description">
                Lo sentimos, la página que estás buscando no existe, fue movida
                o la ruta ingresada no es válida. Volvé al inicio para continuar
                gestionando tu bar.
            </p>

            <div class="message-box">
                <?php if (ENVIRONMENT !== 'production' && isset($message)) : ?>
                    <?= nl2br(esc($message)) ?>
                <?php else : ?>
                    <?= esc(lang('Errors.sorryCannotFind')) ?>
                <?php endif; ?>
            </div>

            <div class="actions">
                <a href="<?= base_url('/') ?>" class="btn btn-primary">Volver al inicio</a>
                <a href="javascript:history.back()" class="btn btn-secondary">Ir atrás</a>
            </div>
        </section>

        <section class="visual">
            <div class="illustration">
                <div class="glass">🍸</div>
                <h2>Ruta perdida</h2>
            </div>
        </section>

    </main>

    <section class="stats">
        <div class="stat">
            <strong>500+</strong>
            <span>Pedidos / día</span>
        </div>
        <div class="stat">
            <strong>50+</strong>
            <span>Mesas soportadas</span>
        </div>
        <div class="stat">
            <strong>100%</strong>
            <span>Tiempo real</span>
        </div>
        <div class="stat">
            <strong>24/7</strong>
            <span>Soporte</span>
        </div>
    </section>

    <div class="footer">
        © <?= date('Y') ?> Gestion_POS · Sistema de gestión para bares, pubs y restaurantes
    </div>

</div>

</body>
</html>