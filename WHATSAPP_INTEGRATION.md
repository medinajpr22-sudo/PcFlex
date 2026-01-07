# 📱 Implementación de WhatsApp con Twilio

## 🎯 Guía Paso a Paso

### 1️⃣ Crear Cuenta en Twilio

1. Ir a https://www.twilio.com/try-twilio
2. Registrarse (te dan $15 gratis de crédito)
3. Verificar tu número de teléfono
4. Ir al Dashboard

### 2️⃣ Configurar WhatsApp Sandbox

1. En el dashboard de Twilio:
    - Ir a "Messaging" → "Try it out" → "Send a WhatsApp message"
    - Verás instrucciones para activar tu sandbox
2. Desde tu WhatsApp, enviar el código a +1 415 523 8886
    - Ejemplo: `join <tu-codigo-unico>`
3. Copiar tus credenciales:
    - Account SID
    - Auth Token

### 3️⃣ Instalar Dependencias

```bash
composer require twilio/sdk
```

### 4️⃣ Crear Servicio de WhatsApp

Crear archivo `app/Services/WhatsAppService.php`:

```php
<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.whatsapp_from');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Enviar mensaje de WhatsApp
     */
    public function sendMessage($to, $message)
    {
        if (!$this->client) {
            Log::error('WhatsApp: Credenciales de Twilio no configuradas');
            return false;
        }

        try {
            // Asegurar formato internacional del número
            $to = $this->formatPhoneNumber($to);

            $this->client->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:$this->from",
                    'body' => $message
                ]
            );

            Log::info("WhatsApp enviado a: $to");
            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notificar préstamo de equipo
     */
    public function notifyLoan($service)
    {
        $user = $service->users;
        $equipment = $service->equipment;

        $message = "🔔 *Préstamo Confirmado - PcFlex*\n\n";
        $message .= "Hola {$user->name},\n\n";
        $message .= "✅ Se ha registrado el préstamo de:\n";
        $message .= "📱 *Equipo:* {$equipment->type_equi}\n";
        $message .= "🔢 *Serie:* {$equipment->serie_equi}\n";
        $message .= "📅 *Fecha préstamo:* {$service->date_ser}\n";
        $message .= "📆 *Fecha devolución:* {$service->return_ser}\n\n";
        $message .= "Por favor, cuida el equipo y devuélvelo en la fecha indicada.\n\n";
        $message .= "Gracias,\n_Sistema PcFlex_";

        return $this->sendMessage($user->contacts->telephone_con, $message);
    }

    /**
     * Notificar devolución de equipo
     */
    public function notifyReturn($service)
    {
        $user = $service->users;
        $equipment = $service->equipment;

        $message = "✅ *Devolución Confirmada - PcFlex*\n\n";
        $message .= "Hola {$user->name},\n\n";
        $message .= "Se ha registrado la devolución de:\n";
        $message .= "📱 *Equipo:* {$equipment->type_equi}\n";
        $message .= "🔢 *Serie:* {$equipment->serie_equi}\n";
        $message .= "📅 *Fecha devolución:* " . now()->format('Y-m-d') . "\n\n";
        $message .= "¡Gracias por cuidar el equipo!\n\n";
        $message .= "Saludos,\n_Sistema PcFlex_";

        return $this->sendMessage($user->contacts->telephone_con, $message);
    }

    /**
     * Recordatorio de devolución próxima
     */
    public function sendReturnReminder($service, $daysRemaining)
    {
        $user = $service->users;
        $equipment = $service->equipment;

        $message = "⏰ *Recordatorio de Devolución - PcFlex*\n\n";
        $message .= "Hola {$user->name},\n\n";
        $message .= "Te recordamos que el préstamo del equipo:\n";
        $message .= "📱 *{$equipment->type_equi}* (Serie: {$equipment->serie_equi})\n\n";
        $message .= "Debe ser devuelto en *{$daysRemaining} día(s)*\n";
        $message .= "📆 *Fecha límite:* {$service->return_ser}\n\n";
        $message .= "Por favor, planifica tu devolución.\n\n";
        $message .= "Gracias,\n_Sistema PcFlex_";

        return $this->sendMessage($user->contacts->telephone_con, $message);
    }

    /**
     * Alerta de devolución vencida
     */
    public function sendOverdueAlert($service, $daysOverdue)
    {
        $user = $service->users;
        $equipment = $service->equipment;

        $message = "🚨 *ALERTA: Préstamo Vencido - PcFlex*\n\n";
        $message .= "Hola {$user->name},\n\n";
        $message .= "⚠️ El préstamo del equipo está *VENCIDO*:\n";
        $message .= "📱 *{$equipment->type_equi}* (Serie: {$equipment->serie_equi})\n\n";
        $message .= "📆 Debió devolverse el: {$service->return_ser}\n";
        $message .= "⏳ Días de retraso: *{$daysOverdue}*\n\n";
        $message .= "Por favor, devuelve el equipo lo antes posible para evitar sanciones.\n\n";
        $message .= "Contacta a la biblioteca si hay algún problema.\n\n";
        $message .= "_Sistema PcFlex_";

        return $this->sendMessage($user->contacts->telephone_con, $message);
    }

    /**
     * Formatear número de teléfono a formato internacional
     */
    protected function formatPhoneNumber($phone)
    {
        // Eliminar espacios, guiones y paréntesis
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Si no tiene código de país, agregar +57 (Colombia)
        if (!str_starts_with($phone, '+')) {
            $phone = '+57' . $phone;
        }

        return $phone;
    }
}
```

