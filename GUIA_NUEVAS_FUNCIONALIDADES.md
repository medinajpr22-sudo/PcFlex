# 🚀 NUEVAS FUNCIONALIDADES IMPLEMENTADAS - PcFlex

## 📋 Resumen de Cambios

Se han implementado las siguientes funcionalidades avanzadas en el sistema PcFlex:

1. ✅ **Control de Tiempo con Fechas Límite**
2. ✅ **Recordatorios Automáticos por Email**
3. ✅ **Alertas de Vencimiento**
4. ✅ **Notificaciones para Bibliotecarios**
5. ✅ **Dashboard con Indicadores Visuales**
6. ✅ **Sistema de Reservas en Línea**

---

## 🔧 PASOS PARA ACTIVAR TODO

### 1. Ejecutar las Migraciones

```bash
php artisan migrate
```

Esto creará:

-   Nuevos campos en la tabla `services` (loan_duration_hours, expected_return_date, reminder_sent, overdue_alert_sent)
-   Nueva tabla `reservations` para el sistema de reservas

### 2. Configurar el Envío de Correos

Edita tu archivo `.env` con las credenciales SMTP (ver CONFIGURACION_CORREO.md):

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

### 3. Activar el Programador de Tareas (Task Scheduler)

Para que los recordatorios se envíen automáticamente cada hora:

**En Windows (XAMPP):**

1. Abre el Programador de tareas de Windows
2. Crea una nueva tarea básica
3. Configúrala para ejecutarse cada hora
4. Acción: Ejecutar programa
5. Programa: `C:\xampp\php\php.exe`
6. Argumentos: `C:\xampp\htdocs\app_vue\artisan schedule:run`

**En Linux/Mac:**

Edita el crontab:

```bash
crontab -e
```

Agrega esta línea:

```
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Prueba Manual del Sistema de Recordatorios

Puedes probar el comando manualmente:

```bash
php artisan prestamos:enviar-recordatorios
```

Verás la salida del comando mostrando cuántos recordatorios y alertas se enviaron.

### 5. Agregar Rutas para Reservas

Agrega estas rutas en `routes/web.php`:

```php
use App\Http\Controllers\ReservationController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas de reservas
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{id}/approve', [ReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{id}/reject', [ReservationController::class, 'reject'])->name('reservations.reject');
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{id}/complete', [ReservationController::class, 'complete'])->name('reservations.complete');
});
```

---

## ⚙️ CÓMO FUNCIONA

### 1. Control de Tiempo por Rol

Cuando se crea un préstamo, el sistema calcula automáticamente la fecha límite según el rol del usuario:

| Rol              | Duración del Préstamo |
| ---------------- | --------------------- |
| Estudiante       | 6 horas               |
| Profesor/Docente | 24 horas (1 día)      |
| Administrativo   | 48 horas (2 días)     |
| Investigador     | 72 horas (3 días)     |

Esto se hace en **PrestamosController::getLoanDurationByRole()**

### 2. Recordatorios Automáticos

El comando `prestamos:enviar-recordatorios` se ejecuta **cada hora** y hace lo siguiente:

**A. Recordatorios de Devolución (1 hora antes)**

-   Busca préstamos que vencen en 1 hora
-   Envía email al usuario con comprobante
-   Marca `reminder_sent = true`

**B. Alertas de Vencimiento (después de vencer)**

-   Busca préstamos ya vencidos
-   Envía email urgente al usuario
-   Marca `overdue_alert_sent = true`

**C. Alertas a Bibliotecarios**

-   Si hay préstamos vencidos
-   Envía un resumen a todos los bibliotecarios/coordinadores

### 3. Dashboard Mejorado

El dashboard ahora muestra:

-   ⚠️ **Alertas superiores** con resumen de préstamos vencidos y próximos a vencer
-   🕒 **Columna "Tiempo Restante"** en cada préstamo con:
    -   🟢 Verde: Más de 3 horas restantes
    -   🟡 Amarillo: Entre 1 y 3 horas
    -   🟠 Naranja: Menos de 1 hora
    -   🔴 Rojo: Vencido

### 4. Sistema de Reservas

Los usuarios pueden reservar equipos en línea:

**Proceso:**

1. Usuario completa formulario de reserva (tipo de equipo, fecha deseada)
2. Reserva queda en estado "pendiente"
3. Bibliotecario revisa y asigna un equipo específico
4. Reserva pasa a "aprobada"
5. Usuario recoge el equipo → "completada"

**Estados de reserva:**

-   `pendiente`: Esperando aprobación
-   `aprobada`: Equipo asignado, listo para recoger
-   `rechazada`: Reserva denegada
-   `completada`: Usuario recogió el equipo
-   `cancelada`: Usuario canceló

---

## 📧 TIPOS DE CORREOS QUE SE ENVÍAN

### 1. Comprobante de Devolución

**Cuándo:** Inmediatamente después de devolver un equipo
**Para:** Usuario que devolvió
**Contenido:** Confirmación, detalles del equipo, fechas

### 2. Recordatorio de Devolución

**Cuándo:** 1 hora antes de la fecha límite
**Para:** Usuario con préstamo
**Contenido:** Tiempo restante, detalles del equipo

### 3. Alerta de Vencimiento

**Cuándo:** Cuando el préstamo ya venció
**Para:** Usuario moroso
**Contenido:** Advertencia urgente, posibles sanciones

### 4. Alerta para Bibliotecarios

**Cuándo:** Cuando hay préstamos vencidos
**Para:** Todos los bibliotecarios y coordinadores
**Contenido:** Tabla con todos los préstamos vencidos

---

## 📱 PARA WHATSAPP (Opcional - Futuro)

Si quieres agregar notificaciones por WhatsApp, puedes usar:

-   **Twilio**: Servicio de pago, fácil de integrar
-   **WhatsApp Business API**: Requiere aprobación de Meta
-   **Baileys** (Node.js): Solución gratuita pero no oficial

Ejemplo con Twilio:

```bash
composer require twilio/sdk
```

```php
use Twilio\Rest\Client;

$twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
$message = $twilio->messages->create(
    "whatsapp:+573224110856", // Número del usuario
    [
        "from" => "whatsapp:+14155238886",
        "body" => "Recordatorio: Tu préstamo vence en 1 hora"
    ]
);
```

---

## 🎨 PERSONALIZACIÓN

### Cambiar Tiempos de Préstamo

Edita `app/Http/Controllers/PrestamosController.php` en la función `getLoanDurationByRole()`:

```php
switch ($roll) {
    case 'estudiante':
        return 6; // Cambia a las horas que quieras
    case 'profesor':
        return 24;
    // etc...
}
```

### Cambiar Cuándo Se Envían Recordatorios

Edita `app/Console/Commands/EnviarRecordatoriosDevolucion.php`:

```php
// Línea ~48: Cambiar el rango de horas
return $horasRestantes > 0 && $horasRestantes <= 1.5; // Cambiar 1.5 por el valor deseado
```

### Cambiar Frecuencia de Recordatorios

Edita `routes/console.php`:

```php
Schedule::command('prestamos:enviar-recordatorios')
    ->hourly() // Cambiar a: ->everyThirtyMinutes(), ->daily(), etc.
```

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Los recordatorios no se envían automáticamente

**Verificar:**

1. ¿Está configurado el cron/task scheduler?
2. Ejecuta manualmente: `php artisan prestamos:enviar-recordatorios`
3. Revisa logs: `storage/logs/laravel.log`

### Los correos no llegan

**Verificar:**

1. Configuración SMTP en `.env`
2. Los usuarios tienen email en la tabla `contacts`
3. Ejecuta prueba: `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('tu@email.com')->subject('Test'));`

### Error en migraciones

**Solución:**

```bash
php artisan migrate:fresh --seed
```

**⚠️ CUIDADO: Esto borrará todos los datos**

### El Dashboard no muestra las alertas

**Verificar:**

1. Limpiar caché: `php artisan cache:clear`
2. Recompilar assets: `npm run build`
3. Refrescar navegador con Ctrl+F5

---

## 📊 ESTADÍSTICAS Y REPORTES

Puedes agregar más métricas al Dashboard editando `PanelPrincipalController.php`:

```php
'stats' => [
    'proximosVencer' => $prestamosProximosVencer,
    'vencidos' => $prestamosVencidos,
    'totalPrestamos' => Services::where('status', 'pendiente')->count(),
    'totalReservas' => Reservation::where('status', 'pendiente')->count(),
    // Agregar más...
],
```

---

## ✅ CHECKLIST DE VERIFICACIÓN

-   [ ] Ejecutar `php artisan migrate`
-   [ ] Configurar credenciales SMTP en `.env`
-   [ ] Probar envío manual de correos
-   [ ] Configurar task scheduler/cron
-   [ ] Probar comando: `php artisan prestamos:enviar-recordatorios`
-   [ ] Verificar que los usuarios tengan emails en la tabla contacts
-   [ ] Agregar rutas de reservas en `routes/web.php`
-   [ ] Probar creación de préstamo (verificar que calcule fecha límite)
-   [ ] Verificar que el Dashboard muestre alertas visuales
-   [ ] Probar devolución (verificar que envíe comprobante)

---

## 📞 SOPORTE

Si tienes problemas:

1. Revisa `storage/logs/laravel.log`
2. Ejecuta `php artisan config:clear` y `php artisan cache:clear`
3. Verifica que todos los modelos tengan las relaciones correctas

---

## 🚀 PRÓXIMOS PASOS SUGERIDOS

1. Crear vista frontend para Reservas (Vue/Inertia)
2. Agregar notificaciones push con Laravel Echo
3. Integrar WhatsApp con Twilio
4. Crear reportes PDF de préstamos vencidos
5. Dashboard con gráficos (Chart.js)
6. Sistema de calificaciones de equipos
7. Fotos del equipo al prestar/devolver
8. QR Codes para escaneo rápido

---

**¡Todo listo! Tu sistema ahora tiene control de tiempo completo, recordatorios automáticos y sistema de reservas. 🎉**
