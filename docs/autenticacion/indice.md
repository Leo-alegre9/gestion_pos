# Índice — Módulo de Autenticación

Documentación del sistema de login, registro y sesiones de BarPOS.

## Documentos disponibles

| Archivo | Contenido |
|---|---|
| [readme.md](readme.md) | Visión general del sistema: qué incluye, archivos clave y credenciales de prueba |
| [arquitectura_autenticacion.md](arquitectura_autenticacion.md) | Diagramas de flujo de login, registro, sesión, hasheo y validaciones por capa |
| [modelo_usuario.md](modelo_usuario.md) | Referencia completa de `UsuarioModel`: métodos, parámetros y ejemplos de uso |
| [guia_implementacion.md](guia_implementacion.md) | Pasos para implementar el sistema desde cero: BD, controlador, vistas, rutas y filtros |

## Archivos de ejemplo

Los archivos de referencia listos para copiar están en [../ejemplos/](../ejemplos/):

| Archivo | Destino en el proyecto |
|---|---|
| [ejemplo_auth_controller.php](../ejemplos/ejemplo_auth_controller.php) | `app/Controllers/Auth.php` |
| [ejemplo_vista_registro.php](../ejemplos/ejemplo_vista_registro.php) | `app/Views/auth/register.php` |
| [ejemplo_routes.php](../ejemplos/ejemplo_routes.php) | Integrar en `app/Config/Routes.php` |
| [ejemplo_filtros.php](../ejemplos/ejemplo_filtros.php) | `app/Filters/AuthFilter.php` y similares |

## Historial de correcciones

| Archivo | Descripción del bug resuelto |
|---|---|
| [../correcciones/error_404_auth.md](../correcciones/error_404_auth.md) | `404 POST auth/store` por inconsistencia de rutas |
| [../correcciones/registro_no_guardaba.md](../correcciones/registro_no_guardaba.md) | Usuario no se insertaba en BD por orden incorrecto de validación |
| [../correcciones/unificacion.md](../correcciones/unificacion.md) | Unificación de campos, mensajes y variables de sesión entre capas |

## Orden de lectura recomendado

1. [readme.md](readme.md) — visión general
2. [arquitectura_autenticacion.md](arquitectura_autenticacion.md) — flujos y seguridad
3. [modelo_usuario.md](modelo_usuario.md) — referencia del modelo
4. [guia_implementacion.md](guia_implementacion.md) — pasos de implementación
