# Sistema de Notificación de Devoluciones - PcFlex

## 📧 Configuración del Sistema de Correo Electrónico

Este sistema ha sido configurado para enviar automáticamente un correo electrónico de comprobante cuando se devuelva un equipo.

## ✅ Archivos Creados

1. **app/Mail/DevolucionComprobante.php** - Clase Mailable que maneja el envío del correo
2. **resources/views/emails/devolucion-comprobante.blade.php** - Plantilla HTML del correo electrónico
3. **app/Http/Controllers/DevolucionController.php** - Actualizado para enviar el correo

## 🔧 Configuración del Correo Electrónico

### Opción 1: Para Pruebas (Usando Mailtrap o Log)

Si estás en desarrollo y quieres probar sin enviar correos reales, puedes usar `log`:

```env
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="pcflex@example.com"
MAIL_FROM_NAME="PcFlex"
```

Los correos se guardarán en `storage/logs/laravel.log`

### Opción 2: Para Producción (Usando Gmail)

Si quieres usar Gmail para enviar correos reales:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="pcflex@tudominio.com"
MAIL_FROM_NAME="PcFlex"
```

**Importante:** Para Gmail necesitas:

1. Activar la verificación en dos pasos
2. Crear una "Contraseña de aplicación" desde https://myaccount.google.com/apppasswords
3. Usar esa contraseña de aplicación en `MAIL_PASSWORD`

### Opción 3: Usando Mailtrap (Recomendado para desarrollo)

Mailtrap es un servicio gratuito para probar correos electrónicos:

1. Regístrate en https://mailtrap.io
2. Crea un inbox
3. Copia las credenciales SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_de_mailtrap
MAIL_PASSWORD=tu_password_de_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="pcflex@example.com"
MAIL_FROM_NAME="PcFlex"
```

### Opción 4: Usando SendGrid, Mailgun, u otros servicios

Puedes usar cualquier servicio SMTP. Solo necesitas las credenciales SMTP del servicio.

## 📝 Cómo Funciona

1. Cuando un usuario devuelve un equipo a través del sistema
2. El sistema verifica que el usuario tenga un email registrado en la tabla `contacts`
3. Si tiene email, envía automáticamente un comprobante con:
    - Información del usuario (nombre, documento, teléfono, email)
    - Información del equipo (tipo, serie, características)
    - Detalles del préstamo (fecha de préstamo, fecha de devolución, estado, ambiente)
4. Si falla el envío del correo, la devolución se completa de todas formas y se registra el error en los logs

## ⚙️ Pasos para Activar

1. Edita tu archivo `.env` con las credenciales SMTP que prefieras
2. Asegúrate de que los usuarios tengan emails registrados en la tabla `contacts`
3. Realiza una devolución de prueba
4. Verifica que llegue el correo (o revisa los logs si usaste `MAIL_MAILER=log`)

## 🧪 Prueba del Sistema

Para probar que todo funciona:

```bash
# En la terminal de Laravel
php artisan tinker

# Luego ejecuta:
Mail::raw('Prueba de correo', function($message) {
    $message->to('tu-email@example.com')
            ->subject('Prueba PcFlex');
});
```

Si recibes el correo, ¡todo está configurado correctamente!

## 📋 Requisitos

-   Los usuarios deben tener un registro en la tabla `contacts` con el campo `email_con` completado
-   El servidor debe tener acceso a internet para enviar correos (si usas SMTP externo)
-   Las credenciales SMTP deben ser válidas

## 🎨 Personalización

Si deseas personalizar el diseño del correo, edita el archivo:
`resources/views/emails/devolucion-comprobante.blade.php`

Puedes modificar:

-   Colores del encabezado (línea con `background: linear-gradient`)
-   Texto del mensaje
-   Estilos CSS
-   Información mostrada

## 🐛 Solución de Problemas

**El correo no llega:**

-   Verifica las credenciales en el `.env`
-   Revisa los logs en `storage/logs/laravel.log`
-   Asegúrate de que el usuario tenga email registrado
-   Verifica que el servidor tenga acceso a internet

**Error de autenticación:**

-   Si usas Gmail, asegúrate de usar una contraseña de aplicación
-   Verifica que el puerto y host sean correctos

**El correo va a spam:**

-   Configura correctamente `MAIL_FROM_ADDRESS` con un dominio válido
-   Considera usar un servicio profesional como SendGrid o Mailgun

## 📞 Soporte

Si tienes problemas, revisa primero los logs en `storage/logs/laravel.log` para ver los errores específicos.