### 5️⃣ Agregar Configuración

Editar `config/services.php`, agregar al final del array:

```php
'twilio' => [
    'sid' => env('TWILIO_SID'),
    'token' => env('TWILIO_AUTH_TOKEN'),
    'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', '+14155238886'),
],
```

### 6️⃣ Configurar Variables de Entorno

En tu archivo `.env`:

```env
TWILIO_SID=tu_account_sid_aqui
TWILIO_AUTH_TOKEN=tu_auth_token_aqui
TWILIO_WHATSAPP_FROM=+14155238886
```

### 7️⃣ Integrar en Controladores

**Ejemplo 1: Al crear un préstamo** (`FormController.php`):

```php
use App\Services\WhatsAppService;

public function store(Request $request)
{
    // ... código existente para crear el servicio ...

    $service = Service::create([...]);

    // Enviar notificación por WhatsApp
    try {
        $whatsapp = new WhatsAppService();
        $whatsapp->notifyLoan($service);
    } catch (\Exception $e) {
        \Log::error('Error enviando WhatsApp: ' . $e->getMessage());
        // No fallar si WhatsApp no funciona
    }

    return redirect()->route('services.index')
        ->with('success', 'Préstamo creado. Notificación enviada por WhatsApp.');
}
```

**Ejemplo 2: Al hacer devolución** (`DevolucionController.php`):

```php
use App\Services\WhatsAppService;

public function devolver(Request $request)
{
    // ... código existente ...

    $service->update(['status' => 'devuelto', ...]);

    // Enviar confirmación por WhatsApp
    try {
        $whatsapp = new WhatsAppService();
        $whatsapp->notifyReturn($service);
    } catch (\Exception $e) {
        \Log::error('Error enviando WhatsApp: ' . $e->getMessage());
    }

    return redirect()->back()
        ->with('success', 'Devolución registrada. Confirmación enviada por WhatsApp.');
}
```

**Ejemplo 3: Recordatorios automáticos** (`EnviarRecordatoriosDevolucion.php`):

