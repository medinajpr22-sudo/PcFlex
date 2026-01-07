<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Devolución</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #4CAF50;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h2 {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 18px;
            border-left: 4px solid #4CAF50;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            padding: 8px;
            background-color: #f9f9f9;
        }
        .info-value {
            display: table-cell;
            width: 60%;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .success-badge {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 20px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .signature {
            margin-top: 60px;
            text-align: center;
        }
        .signature-line {
            border-top: 2px solid #333;
            width: 300px;
            margin: 0 auto 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ COMPROBANTE DE DEVOLUCIÓN</h1>
        <p>Sistema de Gestión de Equipos</p>
        <p>Fecha de emisión: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="success-badge">
        ¡DEVOLUCIÓN EXITOSA!
    </div>

    <div class="info-section">
        <h2>📋 Información del Préstamo</h2>
        <div class="info-row">
            <div class="info-label">ID de Servicio:</div>
            <div class="info-value">#{{ $service->id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha de Préstamo:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($service->date_ser)->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha de Devolución:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($service->return_date)->format('d/m/Y H:i') }}</div>
        </div>
        @if($service->expected_return_date)
        <div class="info-row">
            <div class="info-label">Fecha Límite:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($service->expected_return_date)->format('d/m/Y H:i') }}</div>
        </div>
        @endif
    </div>

    <div class="info-section">
        <h2>👤 Información del Usuario</h2>
        <div class="info-row">
            <div class="info-label">Nombre:</div>
            <div class="info-value">{{ $usuario->name_user }} {{ $usuario->lastname_user }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Identificación:</div>
            <div class="info-value">{{ $usuario->number_identification }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tipo de Usuario:</div>
            <div class="info-value">{{ ucfirst($usuario->user_type) }}</div>
        </div>
    </div>

    <div class="info-section">
        <h2>💻 Información del Equipo</h2>
        <div class="info-row">
            <div class="info-label">Serie:</div>
            <div class="info-value">{{ $service->equipment->serie_equ }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Marca:</div>
            <div class="info-value">{{ $service->equipment->brand_equ }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Procesador:</div>
            <div class="info-value">{{ $service->equipment->processor_equ }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ambiente:</div>
            <div class="info-value">{{ $service->environment->name_env }}</div>
        </div>
    </div>

    @if($service->observation_ser)
    <div class="info-section">
        <h2>📝 Observaciones</h2>
        <div style="padding: 10px; background-color: #f9f9f9; border-left: 4px solid #FF9800;">
            {{ $service->observation_ser }}
        </div>
    </div>
    @endif

    <div class="signature">
        <div class="signature-line"></div>
        <p><strong>Firma del Usuario</strong></p>
        <p>{{ $usuario->name_user }} {{ $usuario->lastname_user }}</p>
        <p>{{ $usuario->number_identification }}</p>
    </div>

    <div class="footer">
        <p>Este documento certifica la devolución del equipo en las condiciones indicadas.</p>
        <p>Generado automáticamente por el Sistema de Gestión de Equipos</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
