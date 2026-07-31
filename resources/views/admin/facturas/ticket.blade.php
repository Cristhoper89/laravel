<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket_{{ $factura->numero_factura ?? 'FAC-' . $factura->id }}</title>
    <style>
        /* Configuración de tamaño para impresoras térmicas de 80mm */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background-color: #f4f4f4;
            /* Fondo gris suave para resaltar el ticket en pantalla */
            margin: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Centra horizontalmente en pantalla */
        }

        /* Contenedor del ticket en pantalla */
        .ticket {
            width: 78mm;
            max-width: 78mm;
            padding: 12px;
            background-color: #fff;
            /* Fondo blanco tipo papel */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            /* Sombras para simular el ticket */
            margin: 0 auto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .linea {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        /* Logo optimizado */
        .ticket-logo {
            max-width: 65px;
            max-height: 65px;
            object-fit: contain;
            margin-bottom: 4px;
            filter: grayscale(100%);
        }

        .header-ticket h2 {
            margin: 0 0 2px 0;
            font-size: 15px;
            text-transform: uppercase;
        }

        .header-ticket p {
            margin: 2px 0;
            font-size: 10px;
        }

        /* Tabla de información básica */
        .info-factura-tabla {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
        }

        .info-factura-tabla td {
            padding: 1px 0;
        }

        /* Tabla de productos */
        .tabla-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .tabla-items th {
            font-size: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }

        .tabla-items td {
            padding: 4px 0;
            vertical-align: top;
        }

        /* Totales */
        .totales-container {
            margin-top: 6px;
            width: 100%;
            font-size: 11px;
        }

        .totales-container td {
            padding: 2px 0;
        }

        /* Reglas exclusivas para cuando el navegador imprime */
        /* Configuración al presionar Imprimir o ver vista previa */
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            html,
            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .no-print {
                display: none !important;
            }

            .ticket {
                width: 100% !important;
                max-width: 78mm !important;
                padding: 0 !important;
                box-shadow: none !important;
                margin: 0 auto !important;
            }
        }
    </style>
</head>

<body>

    <!-- BOTONES DE ACCIÓN (SOLO PANTALLA) -->
    <div class="no-print" style="margin-bottom: 15px; text-align: center; width: 100%;">
        <button onclick="window.print();"
            style="padding: 7px 14px; background: #000; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 11px;">
            Imprimir Manualmente 🖨️
        </button>
        <button onclick="window.close();"
            style="padding: 7px 14px; background: #e0e0e0; color: #000; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px; font-size: 11px;">
            Cerrar Ventana ❌
        </button>
        <div style="border-bottom: 2px dashed #ccc; margin-top: 10px;"></div>
    </div>

    <!-- ESTRUCTURA DEL TICKET POS -->
    <div class="ticket">

        <!-- CABECERA CON LOGO Y EMPRESA -->
        <div class="header-ticket text-center">
            @if (!empty($empresaGlobal->logo))
                <img src="{{ asset('storage/' . $empresaGlobal->logo) }}" alt="Logo" class="ticket-logo">
            @elseif(!empty($empresaGlobal->image))
                <img src="{{ $empresaGlobal->image }}" alt="Logo" class="ticket-logo">
            @endif

            <h2>{{ $empresaGlobal->name ?? 'TUTIENDA' }}</h2>
            <p><span class="bold">NIT:</span> {{ $empresaGlobal->NIT ?? ($empresaGlobal->nit ?? 'N/A') }}</p>
            <p>{{ $empresaGlobal->address ?? 'Dirección no disponible' }}</p>
            <p><span class="bold">Tel:</span> {{ $empresaGlobal->contact ?? ($empresaGlobal->phone ?? 'N/A') }}</p>
        </div>

        <div class="linea"></div>

        <!-- DATOS DE LA VENTA -->
        <table class="info-factura-tabla">
            <tr>
                <td>FACTURA:</td>
                <td align="right" class="bold">{{ $factura->numero_factura ?? 'FAC-' . $factura->id }}</td>
            </tr>
            <tr>
                <td>FECHA:</td>
                <td align="right">{{ $factura->created_at->format('d/m/Y h:i A') }}</td>
            </tr>
            <tr>
                <td>CLIENTE:</td>
                <td align="right" class="bold">{{ $factura->cliente_nombre ?? 'Cliente de Paso' }}</td>
            </tr>
            <tr>
                <td>PAGO:</td>
                <td align="right" class="bold" style="text-transform: uppercase;">{{ $factura->metodo_pago }}</td>
            </tr>
        </table>

        <div class="linea"></div>

        <!-- DETALLE DE PRODUCTOS -->
        <table class="tabla-items">
            <thead>
                <tr>
                    <th align="left">CANT | DESCRIPCIÓN</th>
                    <th align="right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($factura->detalles as $detalle)
                    <tr>
                        <td>
                            <span class="bold">{{ $detalle->cantidad }}x</span>
                            {{ $detalle->producto->name ?? 'Platillo Eliminado' }}
                            <br>
                            <span style="font-size: 9px; color: #444;">&nbsp;&nbsp;P.Unit:
                                ${{ number_format($detalle->precio_unitario, 2) }}</span>
                        </td>
                        <td align="right" class="bold">
                            ${{ number_format($detalle->total_linea, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="linea"></div>

        <!-- TOTALES -->
        <table class="totales-container">
            <tr>
                <td>Subtotal:</td>
                <td align="right">${{ number_format($factura->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>IVA:</td>
                <td align="right">${{ number_format($factura->impuesto, 2) }}</td>
            </tr>
            <tr class="bold" style="font-size: 13px;">
                <td style="padding-top: 4px;">TOTAL A PAGAR:</td>
                <td align="right" style="padding-top: 4px;">${{ number_format($factura->total, 2) }}</td>
            </tr>
        </table>

        <div class="linea"></div>

        <!-- PIE DE PÁGINA -->
        <div class="text-center" style="margin-top: 10px; font-size: 10px;">
            <p class="bold" style="margin: 2px 0;">¡GRACIAS POR TU COMPRA!</p>
            <p style="margin: 2px 0;">Propina voluntaria no incluida.</p>
            <p style="margin: 6px 0 0 0; font-size: 9px; color: #555;">Software POS - TuTienda</p>
        </div>

    </div>

    <!-- AUTO IMPRESIÓN AL CARGAR -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>

</body>

</html>
