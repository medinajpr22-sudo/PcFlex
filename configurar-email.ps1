# Script de Configuración de Email - PcFlex
# Ejecutar: .\configurar-email.ps1

Write-Host "`n╔══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║     CONFIGURADOR DE EMAIL - PCFLEX v1.0             ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

Write-Host "Selecciona tu opción de email:" -ForegroundColor Yellow
Write-Host ""
Write-Host "  [1] Mailtrap (RECOMENDADO - Gratis para testing)" -ForegroundColor Green
Write-Host "      → Perfecto para probar emails sin enviarlos realmente" -ForegroundColor Gray
Write-Host "      → Ver emails en su web https://mailtrap.io" -ForegroundColor Gray
Write-Host ""
Write-Host "  [2] Gmail (Si ya tienes Gmail configurado)" -ForegroundColor Green
Write-Host "      → Envía emails reales a tu Gmail" -ForegroundColor Gray
Write-Host "      → Requiere contraseña de aplicación" -ForegroundColor Gray
Write-Host ""
Write-Host "  [3] Mantener en modo LOG (solo logs, sin emails)" -ForegroundColor Yellow
Write-Host ""

$opcion = Read-Host "Ingresa tu opción (1, 2 o 3)"

switch ($opcion) {
    "1" {
        Write-Host "`n📧 CONFIGURANDO MAILTRAP..." -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Pasos:" -ForegroundColor Yellow
        Write-Host "1. Abre tu navegador y ve a: https://mailtrap.io" -ForegroundColor White
        Write-Host "2. Registrate GRATIS (puedes usar tu cuenta de Google)" -ForegroundColor White
        Write-Host "3. Ve a: Email Testing → Inboxes → My Inbox" -ForegroundColor White
        Write-Host "4. Haz clic en 'Show Credentials'" -ForegroundColor White
        Write-Host "5. Copia tus credenciales y regrésalas aquí`n" -ForegroundColor White
        
        Read-Host "Presiona ENTER cuando hayas creado tu cuenta de Mailtrap"
        
        Write-Host ""
        $username = Read-Host "Ingresa tu Mailtrap Username"
        $password = Read-Host "Ingresa tu Mailtrap Password" -AsSecureString
        $password_plain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($password))
        
        # Leer archivo .env
        $envContent = Get-Content .env
        
        # Actualizar líneas de MAIL
        $envContent = $envContent -replace 'MAIL_MAILER=.*', 'MAIL_MAILER=smtp'
        $envContent = $envContent -replace 'MAIL_HOST=.*', 'MAIL_HOST=sandbox.smtp.mailtrap.io'
        $envContent = $envContent -replace 'MAIL_PORT=.*', 'MAIL_PORT=2525'
        $envContent = $envContent -replace 'MAIL_USERNAME=.*', "MAIL_USERNAME=$username"
        $envContent = $envContent -replace 'MAIL_PASSWORD=.*', "MAIL_PASSWORD=$password_plain"
        $envContent = $envContent -replace 'MAIL_ENCRYPTION=.*', 'MAIL_ENCRYPTION=tls'
        $envContent = $envContent -replace 'MAIL_FROM_ADDRESS=.*', 'MAIL_FROM_ADDRESS="noreply@pcflex.com"'
        $envContent = $envContent -replace 'MAIL_FROM_NAME=.*', 'MAIL_FROM_NAME="PcFlex - Sistema de Préstamos"'
        
        # Guardar .env
        $envContent | Set-Content .env
        
        Write-Host "`n✅ ¡Configuración guardada!" -ForegroundColor Green
        Write-Host ""
        Write-Host "Configuración aplicada:" -ForegroundColor Cyan
        Write-Host "  MAIL_MAILER: smtp" -ForegroundColor White
        Write-Host "  MAIL_HOST: sandbox.smtp.mailtrap.io" -ForegroundColor White
        Write-Host "  MAIL_PORT: 2525" -ForegroundColor White
        Write-Host "  MAIL_USERNAME: $username" -ForegroundColor White
        Write-Host "  MAIL_FROM: noreply@pcflex.com`n" -ForegroundColor White
    }
    
    "2" {
        Write-Host "`n📧 CONFIGURANDO GMAIL..." -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Pasos:" -ForegroundColor Yellow
        Write-Host "1. Abre: https://myaccount.google.com/security" -ForegroundColor White
        Write-Host "2. Activa 'Verificación en 2 pasos' (si no la tienes)" -ForegroundColor White
        Write-Host "3. Ve a 'Contraseñas de aplicaciones'" -ForegroundColor White
        Write-Host "4. Crea una nueva contraseña para 'Correo' en 'Windows'" -ForegroundColor White
        Write-Host "5. Copia la contraseña de 16 caracteres`n" -ForegroundColor White
        
        Read-Host "Presiona ENTER cuando hayas generado tu contraseña de aplicación"
        
        Write-Host ""
        $gmail = Read-Host "Ingresa tu correo de Gmail completo (ejemplo@gmail.com)"
        $appPassword = Read-Host "Ingresa la contraseña de aplicación (16 caracteres sin espacios)" -AsSecureString
        $appPassword_plain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($appPassword))
        
        # Leer archivo .env
        $envContent = Get-Content .env
        
        # Actualizar líneas de MAIL
        $envContent = $envContent -replace 'MAIL_MAILER=.*', 'MAIL_MAILER=smtp'
        $envContent = $envContent -replace 'MAIL_HOST=.*', 'MAIL_HOST=smtp.gmail.com'
        $envContent = $envContent -replace 'MAIL_PORT=.*', 'MAIL_PORT=587'
        $envContent = $envContent -replace 'MAIL_USERNAME=.*', "MAIL_USERNAME=$gmail"
        $envContent = $envContent -replace 'MAIL_PASSWORD=.*', "MAIL_PASSWORD=$appPassword_plain"
        $envContent = $envContent -replace 'MAIL_ENCRYPTION=.*', 'MAIL_ENCRYPTION=tls'
        $envContent = $envContent -replace 'MAIL_FROM_ADDRESS=.*', "MAIL_FROM_ADDRESS=`"$gmail`""
        $envContent = $envContent -replace 'MAIL_FROM_NAME=.*', 'MAIL_FROM_NAME="PcFlex - Sistema de Préstamos"'
        
        # Guardar .env
        $envContent | Set-Content .env
        
        Write-Host "`n✅ ¡Configuración guardada!" -ForegroundColor Green
        Write-Host ""
        Write-Host "Configuración aplicada:" -ForegroundColor Cyan
        Write-Host "  MAIL_MAILER: smtp" -ForegroundColor White
        Write-Host "  MAIL_HOST: smtp.gmail.com" -ForegroundColor White
        Write-Host "  MAIL_PORT: 587" -ForegroundColor White
        Write-Host "  MAIL_USERNAME: $gmail" -ForegroundColor White
        Write-Host "  MAIL_FROM: $gmail`n" -ForegroundColor White
    }
    
    "3" {
        Write-Host "`n📝 Manteniendo configuración en modo LOG" -ForegroundColor Yellow
        Write-Host "Los emails se guardarán en: storage/logs/laravel.log`n" -ForegroundColor Gray
        exit
    }
    
    default {
        Write-Host "`n❌ Opción inválida. Ejecuta el script nuevamente.`n" -ForegroundColor Red
        exit
    }
}

Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host "🔄 Limpiando caché de Laravel..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear

Write-Host ""
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host "✅ ¡CONFIGURACIÓN COMPLETA!" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Cyan

Write-Host "📝 Siguiente paso: PROBAR EMAIL" -ForegroundColor Yellow
Write-Host ""
Write-Host "Ejecuta este comando para probar:" -ForegroundColor White
Write-Host ""
Write-Host "  php artisan tinker" -ForegroundColor Cyan
Write-Host ""
Write-Host "Luego dentro de tinker, ejecuta:" -ForegroundColor White
Write-Host ""
Write-Host '  Mail::raw("Prueba desde PcFlex", function($m) {' -ForegroundColor Green
Write-Host '      $m->to("test@test.com")->subject("Test Email");' -ForegroundColor Green
Write-Host '  });' -ForegroundColor Green
Write-Host ""
Write-Host "Si sale '= null' es porque funcionó!" -ForegroundColor Yellow

if ($opcion -eq "1") {
    Write-Host "`nAhora ve a https://mailtrap.io y verás el email en tu inbox 📧`n" -ForegroundColor Cyan
} elseif ($opcion -eq "2") {
    Write-Host "`nAhora revisa tu Gmail y verás el email 📧`n" -ForegroundColor Cyan
}

Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Cyan
