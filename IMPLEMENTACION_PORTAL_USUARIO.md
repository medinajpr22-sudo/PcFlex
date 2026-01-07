# 🚀 GUÍA DE IMPLEMENTACIÓN - NUEVAS FUNCIONALIDADES

## Portal de Autoservicio para Usuarios ✅

### 1. Ejecutar Migraciones

Primero, ejecuta las migraciones para crear las tablas y campos necesarios:

```bash
php artisan migrate
```

Esto creará:

-   Campo `password` y `remember_token` en la tabla `borrower_users`
-   Campo `return_photo` en la tabla `services`
-   Tabla `reservations`
-   Campos de control de tiempo en `services` (loan_duration_hours, expected_return_date, etc.)

### 2. Configurar el Almacenamiento de Archivos

Crea el enlace simbólico para que las fotos sean accesibles:

```bash
php artisan storage:link
```

### 3. Crear Contraseñas para Usuarios Existentes

Actualiza la base de datos para agregar contraseñas a los usuarios existentes (puedes hacerlo manualmente o crear un seeder):

```sql
UPDATE borrower_users
SET password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMESaXxQJW4s.O8LQP1FJlWX2a'  -- password: "password"
WHERE password IS NULL;
```

O mejor, crea un comando Artisan:

```bash
php artisan make:command SetDefaultPasswords
```

Y en el comando:

```php
<?php

namespace App\Console\Commands;

use App\Models\Borrower_users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetDefaultPasswords extends Command
{
    protected $signature = 'borrowers:set-passwords';
    protected $description = 'Set default passwords for borrowers';

    public function handle()
    {
        Borrower_users::whereNull('password')->update([
            'password' => Hash::make('sena2024')
        ]);

        $this->info('Contraseñas actualizadas exitosamente!');
        $this->info('Contraseña por defecto: sena2024');
    }
}
```

Luego ejecuta:

```bash
php artisan borrowers:set-passwords
```

### 4. Funcionalidades del Portal de Usuario

Los usuarios pueden acceder en: **http://tu-app/borrower/login**

**Características implementadas:**

✅ **Login y Registro** - Los usuarios pueden crear cuentas y autenticarse
✅ **Dashboard Personalizado** - Vista de préstamos activos, sanciones, y reservas
✅ **Historial de Préstamos** - Ver todos los préstamos pasados y presentes
✅ **Gestión de Sanciones** - Ver sanciones activas e históricas
✅ **Renovación de Préstamos** - Los usuarios pueden renovar préstamos cuando falten menos de 2 horas
✅ **Descargar Comprobantes** - Descargar PDF de devoluciones

### 5. Rutas Disponibles

**Rutas públicas:**

-   `GET /borrower/login` - Formulario de login
-   `POST /borrower/login` - Procesar login
-   `GET /borrower/register` - Formulario de registro
-   `POST /borrower/register` - Procesar registro

**Rutas protegidas (requieren autenticación):**

-   `POST /borrower/logout` - Cerrar sesión
-   `GET /borrower/dashboard` - Dashboard principal
-   `GET /borrower/history` - Historial de préstamos
-   `GET /borrower/sanctions` - Ver sanciones
-   `POST /borrower/renew-loan/{service}` - Renovar préstamo
-   `GET /borrower/download-receipt/{service}` - Descargar comprobante PDF

---

## Fotografías al Reportar Daños ✅

### Implementación Optimizada

El sistema de fotografías está implementado **solo cuando hay daños o novedades**, NO en cada devolución normal. Esto hace el proceso mucho más eficiente.

**Flujo correcto:**

1. 📦 **Devolución normal** - Sin foto, proceso rápido
2. ⚠️ **¿Hay daños?** - El bibliotecario crea un reporte
3. 📸 **Reporte con foto** - Se adjunta evidencia del daño

### Ubicación del Campo de Foto

La foto se almacena en la tabla `disabilities` (reportes/novedades), NO en `services`.

**Campo en la BD:**

```sql
disabilities.photo_evidence VARCHAR(255) NULLABLE
```

**Almacenamiento:**

```
storage/app/public/disability_photos/
```

### Cómo Funciona

1. **Cuando NO hay problemas:**

    - Bibliotecario devuelve el equipo normalmente
    - No se requiere foto
    - Proceso rápido y eficiente ✅

