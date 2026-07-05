# Guía de Despliegue e Instalación - CardNet.ec (Laravel + MySQL)

Este repositorio contiene la arquitectura dinámica de **CardNet.ec** migrada de HTML estático a **Laravel 11**, **MySQL**, **Filament Admin Panel (v3)** y simuladores táctiles interactivos con **Fabric.js Canvas**.

---

## 🚀 Requisitos previos

1.  **PHP**: Versión 8.2 o superior.
2.  **Composer**: Para la gestión de dependencias de PHP.
3.  **MySQL** o **MariaDB**: Para la base de datos relacional.
4.  **Servidor Web**: Apache, Nginx o el panel de control de Hostinger.

---

## 📦 Instalación y Configuración del Servidor

### Paso 1: Clonar y Preparar el Entorno
Sube todo el contenido del repositorio al directorio del servidor (en Hostinger, la carpeta raíz suele ser `public_html`).

Copia el archivo de configuración `.env`:
```bash
cp .env.example .env
```

### Paso 2: Instalar Dependencias
Ejecuta Composer para instalar Laravel y Filament:
```bash
composer install --no-dev --optimize-autoloader
```

### Paso 3: Generar la Clave de Seguridad
```bash
php artisan key:generate
```

### Paso 4: Configurar la Base de Datos
Edita tu archivo `.env` en la raíz e introduce los datos de conexión de Hostinger:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### Paso 5: Correr Migraciones y Cargar Datos Iniciales (Seeders)
Este comando creará todas las tablas relacionales y cargará el catálogo de termos, agendas, placas, kits, materiales, y configuraciones por defecto.
```bash
php artisan migrate --seed
```

### Paso 6: Crear el Enlace Simbólico de Almacenamiento
Necesario para que las imágenes subidas por el panel de Filament sean visibles públicamente en el frontend:
```bash
php artisan storage:link
```

---

## 🛡️ Acceso al Dashboard de Administrador

El panel de administración seguro está localizado en la ruta:
👉 **`https://cardnet.ec/admin`** (o `http://localhost:8000/admin` localmente)

### Credenciales por Defecto (Cargadas vía Seeder)
*   **Usuario**: `admin@cardnet.ec`
*   **Contraseña**: `CardNetSecure2026!`

> [!CAUTION]
> **Cambio de Contraseña**: Una vez ingreses por primera vez al dashboard, es altamente recomendable cambiar la contraseña del perfil de administrador.

---

## 🛠️ Estructura del Simulador Táctil (Fabric.js)

El simulador interactivo de productos está implementado en la página `simulador.html` (y mapeado a la vista Blade `/simulador`), cargando de forma fluida la librería de Canvas **Fabric.js** desde un CDN seguro para optimizar el rendimiento de renderización.

*   **Restricción de Área**: Cuenta con coordenadas de delimitación para evitar que el usuario desplace o escale el logotipo fuera de las zonas físicas de grabado de cada artículo (por ejemplo, el centro del termo o la tapa de la agenda).
*   **Generador QR**: El simulador de carnets PVC genera dinámicamente un código QR de alta definición usando la librería **QRCodeJS**, incrustándolo automáticamente en la vista previa del carnet.
