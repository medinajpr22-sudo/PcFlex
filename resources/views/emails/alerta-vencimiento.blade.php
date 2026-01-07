<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Vencimiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #e53935 0%, #e35d5b 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header .icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .danger-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin-bottom: 20px;
        }
        .danger-message h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .time-overdue {
            background: linear-gradient(135deg, #e53935 0%, #e35d5b 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .time-overdue h3 {
            margin: 0;
            font-size: 36px;
        }
        .time-overdue p {
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .details-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .details-section h3 {
            margin-top: 0;
            color: #e53935;
            border-bottom: 2px solid #e53935;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .urgent-note {
            background-color: #fff3cd;
            color: #856404;
            padding: 20px;
            border-radius: 5px;
            border: 2px solid #ffc107;
            margin-top: 20px;
            text-align: center;
        }
        .urgent-note h3 {
            margin: 0 0 10px 0;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🚨</div>
            <h1>PRÉSTAMO VENCIDO</h1>
            <p>PcFlex - Sistema de Préstamos</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Danger Message -->
            <div class="danger-message">
                <h2>¡URGENTE {{ $usuario->name }}!</h2>
                <p>Tu préstamo ha excedido el tiempo permitido. Debes devolver el equipo INMEDIATAMENTE.</p>
            </div>

            <!-- Time Overdue -->
            <div class="time-overdue">
                <h3>{{ $horasVencidas }} horas</h3>
                <p>de retraso en la devolución</p>
            </div>

            <!-- Detalles del Equipo -->
            <div class="details-section">
                <h3>💻 Equipo que debes devolver</h3>
                <div class="detail-row">
                    <span class="detail-label">Tipo:</span>
                    <span class="detail-value">{{ $equipo->type_equi }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Serie:</span>
                    <span class="detail-value">{{ $equipo->serie_equi }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Características:</span>
                    <span class="detail-value">{{ $equipo->characteristics }}</span>
                </div>
            </div>

            <!-- Detalles del Préstamo -->
            <div class="details-section">
                <h3>📅 Información del Préstamo Vencido</h3>
                <div class="detail-row">
                    <span class="detail-label">Fecha de Préstamo:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($service->date_ser)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Debiste devolver el:</span>
                    <span class="detail-value" style="color: #dc3545; font-weight: bold;">{{ \Carbon\Carbon::parse($service->expected_return_date)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duración permitida:</span>
                    <span class="detail-value">{{ $service->loan_duration_hours }} horas</span>
                </div>
            </div>

            <!-- Urgent Note -->
            <div class="urgent-note">
                <h3>⚠️ ACCIÓN REQUERIDA INMEDIATA</h3>
                <p style="margin: 10px 0;">
                    <strong>Debes devolver el equipo AHORA para evitar:</strong>
                </p>
                <ul style="text-align: left; margin: 10px 0; padding-left: 40px;">
                    <li>Sanciones disciplinarias</li>
                    <li>Bloqueo de tu cuenta</li>
                    <li>Restricción de futuros préstamos</li>
                    <li>Posibles multas económicas</li>
                </ul>
                <p style="margin: 10px 0 0 0;">
                    <strong>Dirígete inmediatamente a la biblioteca para devolver el equipo.</strong>
                </p>
            </div>

            <!-- Contact Info -->
            <div style="text-align: center; margin-top: 20px;">
                <p style="color: #dc3545; font-weight: bold;">
                    Si ya devolviste el equipo, por favor ignora este mensaje.
                </p>
                <p>Para cualquier aclaración, contacta a la biblioteca de inmediato.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>PcFlex</strong> - Sistema de Gestión de Préstamos</p>
            <p>Este es un correo automático, por favor no responder.</p>
            <p>&copy; {{ date('Y') }} PcFlex. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
