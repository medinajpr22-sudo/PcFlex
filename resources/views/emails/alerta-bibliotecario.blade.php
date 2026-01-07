<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta para Bibliotecarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 700px;
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
        .summary-box {
            background-color: #fff3cd;
            color: #856404;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary-box h2 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .overdue-badge {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">📋</div>
            <h1>Reporte de Préstamos Vencidos</h1>
            <p>PcFlex - Sistema de Préstamos</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Summary -->
            <div class="summary-box">
                <h2>{{ count($prestamosVencidos) }}</h2>
                <p>Préstamos vencidos pendientes de devolución</p>
            </div>

            <p>A continuación se presenta el listado de préstamos que han excedido su fecha límite:</p>

            <!-- Tabla de préstamos vencidos -->
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Documento</th>
                        <th>Equipo</th>
                        <th>Serie</th>
                        <th>Vencido hace</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestamosVencidos as $prestamo)
                    <tr>
                        <td>{{ $prestamo->users->name }} {{ $prestamo->users->last_name }}</td>
                        <td>{{ $prestamo->users->number_identification }}</td>
                        <td>{{ $prestamo->equipment->type_equi }}</td>
                        <td>{{ $prestamo->equipment->serie_equi }}</td>
                        <td>
                            <span class="overdue-badge">
                                {{ round(\Carbon\Carbon::parse($prestamo->expected_return_date)->diffInHours(now())) }} horas
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <strong>📌 Recomendaciones:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Contactar a los usuarios con préstamos vencidos</li>
                    <li>Verificar el estado de los equipos al momento de la devolución</li>
                    <li>Considerar aplicar sanciones según el reglamento</li>
                    <li>Actualizar el estado de los usuarios si es necesario</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>PcFlex</strong> - Sistema de Gestión de Préstamos</p>
            <p>Este es un correo automático generado para el personal de biblioteca.</p>
            <p>&copy; {{ date('Y') }} PcFlex. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
