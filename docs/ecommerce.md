# E-commerce interno — sistema_mayorista

## Instalación
Después de actualizar master, ejecutar en cada instalación:

    php artisan migrate
    php artisan storage:link
    php artisan optimize:clear

Si storage:link indica que el enlace ya existe, no es necesario repetirlo.
Las fotos se guardan en el disco public de Laravel bajo storage/app/public/ecommerce.
En Windows/XAMPP verificar que public/storage apunte a storage/app/public.

## Pantallas
- /ecommerce: catálogo agrupado por los primeros 11 caracteres (código base + color), con talles, stock total registrado, precio real desde artículos y filtro por línea, imagen, precio y texto.
- /ecommerce/importar: carga simultánea de hasta 20 imágenes JPG/PNG/WEBP (máximo 30 MB cada una) o ZIP de hasta 200 MB y hasta 500 entradas.
- La línea se puede elegir por lote o individualmente. Si no se indicó línea al importar, se conserva la clasificación anterior o se busca por la OT en carga_fotos.
- Los productos sin foto y sin clasificación también figuran en el catálogo.
- Una nueva imagen del mismo código base reemplaza la fotografía anterior.
- Solo se aceptan códigos existentes en la tabla artículos; no se generan artículos automáticamente.

## Nombres aceptados
Ejemplo completo:
    100613678CV V35 C13.388 OT26697 BERMUDA ECO VARÓN 08-10-26.jpg

Datos reconocidos:
- codigo_base = 100613678CV
- valor_v = V35
- valor_c = C13.388
- ot = 26697
- descripcion_archivo = BERMUDA ECO VARÓN
- fecha_archivo = 2026-10-08

Alternativa simple:
    100613678CV.png

También se aceptan códigos de 9 dígitos, con o sin color, fecha al comienzo/final de la descripción,
OT/fecha/descripción opcionales, valor "V 89" separado y valor numérico sin prefijo V.
Cuando la imagen lleva 9 dígitos y el ERP contiene variantes de color, la foto genérica se
muestra en todos los colores que no tengan imagen específica. Se conservan las imágenes
específicas de 11 dígitos como prioridad.
Si la línea no se indicó, se busca en la OT y luego en el prefijo de la descripción.

Los valores V y C se guardan textualmente. **No modifican** articulos.art_precio ni articulos.prec_vent.
La fotografía es una por código base + color, compartida visualmente con las variantes de talle.

## Permisos existentes
- articulos index: ver catálogo
- articulos create: importar fotografías
- articulos edit: clasificar líneas
- articulos destroy: quitar fotografía

## Nota de servidor
Las fotos NO deben versionarse en Git. El almacenamiento es local al servidor que recibe la importación. Si se accede desde varias instalaciones, compartir el almacenamiento public o configurar un disco central.
Si la carga no llega al servidor, revisar upload_max_filesize, post_max_size y max_file_uploads de PHP y habilitar extensión ZIP para paquetes.

## Diseño tipo tienda
El catálogo usa navegación horizontal por líneas, buscador, filtros laterales, tarjetas visuales
y modal de detalle para cada familia/color. Los controles de clasificación y eliminación
de fotografía se mantienen en el modal (sujetos a permisos).
El botón "Vista tienda" oculta temporalmente el menú administrativo y permite volver al panel.
No hay carrito ni checkout; el catálogo es interno y de consulta.