```php
use App\Services\WhatsAppService;

public function handle()
{
    $whatsapp = new WhatsAppService();

    // Recordatorios 1 día antes
    $serviciosManana = Service::where('status', 'pendiente')
        ->whereDate('return_ser', now()->addDay())
        ->get();

    foreach ($serviciosManana as $servicio) {
        try {
            // Enviar email (código existente)
            Mail::to($servicio->users->contacts->email_con)
                ->send(new RecordatorioDevolucion($servicio));

            // NUEVO: Enviar WhatsApp
            $whatsapp->sendReturnReminder($servicio, 1);

        } catch (\Exception $e) {
            \Log::error("Error en recordatorio: " . $e->getMessage());
        }
    }

    // Alertas de vencidos
    $serviciosVencidos = Service::where('status', 'pendiente')
        ->whereDate('return_ser', '<', now())
        ->get();

    foreach ($serviciosVencidos as $servicio) {
        $diasVencido = now()->diffInDays($servicio->return_ser);

        try {
            $whatsapp->sendOverdueAlert($servicio, $diasVencido);
        } catch (\Exception $e) {
            \Log::error("Error en alerta vencido: " . $e->getMessage());
        }
    }
}
```

### 8️⃣ Probar WhatsApp

```bash
php artisan tinker

# Crear instancia del servicio
$whatsapp = new App\Services\WhatsAppService();

# Enviar mensaje de prueba (usar tu número de WhatsApp)
$whatsapp->sendMessage('+573001234567', 'Hola! Este es un mensaje de prueba desde PcFlex 🚀');
```

### 9️⃣ Migrar de Sandbox a Producción

**IMPORTANTE**: El Twilio Sandbox es solo para pruebas. Para producción:

1. **Opción 1: Número Dedicado de Twilio** ($$$)

    - Comprar un número de Twilio habilitado para WhatsApp
    - Costo: ~$1.50/mes + $0.005 por mensaje
    - Ir a Twilio Console → Phone Numbers → Buy a Number
    - Habilitar WhatsApp en ese número

2. **Opción 2: WhatsApp Business API** (Gratis pero complejo)

    - Requiere aprobación de Facebook
    - Requiere negocio verificado
    - Solo para empresas

3. **Opción 3: Proveedores Alternativos**
    - **360Dialog**: https://www.360dialog.com
    - **MessageBird**: https://messagebird.com
    - Algunos ofrecen planes más económicos

---

## 📊 Formato de Números de Teléfono

**Colombia:**

-   Formato correcto: `+573001234567`
-   Sin código de país: `3001234567` (el servicio agrega +57)

**Otros países:**

-   Ajustar el método `formatPhoneNumber()` según sea necesario

---

## 🐛 Solución de Problemas

### Error: "The 'To' number is not a valid WhatsApp number"

-   **Causa**: El número no está registrado en el sandbox
-   **Solución**: Enviar `join <código>` desde ese WhatsApp al +1 415 523 8886

### Error: "Authentication Error"

-   **Causa**: SID o Token incorrectos
-   **Solución**: Verificar credenciales en .env

### Mensajes no llegan

-   Verificar logs: `storage/logs/laravel.log`
-   Verificar saldo de Twilio
-   Verificar que el número está en formato internacional

### Testing en Producción

```bash
# Ver logs de Twilio
# Ir a: https://console.twilio.com/monitor/logs/debugger
```

---

## 💰 Costos Aproximados

**Twilio WhatsApp (después del crédito gratis):**

-   Mensajes enviados: $0.005 USD por mensaje
-   Mensajes recibidos: $0.005 USD por mensaje
-   1000 mensajes ≈ $5 USD

**Alternativas más económicas:**

-   Baileys (gratis pero requiere mantener conexión activa)
-   WhatsApp Business API directamente (gratis pero complejo)

---

## 🎓 Recursos

-   **Twilio Docs**: https://www.twilio.com/docs/whatsapp
-   **Sandbox Tutorial**: https://www.twilio.com/docs/whatsapp/sandbox
-   **API Reference**: https://www.twilio.com/docs/sms/api
-   **PHP SDK**: https://github.com/twilio/twilio-php

---

**Nota**: Para producción, considera usar colas de Laravel para enviar WhatsApp de forma asíncrona:

```php
use Illuminate\Support\Facades\Queue;

Queue::push(function() use ($service) {
    $whatsapp = new WhatsAppService();
    $whatsapp->notifyLoan($service);
});
```

Esto evita que el usuario espere mientras se envía el mensaje.
