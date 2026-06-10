# Índice de documentación — BarPOS

## Autenticación

Documentación del módulo de login, registro y sesiones.

- [autenticacion/indice.md](autenticacion/indice.md) — índice del módulo con orden de lectura
- [autenticacion/readme.md](autenticacion/readme.md) — visión general
- [autenticacion/arquitectura_autenticacion.md](autenticacion/arquitectura_autenticacion.md) — flujos y diagramas
- [autenticacion/modelo_usuario.md](autenticacion/modelo_usuario.md) — referencia de `UsuarioModel`
- [autenticacion/guia_implementacion.md](autenticacion/guia_implementacion.md) — pasos de implementación

## Patrones de diseño

- [patrones/service_layer_pagos.md](patrones/service_layer_pagos.md) — Service Layer en `PrepararPagoService` y `RegistrarPagoService`
- [patrones/strategy_pagos.md](patrones/strategy_pagos.md) — Strategy en `PagoEfectivo`, `PagoTarjeta` y `PagoTransferencia`

## Sistema de pagos

- [hu_registrar_pago.md](hu_registrar_pago.md) — Historia de usuario HU-PAG-004
- [contrato_operaciones.md](contrato_operaciones.md) — Contratos de operación del caso de uso Cobrar
- [anotacion_flujo_pago.md](anotacion_flujo_pago.md) — Anotación del flujo completo de pago

## Referencia del proyecto

- [funcionalidades.md](funcionalidades.md) — Listado completo de las 36 funcionalidades del sistema
- [documentacion_metodos.md](documentacion_metodos.md) — Referencia de todos los métodos de controllers, models y services
- [diccionario_datos.md](diccionario_datos.md) — Definición de tablas y columnas de la base de datos
- [stored_procedures.md](stored_procedures.md) — Procedimientos almacenados: `sp_resumen_dashboard` y `sp_cerrar_pedido`
- [skills.md](skills.md) — Contexto del proyecto: stack, módulos y regla principal

## Pruebas

- [plan_de_pruebas.md](plan_de_pruebas.md) — Plan de pruebas del sistema
- [tests-unitarios.md](tests-unitarios.md) — Documentación de los tests unitarios

## Diagramas

- [diagramas/diagrama_clases.md](diagramas/diagrama_clases.md) — Diagrama de clases en Mermaid
- [diagramas/diagrama_clases.puml](diagramas/diagrama_clases.puml) — Diagrama de clases en PlantUML
- [diagramas/diagrama_arquitectura.puml](diagramas/diagrama_arquitectura.puml) — Arquitectura general en PlantUML
- [diagramas/SEQUENCE_PAGO.puml](diagramas/SEQUENCE_PAGO.puml) — Diagrama de secuencia del flujo de pago
- [diagramas/diagrama.png](diagramas/diagrama.png) — Imagen exportada del diagrama

## Historial de correcciones

- [correcciones/error_404_auth.md](correcciones/error_404_auth.md) — `404 POST auth/store` por inconsistencia de rutas
- [correcciones/registro_no_guardaba.md](correcciones/registro_no_guardaba.md) — Usuario no se insertaba en BD
- [correcciones/unificacion.md](correcciones/unificacion.md) — Unificación de campos, mensajes y sesión

## Ejemplos de código

Archivos de referencia para copiar al proyecto:

- [ejemplos/ejemplo_auth_controller.php](ejemplos/ejemplo_auth_controller.php) → `app/Controllers/Auth.php`
- [ejemplos/ejemplo_vista_registro.php](ejemplos/ejemplo_vista_registro.php) → `app/Views/auth/register.php`
- [ejemplos/ejemplo_routes.php](ejemplos/ejemplo_routes.php) → `app/Config/Routes.php`
- [ejemplos/ejemplo_filtros.php](ejemplos/ejemplo_filtros.php) → `app/Filters/`