2. **Cuando HAY daños/novedades:**
    - El sistema detecta que el usuario que devuelve NO es el mismo que prestó
    - Redirige al formulario de reportes
    - Bibliotecario describe el problema
    - **Opcionalmente** sube una foto como evidencia
    - La foto se guarda en `disabilities.photo_evidence`

### Mostrar las Fotos en la Vista de Reportes

Para ver las fotos en el listado de reportes, actualiza `resources/js/Pages/reports/Index.vue`:

```vue
<div v-if="report.photo_evidence" class="mt-2">
    <img 
        :src="`/storage/${report.photo_evidence}`" 
        alt="Evidencia del daño" 
        class="w-32 h-32 object-cover rounded border border-gray-300 cursor-pointer hover:scale-105 transition"
        @click="openImageModal(report.photo_evidence)"
    />
</div>
```

### En PDFs de Reportes

Si generas PDFs de los reportes, incluye la foto:

```php
@if($disability->photo_evidence)
    <div class="mt-3">
        <strong>Evidencia Fotográfica:</strong><br>
        <img src="{{ public_path('storage/' . $disability->photo_evidence) }}"
             style="max-width: 400px; margin-top: 10px; border: 1px solid #ddd; border-radius: 5px;">
```

---

## Sistema de Notificaciones en Tiempo Real ⏳

### Estado Actual

✅ **Eventos de Broadcasting creados:**

-   `LoanExpiringSoon` - Cuando un préstamo está próximo a vencer
-   `NewReservationCreated` - Cuando se crea una reserva
-   `ReservationApproved` - Cuando se aprueba una reserva

✅ **Eventos disparados en:**

-   `EnviarRecordatoriosDevolucion` command
-   `ReservationController@store` y `ReservationController@approve`

### Pasos Pendientes para Activar

#### 1. Configurar Laravel Reverb (Alternativa a Pusher - Gratis)

Laravel 11 viene con Reverb, un servidor WebSocket gratuito:

```bash
# Instalar Reverb
php artisan reverb:install

# Iniciar el servidor Reverb (en una terminal separada)
php artisan reverb:start
```

#### 2. Actualizar `.env`

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

#### 3. Instalar Laravel Echo en el Frontend

```bash
npm install --save laravel-echo pusher-js
```

#### 4. Configurar Echo en `resources/js/bootstrap.js`

```javascript
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
    enabledTransports: ["ws", "wss"],
});
```

#### 5. Agregar variables a `.env` para Vite

```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

#### 6. Crear Componente de Notificaciones Vue

Crea `resources/js/Components/NotificationToast.vue`:

```vue
<script setup>
import { ref, onMounted } from "vue";

const notifications = ref([]);

const addNotification = (notification) => {
    const id = Date.now();
    notifications.value.push({ id, ...notification });

    setTimeout(() => {
        removeNotification(id);
    }, 5000);
};

const removeNotification = (id) => {
    notifications.value = notifications.value.filter((n) => n.id !== id);
};

onMounted(() => {
    // Escuchar eventos en el canal de bibliotecarios
    window.Echo.channel("bibliotecarios")
        .listen("LoanExpiringSoon", (e) => {
            addNotification({
                type: "warning",
                title: "⏰ Préstamo por vencer",
                message: `El préstamo de ${e.user_name} (${e.equipment_serie}) vence en ${e.hours_remaining} horas`,
            });
        })
        .listen("NewReservationCreated", (e) => {
            addNotification({
                type: "info",
                title: "📋 Nueva reserva",
                message: `${e.user_name} ha creado una reserva para ${e.equipment_serie}`,
            });
        });
});
</script>

<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <div
            v-for="notif in notifications"
            :key="notif.id"
            :class="{
                'bg-yellow-50 border-yellow-500': notif.type === 'warning',
                'bg-blue-50 border-blue-500': notif.type === 'info',
                'bg-green-50 border-green-500': notif.type === 'success',
            }"
            class="max-w-sm border-l-4 p-4 rounded shadow-lg animate-slide-in"
        >
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-bold">{{ notif.title }}</p>
                    <p class="text-sm text-gray-700">{{ notif.message }}</p>
                </div>
                <button
                    @click="removeNotification(notif.id)"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ✕
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>
```

#### 7. Agregar el Componente al Layout

En `AuthenticatedLayout.vue`:

```vue
<script setup>
import NotificationToast from "@/Components/NotificationToast.vue";
</script>

