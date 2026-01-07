# 📧 Guía Rápida: Configurar y Probar Emails Localmente

## 🎯 Opción 1: Mailtrap (RECOMENDADO - Más fácil)

### Paso 1: Crear cuenta en Mailtrap

1. Ir a https://mailtrap.io
2. Registrarse gratis (con Google o email)
3. Ir a "Email Testing" → "Inboxes" → "My Inbox"
4. Click en "Show Credentials"
5. Copiar las credenciales SMTP

### Paso 2: Configurar .env

Abrir tu archivo `.env` y REEMPLAZAR las líneas de MAIL con:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_de_mailtrap
MAIL_PASSWORD=tu_password_de_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pcflex.com"
MAIL_FROM_NAME="PcFlex - Sistema de Préstamos"
```

### Paso 3: Limpiar caché

```bash
php artisan config:clear
php artisan cache:clear
```

### Paso 4: Probar email desde tinker

```bash
php artisan tinker

# Copiar y pegar este código:
use Illuminate\Support\Facades\Mail;

Mail::raw('Este es un email de prueba desde PcFlex!', function($message) {
    $message->to('test@example.com')
            ->subject('Prueba de Email Local');
});

# Si sale "= null" es porque funcionó!
# Ir a Mailtrap.io y verás el email
```

---

## 🎯 Opción 2: Gmail (Si ya tienes Gmail)

### Paso 1: Habilitar contraseñas de aplicación

1. Ir a https://myaccount.google.com/security
2. Activar "Verificación en 2 pasos" (si no la tienes)
3. Ir a "Contraseñas de aplicaciones"
4. Seleccionar "Correo" y "Windows"
5. Copiar la contraseña generada (16 caracteres)

### Paso 2: Configurar .env

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=contraseña_de_16_caracteres_que_generaste
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="tucorreo@gmail.com"
MAIL_FROM_NAME="PcFlex - Sistema de Préstamos"
```

### Paso 3: Limpiar caché y probar

```bash
php artisan config:clear
php artisan tinker

Mail::raw('Prueba desde Gmail', function($m) {
    $m->to('tucorreo@gmail.com')->subject('Test PcFlex');
});
```

---

## ✅ Probar Emails Reales de tu App

### 1. Probar Email de Devolución

```bash
php artisan tinker

# Obtener un servicio de prueba (préstamo activo)
$service = App\Models\Services::where('status', 'pendiente')->first();

# Si no tienes servicios, crear datos de prueba primero
# Ver el archivo: database/seeders/DatabaseSeeder.php

# Enviar email de devolución de prueba
$usuario = $service->users;
$equipo = $service->equipment;

Mail::to($usuario->contacts->email_con)
    ->send(new App\Mail\DevolucionComprobante($service, $usuario, $equipo));

echo "Email enviado! Verifica tu inbox de Mailtrap o Gmail";
```

### 2. Probar Email de Recordatorio

```bash
php artisan tinker

$service = App\Models\Services::where('status', 'pendiente')->first();
$usuario = $service->users;
$equipo = $service->equipment;

Mail::to($usuario->contacts->email_con)
    ->send(new App\Mail\RecordatorioDevolucion($service, $usuario, $equipo, 24));

echo "Recordatorio enviado!";
```

### 3. Probar Comando de Recordatorios Automáticos

```bash
# Ejecutar el comando que envía recordatorios
php artisan app:enviar-recordatorios-devolucion

# Verás en consola cuántos emails se enviaron
# Revisa Mailtrap para ver los emails
```

---

## 🐛 Solución de Problemas

### Error: "Connection could not be established with host"

**Solución:**

```bash
# Verificar configuración
php artisan config:clear
php artisan cache:clear

# Ver logs
tail -f storage/logs/laravel.log
```

### Error: "Authentication failed"

-   Gmail: Verificar que usaste la contraseña de aplicación (no tu contraseña normal)
-   Mailtrap: Verificar credenciales en mailtrap.io

### Los emails no llegan

1. Verificar que el .env tiene las credenciales correctas
2. Ejecutar `php artisan config:clear`
3. Verificar logs: `storage/logs/laravel.log`
4. En Mailtrap, revisar la pestaña "Inbox"

### Ver contenido del email en logs

Si quieres ver el HTML del email en logs (sin enviarlo):

```env
MAIL_MAILER=log
```

Luego revisar: `storage/logs/laravel.log`

---

## 📝 Checklist de Pruebas

-   [ ] Configurar credenciales en .env
-   [ ] Limpiar caché: `php artisan config:clear`
-   [ ] Probar email simple con tinker
-   [ ] Verificar email llegó a Mailtrap/Gmail
-   [ ] Probar email de devolución
-   [ ] Probar email de recordatorio
-   [ ] Probar comando automático de recordatorios
-   [ ] Verificar que los emails se ven bien (HTML)
-   [ ] Verificar que los enlaces funcionan
-   [ ] Todo funciona ✅ → Listo para producción!

---

## 🚀 Siguiente Paso: Despliegue

Una vez que los emails funcionen localmente, podemos:

1. Subir el proyecto a GitHub
2. Desplegar en Railway.app
3. Configurar las mismas credenciales de email en Railway
4. ¡Listo para producción!

---

**Tiempo estimado:** 10-15 minutos  
**Dificultad:** ⭐⭐ (Fácil)  
**Requiere:** Cuenta de Mailtrap (gratis) o Gmail
