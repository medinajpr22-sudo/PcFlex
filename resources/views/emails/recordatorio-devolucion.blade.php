<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Devolución</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
        }
        .warning-message h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .time-remaining {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .time-remaining h3 {
            margin: 0;
            font-size: 36px;
        }
        .time-remaining p {
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
            color: #667eea;
            border-bottom: 2px solid #667eea;
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
        .important-note {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">⏰</div>
            <h1>Recordatorio de Devolución</h1>
            <p>PcFlex - Sistema de Préstamos</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Warning Message -->
            <div class="warning-message">
                <h2>¡Atención {{ $usuario->name }}!</h2>
                <p>Se acerca la fecha límite para devolver tu equipo prestado.</p>
            </div>

            <!-- Time Remaining -->
            <div class="time-remaining">
                <h3>{{ $horasRestantes }} horas</h3>
                <p>restantes para devolver el equipo</p>
            </div>

            <!-- Detalles del Equipo -->
            <div class="details-section">
                <h3>💻 Equipo Prestado</h3>
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
                <h3>📅 Información del Préstamo</h3>
                <div class="detail-row">
                    <span class="detail-label">Fecha de Préstamo:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($service->date_ser)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha Límite de Devolución:</span>
                    <span class="detail-value" style="color: #dc3545; font-weight: bold;">{{ \Carbon\Carbon::parse($service->expected_return_date)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duración del Préstamo:</span>
                    <span class="detail-value">{{ $service->loan_duration_hours }} horas</span>
                </div>
            </div>

            <!-- Important Note -->
            <div class="important-note">
                <strong>⚠️ Importante:</strong>
                <p style="margin: 10px 0 0 0;">
                    Por favor, devuelve el equipo antes de la fecha límite para evitar sanciones. 
                    Si no puedes devolverlo a tiempo, contacta a la biblioteca lo antes posible.
                </p>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin-top: 20px;">
                <p>¿Necesitas ayuda? Visita la biblioteca o contáctanos.</p>
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