<template>
    <div>
        <NotificationToast />
        <!-- resto del layout -->
    </div>
</template>
```

---

## Comandos Útiles

### Ejecutar el Scheduler (Recordatorios automáticos)

**En desarrollo:**

```bash
php artisan schedule:work
```

**En producción (agregar al crontab):**

```cron
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Enviar Recordatorios Manualmente

```bash
php artisan recordatorios:enviar
```

### Ver Rutas

```bash
php artisan route:list
```

### Limpiar Caché

```bash
php artisan optimize:clear
```

---

## Testing del Portal de Usuario

### 1. Crear un usuario de prueba

```bash
php artisan tinker
```

```php
$user = new App\Models\Borrower_users();
$user->name_user = 'Juan';
$user->lastname_user = 'Pérez';
$user->number_identification = '1234567890';
$user->user_type = 'estudiante';
$user->password = bcrypt('password123');
$user->save();
```

### 2. Probar el Login

1. Ir a `http://localhost/borrower/login`
2. Ingresar:
    - Identificación: `1234567890`
    - Contraseña: `password123`
3. Deberías ver el dashboard del usuario

### 3. Probar Renovación

Para probar la renovación, crea un préstamo que esté próximo a vencer:

```php
$service = App\Models\Services::find(1); // Ajusta el ID
$service->expected_return_date = now()->addHour();
$service->save();
```

Luego intenta renovar desde el dashboard del usuario.

---

## Resumen de Archivos Creados/Modificados

### ✅ Archivos Creados:

**Backend:**

-   `app/Http/Controllers/BorrowerAuth/BorrowerAuthController.php`
-   `app/Http/Controllers/BorrowerAuth/BorrowerDashboardController.php`
-   `app/Http/Middleware/RedirectIfNotBorrower.php`
-   `resources/views/receipts/return-receipt.blade.php`
-   `database/migrations/2026_01_06_000003_add_password_to_borrower_users_table.php`
-   `database/migrations/2026_01_06_000004_add_return_photo_to_services_table.php`

**Frontend:**

-   `resources/js/Layouts/BorrowerLayout.vue`
-   `resources/js/Pages/Borrower/Login.vue`
-   `resources/js/Pages/Borrower/Register.vue`
-   `resources/js/Pages/Borrower/Dashboard.vue`
-   `resources/js/Pages/Borrower/History.vue`
-   `resources/js/Pages/Borrower/Sanctions.vue`

### ✅ Archivos Modificados:

-   `config/auth.php` - Agregado guard 'borrower'
-   `app/Models/Borrower_users.php` - Ahora es Authenticatable
-   `app/Models/Services.php` - Limpieza de campos innecesarios
-   `app/Models/Disability.php` - Agregado campo photo_evidence
-   `app/Http/Controllers/reportsController.php` - Soporte para fotos de evidencia
-   `resources/js/Pages/reports/Create.vue` - Campo de foto con preview
-   `routes/web.php` - Rutas del portal de usuario

---

## Troubleshooting

### Error: "Class Borrower_users not found"

```bash
composer dump-autoload
```

### Error: "SQLSTATE[42S22]: Column not found"

```bash
php artisan migrate:fresh --seed
```

⚠️ **Esto borrará todos los datos. Úsalo solo en desarrollo.**

### Las fotos no se muestran

```bash
php artisan storage:link
chmod -R 755 storage/app/public
```

### Broadcasting no funciona

```bash
# Reinicia el servidor Reverb
php artisan reverb:start

# Reconstruye los assets de Vue
npm run build
```

---

## Próximos Pasos Recomendados

1. ✅ **Implementar notificaciones en tiempo real** (sigue la guía anterior)
2. 📧 **Configurar SMTP** para enviar emails reales (Gmail, SendGrid, Mailgun)
3. 📱 **Notificaciones por WhatsApp** usando Twilio API
4. 🔒 **Agregar autenticación de dos factores (2FA)**
5. 📊 **Reportes avanzados y analíticas**

---

¿Necesitas ayuda con alguna de estas funcionalidades? ¡Estoy aquí para ayudarte! 🚀
