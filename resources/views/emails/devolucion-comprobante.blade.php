<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Devolución</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin-bottom: 20px;
        }
        .success-message h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
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
        .footer p {
            margin: 5px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">✅</div>
            <h1>¡Devolución Exitosa!</h1>
            <p>PcFlex - Sistema de Préstamos</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Success Message -->
            <div class="success-message">
                <h2>¡Felicitaciones por la devolución!</h2>
                <p>Tu equipo ha sido devuelto exitosamente. Aquí está tu comprobante de devolución.</p>
            </div>

            <!-- Detalles del Usuario -->
            <div class="details-section">
                <h3>📋 Información del Usuario</h3>
                <div class="detail-row">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value">{{ $usuario->name }} {{ $usuario->last_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Documento:</span>
                    <span class="detail-value">{{ $usuario->type_identification }}: {{ $usuario->number_identification }}</span>
                </div>
                @if($usuario->contacts && $usuario->contacts->email_con)
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $usuario->contacts->email_con }}</span>
                </div>
                @endif
                @if($usuario->contacts && $usuario->contacts->telephone_con)
                <div class="detail-row">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value">{{ $usuario->contacts->telephone_con }}</span>
                </div>
                @endif
            </div>

            <!-- Detalles del Equipo -->
            <div class="details-section">
                <h3>💻 Información del Equipo</h3>
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

            <!-- Detalles del Servicio -->
            <div class="details-section">
                <h3>📅 Detalles del Préstamo</h3>
                <div class="detail-row">
                    <span class="detail-label">Fecha de Préstamo:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($service->date_ser)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha de Devolución:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($service->return_ser)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estado:</span>
                    <span class="detail-value" style="color: #28a745; font-weight: bold;">{{ ucfirst($service->status) }}</span>
                </div>
                @if($service->environment)
                <div class="detail-row">
                    <span class="detail-label">Ambiente:</span>
                    <span class="detail-value">{{ $service->environment->names }}</span>
                </div>
                @endif
            </div>

            <!-- Call to Action -->
            <div style="text-align: center;">
                <p>Gracias por utilizar nuestro sistema de préstamos de equipos.</p>
                <p style="color: #6c757d; font-size: 14px;">Este es un comprobante automático de tu devolución.</p>
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
