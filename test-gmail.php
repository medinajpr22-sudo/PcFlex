<?php

use Illuminate\Support\Facades\Mail;

echo "\n=== PROBANDO EMAIL CON GMAIL ===\n\n";
echo "Enviando email REAL a: pablo20.estudio@gmail.com\n";

try {
    Mail::raw('¡Hola! Este es un email de prueba REAL desde PcFlex usando Gmail. Si recibes esto en tu bandeja de entrada, significa que la configuración está funcionando perfectamente! 🎉', function($message) {
        $message->to('pablo20.estudio@gmail.com')
                ->subject('Prueba Email REAL - PcFlex');
    });
    
    echo "\n✅ EMAIL ENVIADO EXITOSAMENTE!\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "📧 REVISA TU GMAIL: pablo20.estudio@gmail.com\n\n";
    echo "Deberías ver un email nuevo con el asunto:\n";
    echo "'Prueba Email REAL - PcFlex'\n\n";
    echo "Si no lo ves en la bandeja principal, revisa:\n";
    echo "  - Spam/Correo no deseado\n";
    echo "  - Promociones\n";
    echo "  - Social\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
}
