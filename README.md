# 💅 Sandy Beauty Nails – Sistema de Administración de Citas

Sistema completo en PHP puro para gestionar citas, clientes e ingresos en un salón de belleza. Incluye interfaz pública para agendar citas y dashboard administrativo con gráficas, filtros y reportes.

## 🧰 Tecnologías
- **Backend**: PHP 8.x (puro, sin frameworks)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: Bootstrap 5.x + JavaScript Vanilla
- **Arquitectura**: MVC (Model-View-Controller)
- **Pagos**: Integración con Mercado Pago
- **Seguridad**: CSRF protection, validaciones, sanitización

## 🎯 Funcionalidades Principales

### 🌐 Interfaz Pública
- **Sistema de Reservas Online**: Disponible lunes a sábado de 8:00 a 19:00 hrs
- **Validación de Clientes**: Búsqueda automática por teléfono
- **Precarga de Datos**: Para clientes recurrentes
- **Selección de Servicios**: Catálogo completo con precios
- **Disponibilidad en Tiempo Real**: Bloqueo automático de horarios ocupados
- **Pago Seguro**: Integración con Mercado Pago

### 🏢 Dashboard Administrativo
- **Gestión de Reservaciones**: Lista completa con filtros avanzados
- **Administración de Clientes**: Historial y clientes frecuentes
- **Reportes Financieros**: Ingresos por período, servicio y manicurista
- **Gráficas y Analytics**: Visualización de datos con Chart.js
- **Control de Estados**: Actualización manual de citas

## 🚀 Instalación y Configuración

### Prerrequisitos
- Servidor web con PHP 8.0+ y mod_rewrite habilitado
- MySQL 5.7+ o MariaDB 10.3+
- Extensiones PHP: PDO, PDO_MySQL, mbstring, openssl

### 1. Configuración del Servidor

#### Apache
Asegúrate de que el archivo `.htaccess` esté en la raíz del proyecto y que `mod_rewrite` esté habilitado:

```bash
# En Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx
Configura el virtual host con las siguientes reglas de reescritura:

```nginx
location / {
    try_files $uri $uri/ /index.php?route=$uri&$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

### 2. Base de Datos

#### Crear la base de datos
```sql
CREATE DATABASE sandy_beauty_nails CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Importar el schema
```bash
mysql -u root -p sandy_beauty_nails < database/schema.sql
```

O ejecuta el archivo `database/schema.sql` en tu cliente MySQL preferido.

### 3. Configuración de la Aplicación

Edita el archivo `config/config.php` con tus datos:

```php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'sandy_beauty_nails');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');

// URL de la aplicación
define('APP_URL', 'http://tu-dominio.com');

// Configuración de Mercado Pago (opcional)
define('MP_ACCESS_TOKEN', 'tu_access_token');
define('MP_PUBLIC_KEY', 'tu_public_key');
```

### 4. Configuración de Mercado Pago (Opcional)

Para habilitar los pagos en línea:

1. Crea una cuenta en [Mercado Pago Developers](https://www.mercadopago.com.mx/developers)
2. Obtén tus credenciales de prueba/producción
3. Configúralas en `config/config.php`

### 5. Permisos

Asegúrate de que el servidor web tenga permisos de escritura en directorios necesarios:

```bash
sudo chown -R www-data:www-data /path/to/sandybeautynails
sudo chmod -R 755 /path/to/sandybeautynails
```

## 👤 Acceso Administrativo

### Credenciales por Defecto
- **Usuario**: `admin`
- **Contraseña**: `admin123`

⚠️ **IMPORTANTE**: Cambia estas credenciales en producción ejecutando:

```sql
UPDATE admin_users SET password = '$2y$10$hash_de_nueva_contraseña' WHERE username = 'admin';
```

Puedes generar el hash con:
```php
echo password_hash('tu_nueva_contraseña', PASSWORD_DEFAULT);
```

## 📂 Estructura del Proyecto

```
sandybeautynails/
├── .htaccess                 # Configuración Apache
├── index.php                 # Punto de entrada
├── config/
│   └── config.php            # Configuración general
├── app/
│   ├── core/                 # Clases principales
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   └── Router.php
│   ├── controllers/          # Controladores MVC
│   │   ├── AdminController.php
│   │   ├── BookingController.php
│   │   ├── HomeController.php
│   │   └── PaymentController.php
│   ├── models/               # Modelos de datos
│   │   ├── Appointment.php
│   │   ├── Customer.php
│   │   ├── Manicurist.php
│   │   └── Service.php
│   └── views/                # Vistas HTML
│       ├── layouts/
│       ├── home/
│       ├── booking/
│       ├── admin/
│       └── errors/
├── public/                   # Archivos públicos
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── app.js
│   │   └── booking.js
│   └── images/
└── database/
    └── schema.sql            # Esquema de base de datos
```

## 🔧 Uso del Sistema

### Para Clientes
1. Visita la página principal
2. Haz clic en "Reservar Cita"
3. Ingresa tu número de teléfono
4. Completa tus datos (se autocompletarán si ya eres cliente)
5. Selecciona servicio, fecha y hora
6. Confirma y procede al pago

### Para Administradores
1. Accede a `/admin`
2. Inicia sesión con tus credenciales
3. Usa el dashboard para:
   - Ver estadísticas del día
   - Gestionar reservaciones
   - Consultar clientes
   - Generar reportes financieros

## 🛠️ Personalización

### Horarios de Negocio
Modifica en `config/config.php`:
```php
define('BUSINESS_START_HOUR', 8);
define('BUSINESS_END_HOUR', 19);
define('BUSINESS_DAYS', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
```

### Servicios y Precios
Actualiza la tabla `services` en la base de datos o crea un panel administrativo para gestionarlos.

### Colores y Estilos
Modifica las variables CSS en `public/css/style.css`:
```css
:root {
    --primary-color: #e91e63;
    --secondary-color: #f8bbd9;
    /* ... más variables */
}
```

## 🔒 Seguridad

- ✅ Protección CSRF en formularios
- ✅ Validación y sanitización de entrada
- ✅ Consultas preparadas (PDO)
- ✅ Sesiones seguras
- ✅ Headers de seguridad
- ✅ Protección de archivos sensibles

## 📊 Base de Datos

### Tablas Principales
- `customers`: Información de clientes
- `services`: Catálogo de servicios
- `manicurists`: Personal del salón
- `appointments`: Citas programadas
- `admin_users`: Usuarios administrativos
- `payment_transactions`: Historial de pagos

## 🐛 Solución de Problemas

### Error 500 - Internal Server Error
- Verifica permisos de archivos
- Revisa que mod_rewrite esté habilitado
- Consulta los logs de Apache/Nginx

### Error de Conexión a Base de Datos
- Verifica credenciales en `config/config.php`
- Asegúrate de que MySQL esté corriendo
- Confirma que la base de datos existe

### Formulario de Reservas No Funciona
- Verifica que JavaScript esté habilitado
- Revisa la consola del navegador para errores
- Confirma que las rutas AJAX sean correctas

## 📞 Soporte

Para soporte técnico o consultas:
- 📧 Email: soporte@sandybeautynails.com
- 📱 WhatsApp: (555) 123-4567
- 🌐 Sitio web: https://sandybeautynails.com

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

⭐ **¡No olvides cambiar las credenciales por defecto en producción!**
