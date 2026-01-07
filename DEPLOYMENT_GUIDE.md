# 🚀 Guía de Despliegue a Producción - PcFlex

## 📋 CHECKLIST PRE-DESPLIEGUE

### 1. ⚙️ Configuración de Entorno

```bash
# Copiar .env.example a .env
cp .env.example .env.production

# Editar .env.production con estos valores:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# Generar nueva APP_KEY
php artisan key:generate
```

### 2. 🗄️ Base de Datos

Editar en `.env.production`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1  # o la IP de tu servidor MySQL
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario_mysql
DB_PASSWORD=contraseña_segura
```

### 3. 📧 Configurar Email Real

**Opción 1: Gmail SMTP (Gratis, limitado)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=contraseña_app  # Usar App Password de Google
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tucorreo@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Opción 2: Mailtrap.io (Gratis para testing, 1000 emails/mes)**

-   Crear cuenta en https://mailtrap.io
-   Copiar credenciales SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
```

**Opción 3: SendGrid (Gratis 100 emails/día)**

-   Crear cuenta en https://sendgrid.com
-   Generar API Key

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=tu_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

### 4. 📱 WhatsApp (FALTA IMPLEMENTAR)

**Opción 1: Twilio WhatsApp API** (Más profesional)

```bash
composer require twilio/sdk
```

**Opción 2: WhatsApp Business API** (Requiere aprobación)

-   https://developers.facebook.com/docs/whatsapp

**Opción 3: Baileys (Node.js)** (Open source pero menos estable)

-   Requiere Node.js y conexión QR

### 5. 🔐 Seguridad

```bash
# Asegurar permisos
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs

# Proteger .env
chmod 600 .env

# Asegurar que .gitignore incluye:
# .env
# .env.production
# /vendor
# /node_modules
```

### 6. ⚡ Optimizaciones

```bash
# Instalar dependencias de producción
composer install --optimize-autoloader --no-dev

# Compilar assets
npm run build

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Opcional: Cachear eventos
php artisan event:cache
```

### 7. 📊 Migraciones y Seeders

```bash
# Ejecutar migraciones en producción
php artisan migrate --force

# Si necesitas datos de ejemplo (NO en producción real)
# php artisan db:seed --force
```

### 8. ⏰ Configurar Cron Jobs

En el servidor, agregar a crontab:

```bash
crontab -e

# Agregar esta línea:
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### 9. 🔄 Storage Link

```bash
# Crear enlace simbólico para archivos públicos
php artisan storage:link
```

---

## 🌐 DESPLIEGUE EN RAILWAY.APP (RECOMENDADO)

### Paso 1: Preparar Repositorio

```bash
# Inicializar Git (si no lo has hecho)
git init
git add .
git commit -m "Preparar para producción"

# Crear repositorio en GitHub
# Subir código
git remote add origin https://github.com/tu-usuario/pcflex.git
git branch -M main
git push -u origin main
```

### Paso 2: Configurar Railway

1. Ir a https://railway.app
2. Registrarse con GitHub
3. Click en "New Project"
4. Seleccionar "Deploy from GitHub repo"
5. Seleccionar tu repositorio

### Paso 3: Agregar MySQL

1. En Railway, click en "+ New"
2. Seleccionar "Database" → "MySQL"
3. Railway generará credenciales automáticamente

### Paso 4: Variables de Entorno en Railway

En el panel de Railway, agregar estas variables:

```
APP_NAME=PcFlex
APP_ENV=production
APP_KEY=base64:GENERAR_CON_php_artisan_key:generate
APP_DEBUG=false
APP_URL=https://tu-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME=PcFlex

SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### Paso 5: Configurar Build

Crear archivo `Procfile` en la raíz:

```
web: vendor/bin/heroku-php-apache2 public/
```

Crear archivo `railway.json`:

```json
{
    "build": {
        "builder": "NIXPACKS"
    },
    "deploy": {
        "startCommand": "php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=$PORT"
    }
}
```

---

## 🌐 DESPLIEGUE EN RENDER.COM

### Paso 1: Crear cuenta en Render

1. Ir a https://render.com
2. Registrarse con GitHub

### Paso 2: Crear Web Service

1. Click en "New +"
2. Seleccionar "Web Service"
3. Conectar tu repositorio de GitHub
4. Configurar:
    - **Name**: pcflex
    - **Environment**: PHP
    - **Build Command**:
        ```bash
        composer install --optimize-autoloader --no-dev && npm install && npm run build
        ```
    - **Start Command**:
        ```bash
        php artisan serve --host=0.0.0.0 --port=$PORT
        ```

### Paso 3: Agregar PostgreSQL (gratis)

1. En Render, click en "New +"
2. Seleccionar "PostgreSQL"
3. Copiar la DATABASE_URL

