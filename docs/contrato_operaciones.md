# Contrato de Operaciones — Funcionalidad: Cobrar

---

Contrato Mostrar Formulario de Cobro
Nombre: mostrarFormularioDeCobro(idPedido).
Responsabilidades: Recuperar los datos del pedido y renderizar el formulario de cobro con el total y los métodos de pago disponibles.
Referencias cruzadas: Caso de uso: cobrar.
Excepciones: Si el pedido no existe, indicar que no fue encontrado. Si el pedido no está cerrado, indicar que debe cerrarse antes de cobrar. Si ya existe un pago registrado, redirigir al comprobante existente.
Salida: Vista del formulario de cobro con los datos del pedido, ítems, total calculado y métodos de pago.
Precondiciones: El pedido existe y fue cerrado previamente. No existe un pago registrado para ese pedido.
Poscondiciones: -No se modifica ningún dato (operación de solo lectura). -Se muestra el formulario de cobro al usuario.

---

Contrato Procesar Cobro
Nombre: procesarCobro(idPedido, idMetodoPago).
Responsabilidades: Registrar el pago del pedido en el sistema, calculando el total en el servidor y actualizando el estado del pedido a pagado.
Referencias cruzadas: Caso de uso: cobrar.
Excepciones: Si los datos del pago no son válidos, indicar que se cometió un error y devolver al formulario. Si falla la transacción en la base de datos, indicar que no se pudo registrar el pago.
Salida: Redirección al comprobante del pago con mensaje de éxito.
Precondiciones: El pedido existe, está cerrado y no tiene un pago previo registrado. El método de pago seleccionado es válido y está activo.
Poscondiciones: -Se crea un registro de pago con el monto, método y fecha (creación de la instancia). -El estado del pedido se actualiza a pagado. -Se notifica al usuario que el pago fue registrado exitosamente.

---

Contrato Mostrar Comprobante
Nombre: mostrarComprobante(idPago).
Responsabilidades: Recuperar y mostrar los datos completos del comprobante de un pago ya registrado.
Referencias cruzadas: Caso de uso: cobrar.
Excepciones: Si el pago con el identificador dado no existe, indicar que el comprobante no fue encontrado.
Salida: Vista del comprobante con los datos del pago, pedido, ítems y total.
Precondiciones: El pago existe en el sistema y tiene un pedido asociado con al menos un ítem.
Poscondiciones: -No se modifica ningún dato (operación de solo lectura). -Se muestra el comprobante completo al usuario.
