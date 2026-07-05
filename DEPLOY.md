# Guía de Despliegue en Hosting Compartido - CardNet.ec (PHP + MySQL)

Hemos configurado CardNet.ec con una arquitectura de **PHP Puro (Vanilla PHP) y MySQL**. Esto permite que el sitio web funcione en **cualquier plan de alojamiento compartido convencional** (como Hostinger o GoDaddy) sin necesidad de terminal SSH, Composer o configuraciones complejas de VPS.

---

## 📋 Requisitos del Servidor
*   **Servidor Web**: Apache / Nginx (estándar en Hostinger).
*   **Versión de PHP**: PHP 8.0 o superior (recomendado 8.2).
*   **Base de datos**: MySQL o MariaDB.

---

## 🛠️ Paso a Paso para Desplegar en Hostinger

### Paso 1: Crear la Base de Datos en Hostinger
1. Ingresa a tu panel de Hostinger (hPanel).
2. Ve a la sección **Bases de datos** ➔ **Bases de datos MySQL**.
3. Crea una nueva base de datos y toma nota de:
   *   **Nombre de la base de datos**
   *   **Usuario de la base de datos**
   *   **Contraseña**
   *   **Host** (habitualmente es `localhost`).

### Paso 2: Importar la Base de Datos (`database.sql`)
1. En la misma sección del panel de Hostinger, haz clic en **Ingresar a phpMyAdmin** al lado de tu base de datos recién creada.
2. Haz clic en la pestaña **Importar** en la barra superior.
3. Selecciona el archivo **`database.sql`** ubicado en la raíz del proyecto y haz clic en **Importar** (en la parte inferior).
   *   *Esto creará automáticamente las tablas y cargará el catálogo semilla de termos, agendas, placas, materiales, carrusel y usuario de administración.*

### Paso 3: Configurar los Datos de Conexión (`db.php`)
Abre el archivo **`db.php`** en tu editor de código o administrador de archivos de Hostinger y reemplaza los valores de conexión con los que creaste en el **Paso 1**:
```php
$db_host = 'localhost'; // Habitualmente 'localhost'
$db_name = 'tu_nombre_de_base_de_datos';
$db_user = 'tu_usuario_de_base_de_datos';
$db_pass = 'tu_contraseña';
```

### Paso 4: Subir los Archivos al Servidor
1. Comprime todos los archivos del directorio en un archivo `.zip` (por ejemplo: `proyecto.zip`).
   *   *Asegúrate de incluir las carpetas `css/`, `js/`, `images/`, `admin/`, los archivos `.php` y `.html`.*
2. En tu panel de Hostinger, ve al **Administrador de Archivos** ➔ ingresa a la carpeta **`public_html`**.
3. Sube tu archivo `proyecto.zip` y descomprímelo directamente allí.
4. Asegúrate de crear una carpeta llamada **`uploads`** en la raíz (dentro de `public_html`) con permisos de escritura (755 o 777) para almacenar los logos y simulaciones que suban tus clientes.

---

## 🛡️ Acceso y Gestión del Panel de Administrador

Una vez subidos los archivos, el panel de control seguro estará disponible de forma inmediata en la dirección:
👉 **`https://tu-dominio.ec/admin`**

### Credenciales de Acceso por Defecto:
*   **Usuario**: `admin@cardnet.ec`
*   **Contraseña**: `CardNetSecure2026!`

Desde este panel podrás:
1.  **Dashboard**: Ver la lista de solicitudes de cotización más recientes enviadas por los usuarios, con sus enlaces a WhatsApp y descargas de logos.
2.  **Productos**: Añadir, editar o eliminar los termos, agendas y kits del catálogo, y activar la opción de simulador.
3.  **Carrusel Hero**: Modificar dinámicamente los banners de cabecera de la home sin tocar código.
4.  **Antes y Después**: Gestionar los bloques comparativos del taller.