### Paso 4: Variables de Entorno

Agregar las mismas variables que en Railway, pero usando PostgreSQL:

```
DB_CONNECTION=pgsql
DB_HOST=... (desde DATABASE_URL)
DB_PORT=5432
DB_DATABASE=... (desde DATABASE_URL)
```

---

## 📱 IMPLEMENTAR WHATSAPP (PENDIENTE)

### Opción 1: Twilio (Recomendado)

1. Crear cuenta en https://www.twilio.com
2. Instalar SDK:

    ```bash
    composer require twilio/sdk
    ```

3. Crear servicio `app/Services/WhatsAppService.php`:

    ```php
    <?php
    namespace App\Services;

    use Twilio\Rest\Client;

    class WhatsAppService
    {
        protected $client;
        protected $from;

        public function __construct()
        {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $this->from = config('services.twilio.whatsapp_from');
            $this->client = new Client($sid, $token);
        }

        public function sendMessage($to, $message)
        {
            try {
                $this->client->messages->create(
                    "whatsapp:$to",
                    [
                        'from' => "whatsapp:$this->from",
                        'body' => $message
                    ]
                );
                return true;
            } catch (\Exception $e) {
                \Log::error('WhatsApp Error: ' . $e->getMessage());
                return false;
            }
        }
    }
    ```

4. Agregar en `config/services.php`:

    ```php
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'), // Ej: +14155238886
    ],
    ```

5. En `.env`:

    ```
    TWILIO_SID=tu_sid
    TWILIO_AUTH_TOKEN=tu_token
    TWILIO_WHATSAPP_FROM=+14155238886
    ```

6. Usar en controladores:

    ```php
    use App\Services\WhatsAppService;

    $whatsapp = new WhatsAppService();
    $whatsapp->sendMessage('+57300XXXXXXX', '¡Tu equipo ha sido prestado!');
    ```

---

## ✅ VERIFICACIÓN POST-DESPLIEGUE

### Probar Funcionalidades:

-   [ ] Login y registro funcionan
-   [ ] Crear/editar/eliminar equipos
-   [ ] Crear/editar/eliminar usuarios
-   [ ] Realizar préstamos
-   [ ] Realizar devoluciones
-   [ ] Ver estadísticas
-   [ ] Generar PDFs
-   [ ] Recibir emails (verificar en bandeja)
-   [ ] WhatsApp funciona (después de implementar)
-   [ ] Búsquedas funcionan
-   [ ] Paginación funciona
-   [ ] Permisos funcionan correctamente

### Monitorear:

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# En Railway/Render: ver logs en el panel web
```

---

## 🆘 SOLUCIÓN DE PROBLEMAS COMUNES

### Error 500 después de desplegar:

```bash
# Verificar permisos
chmod -R 775 storage bootstrap/cache

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerar caché
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Emails no se envían:

-   Verificar credenciales SMTP en .env
-   Verificar que Gmail permite "Apps menos seguras" o usar App Password
-   Revisar logs: `storage/logs/laravel.log`
-   Probar con: `php artisan tinker` → `Mail::raw('Test', function($m) { $m->to('test@test.com')->subject('Test'); });`

### Assets no cargan (CSS/JS):

```bash
# Recompilar
npm run build

# Verificar APP_URL en .env
# Verificar que /public/build existe

# En Railway, asegurar que se ejecuta npm run build
```

### Base de datos no conecta:

-   Verificar credenciales DB en .env
-   Verificar que el servidor MySQL acepta conexiones remotas
-   Verificar firewall/IP whitelist

---

## 📚 RECURSOS ADICIONALES

-   **Laravel Deployment**: https://laravel.com/docs/deployment
-   **Railway Docs**: https://docs.railway.app
-   **Render PHP**: https://render.com/docs/deploy-php
-   **Twilio WhatsApp**: https://www.twilio.com/docs/whatsapp
-   **Laravel Queues**: https://laravel.com/docs/queues
-   **Laravel Scheduler**: https://laravel.com/docs/scheduling

---

## 💡 RECOMENDACIONES FINALES

1. **Backup Automático**: Configurar backups diarios de la base de datos
2. **Monitoreo**: Usar Laravel Telescope en desarrollo, Sentry en producción
3. **CDN**: Usar Cloudflare para caché y protección
4. **Queue**: Mover emails a cola para no bloquear requests
5. **Testing**: Escribir tests antes de hacer cambios grandes
6. **Versionado**: Usar Git tags para versiones (v1.0.0, v1.1.0, etc.)
7. **Documentation**: Mantener este README actualizado

---

**Creado**: 2026-01-07  
**Autor**: Equipo de Desarrollo PcFlex  
**Versión**: 1.0
