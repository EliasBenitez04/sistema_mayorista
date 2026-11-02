# Carrito E-commerce hacia Pedidos

## Flujo
1. Abrir /ecommerce y elegir "Agregar al carrito" sobre un producto.
2. Elegir talle/variante (código de artículo exacto) y cantidad en la vista rápida.
3. Repetir con otros talles y productos; el carrito se mantiene en la sesión del usuario.
4. Pulsar "Mi carrito" para ver líneas, modificar cantidades, quitar variantes y consultar subtotal estimado.
5. Pulsar "Continuar y completar cliente": POST /ecommerce/carrito/continuar redirige a /pedido_compras/create.
6. El sistema pasa cada código completo y cantidad al detalle del pedido, fusionándolo con el borrador existente sin duplicarlo. El precio se lee de articulos.prec_vent, no del cliente ni de la imagen.
7. Seleccionar cliente, condición, descuento y observación, y guardar por el flujo original.
8. Tras guardar correctamente, se limpia la sesión del carrito.

## API del carrito
Las rutas autenticadas usan permisos existentes "pedido_compras create", CSRF y sesión Laravel:
- GET /ecommerce/carrito
- POST /ecommerce/carrito {codigo, cantidad}
- PATCH /ecommerce/carrito/{codigo} {cantidad}
- DELETE /ecommerce/carrito/{codigo}
- DELETE /ecommerce/carrito
- POST /ecommerce/carrito/continuar

Todas las modificaciones consultan el código real en la base de datos y no aceptan precios desde el navegador.
Límites: 100 variantes distintas y 9.999 unidades por variante.

## Borrador
La lógica de pedido_compras/borrador.blade.php sigue restaurando primero los campos del cliente y el detalle anterior.
Luego emite pedido:borrador-restaurado para fusionar los artículos enviados desde el carrito.
El traspaso es de un solo uso (session()->pull('ecommerce_transfer')), por lo que recargar la pantalla no vuelve a duplicar líneas.
El índice de pedidos ya limpia el borrador pendiente al regresar del flujo exitoso.

## Alcance
No crea pedidos al agregar al carrito. No gestiona pago, compra web pública ni reserva stock.
El precio/stock mostrado es informativo; el pedido se guarda por la lógica original.


## Carga rápida (02/11/2026)
El catálogo ahora replica el patrón de selección rápida del buscador de Pedidos:
- Un selector global de **Cantidad por clic** (con −/+, valores 1, 3, 6, 12 y valor manual).
- Un solo clic sobre el talle de cualquier tarjeta agrega esa cantidad al carrito; no hace falta abrir el detalle.
- Las cantidades del mismo código se acumulan en sesión Laravel.
- El número verde sobre cada talle indica cuántas unidades ya están cargadas en el carrito.
- La barra inferior fija muestra unidades/subtotal y permite revisar o ir directamente al formulario de cliente.
- En la lista de productos se muestran solo talles con disponibilidad positiva para pedidos, calculada con la misma lógica que el buscador de pedido: stock general - stock de sucursal del usuario. Los demás se identifican como «Sin disp.».
- Se amplió la paginación a 36 familias por página para reducir la navegación.

**Concurrencia del navegador:** las solicitudes AJAX se encolan de forma serial para no perder clics rápidos. El botón «Continuar al pedido» queda deshabilitado mientras haya cambios pendientes de guardar. La transición al pedido continúa usando POST con CSRF y la sesión actual; los precios finales se leen desde la BD.

La vista ampliada y la edición tradicional del carrito siguen disponibles. No se cambia la lógica de guardado del pedido.
