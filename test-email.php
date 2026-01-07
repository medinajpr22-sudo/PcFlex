<?php

use Illuminate\Support\Facades\Mail;

echo "\n=== PROBANDO EMAIL ===\n\n";
echo "Enviando email de prueba...\n";

try {
    Mail::raw('¡Este es un email de prueba desde PcFlex! Si ves esto en Mailtrap, la configuracion funciona correctamente.', function($message) {
        $message->to('test@example.com')
                ->subject('Prueba Email PcFlex');
    });
    
    echo "\n✅ EMAIL ENVIADO EXITOSAMENTE!\n\n";
    echo "Ahora ve a: https://mailtrap.io\n";
    echo "1. Haz clic en 'Email Testing'\n";
    echo "2. Selecciona 'My Inbox'\n";
    echo "3. Deberias ver el email que acabamos de enviar\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
}
