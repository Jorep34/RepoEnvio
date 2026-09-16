# EnvíaPro - Sistema de Gestión de Envíos

Aplicativo web desarrollado en PHP + MySQL para registrar, consultar, editar y eliminar envíos.

## Datos de conexión
- Host: `jojoapp.alwaysdata.net`
- Usuario: `jojoapp`
- Base de datos: `jojoapp_enviosdb`

> La contraseña se encuentra únicamente en `config.php`.

## Estructura
- `index.php`: interfaz y operaciones CRUD.
- `config.php`: conexión MySQL y creación automática de la tabla.
- `style.css`: diseño responsive.
- `README.md`: documentación.

## Tabla
La aplicación crea automáticamente `envios` si no existe, con:
- `id`
- `destinatario`
- `direccion`
- `descripcion`
- `creado_en`
- `actualizado_en`

## Instalación en AlwaysData
1. Crea/verifica la base de datos `jojoapp_enviosdb`.
2. Sube los archivos a tu espacio web mediante FileZilla.
3. Verifica que `config.php` conserve las credenciales correctas.
4. Abre la URL pública donde subiste `index.php`.
5. La tabla `envios` se crea automáticamente.

## Funciones
- Crear envío.
- Listar envíos.
- Buscar por destinatario, dirección o descripción.
- Editar envío.
- Eliminar envío.
- Contador de envíos.
- Diseño responsive para computador y móvil.

## Nota de seguridad
Para un entorno real se recomienda no publicar las credenciales de la base de datos en un repositorio público y usar variables de entorno o configuración fuera del directorio público.
