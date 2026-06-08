-- ============================================================
--  bar_pos — Procedimientos Almacenados
--  MariaDB  |  Ejecutar luego de importar bar_pos.sql
-- ============================================================

USE bar_pos;

DROP PROCEDURE IF EXISTS sp_resumen_dashboard;
DROP PROCEDURE IF EXISTS sp_cerrar_pedido;

-- ------------------------------------------------------------
--  sp_resumen_dashboard(IN p_fecha DATE)
--
--  Devuelve los cinco KPIs escalares del dashboard en una
--  única fila, evitando múltiples round-trips a la BD.
--
--  Columnas devueltas:
--    ventas_hoy     DECIMAL  – suma de pagos registrados en p_fecha
--    pedidos_hoy    INT      – pedidos abiertos ese día
--    alertas_stock  INT      – productos por debajo del stock mínimo
--    mesas_ocupadas INT      – mesas en estado 'ocupada' en este momento
--    mesas_total    INT      – total de mesas registradas
-- ------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE sp_resumen_dashboard(IN p_fecha DATE)
BEGIN
    SELECT
        (SELECT COALESCE(SUM(monto), 0)
         FROM pagos
         WHERE DATE(fecha_pago) = p_fecha)            AS ventas_hoy,

        (SELECT COUNT(*)
         FROM pedidos
         WHERE DATE(fecha_apertura) = p_fecha)        AS pedidos_hoy,

        (SELECT COUNT(*)
         FROM stock
         WHERE cantidad_disponible < cantidad_minima) AS alertas_stock,

        (SELECT COUNT(*)
         FROM mesas
         WHERE estado = 'ocupada')                    AS mesas_ocupadas,

        (SELECT COUNT(*) FROM mesas)                  AS mesas_total;
END$$

DELIMITER ;

-- ------------------------------------------------------------
--  sp_cerrar_pedido(IN p_id_pedido INT)
--
--  Cierra un pedido en una única transacción atómica:
--    1. Verifica existencia y que el pedido esté abierto.
--    2. Calcula el total sumando los subtotales de detalle_pedidos.
--    3. Actualiza pedidos (fecha_cierre + id_estado_pedido).
--    4. Libera la mesa asociada (si el pedido tiene id_mesa).
--    5. Descuenta stock para cada ítem del pedido.
--    6. Inserta un movimiento 'salida' en movimientos_stock.
--
--  Columnas devueltas (una fila):
--    resultado  VARCHAR  – 'OK' | 'ERROR:no_existe' |
--                          'ERROR:ya_cerrado' | 'ERROR:transaccion'
--    total      DECIMAL  – total calculado del pedido (0 si error)
-- ------------------------------------------------------------
DELIMITER $$

CREATE PROCEDURE sp_cerrar_pedido(IN p_id_pedido INT)
BEGIN
    DECLARE v_fecha_cierre  DATETIME      DEFAULT NULL;
    DECLARE v_id_mesa       INT           DEFAULT NULL;
    DECLARE v_id_usuario    INT           DEFAULT NULL;
    DECLARE v_id_estado     INT           DEFAULT NULL;
    DECLARE v_total         DECIMAL(12,2) DEFAULT 0.00;
    DECLARE v_resultado     VARCHAR(50)   DEFAULT 'OK';
    DECLARE v_no_data       TINYINT       DEFAULT 0;
    DECLARE v_error         TINYINT       DEFAULT 0;

    DECLARE CONTINUE HANDLER FOR NOT FOUND    SET v_no_data = 1;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET v_error   = 1;

    -- 1. Verificar existencia y estado
    SELECT fecha_cierre, id_mesa, id_usuario
    INTO   v_fecha_cierre, v_id_mesa, v_id_usuario
    FROM   pedidos
    WHERE  id_pedido = p_id_pedido;

    IF v_no_data = 1 THEN
        SET v_resultado = 'ERROR:no_existe';
    ELSEIF v_fecha_cierre IS NOT NULL THEN
        SET v_resultado = 'ERROR:ya_cerrado';
    END IF;

    IF v_resultado = 'OK' THEN

        -- 2. Calcular total del pedido
        SELECT COALESCE(SUM(subtotal), 0.00)
        INTO   v_total
        FROM   detalle_pedidos
        WHERE  id_pedido = p_id_pedido;

        -- 3. Obtener id del estado 'cerrado'
        SET v_no_data = 0;
        SELECT id_estado_pedido INTO v_id_estado
        FROM   estados_pedido
        WHERE  nombre = 'cerrado'
        LIMIT  1;

        START TRANSACTION;

            -- 4. Marcar pedido como cerrado
            UPDATE pedidos
            SET    fecha_cierre     = NOW(),
                   id_estado_pedido = v_id_estado
            WHERE  id_pedido = p_id_pedido;

            -- 5. Liberar la mesa si corresponde
            IF v_id_mesa IS NOT NULL THEN
                UPDATE mesas
                SET    estado = 'libre'
                WHERE  id_mesa = v_id_mesa;
            END IF;

            -- 6. Descontar stock de cada ítem
            UPDATE stock s
            INNER JOIN detalle_pedidos dp ON dp.id_producto = s.id_producto
            SET    s.cantidad_disponible  = GREATEST(0, s.cantidad_disponible - dp.cantidad),
                   s.ultima_actualizacion = NOW()
            WHERE  dp.id_pedido = p_id_pedido;

            -- 7. Registrar salida en historial de movimientos
            INSERT INTO movimientos_stock
                (id_producto, id_pedido, tipo_movimiento, cantidad, motivo, fecha, id_usuario)
            SELECT dp.id_producto, p_id_pedido, 'salida', dp.cantidad,
                   'Cierre de pedido', NOW(), v_id_usuario
            FROM   detalle_pedidos dp
            WHERE  dp.id_pedido = p_id_pedido;

        IF v_error = 1 THEN
            ROLLBACK;
            SET v_resultado = 'ERROR:transaccion';
        ELSE
            COMMIT;
        END IF;

    END IF;

    SELECT v_resultado AS resultado, v_total AS total;
END$$

DELIMITER ;
