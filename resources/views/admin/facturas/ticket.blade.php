<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket_{{ $factura->numero_factura ?? 'FAC-'.$factura->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 10px;
            width: 78mm; /* Ancho estándar de ticketera */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .linea {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .header-ticket {
            margin-bottom: 12px;
        }
        .header-ticket h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
        }
        .header-ticket p { margin: 2px 0; font-size: 11px; }
        .tabla-items {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-items th {
            font-size: 11px;
            padding-bottom: 5px;
            border-b: 1px solid #000;
        }
        .tabla-items td {
            padding: 4px 0;
            vertical-align: top;
        }
        .totales-container {
            margin-top: 10px;
            width: 100%;
        }
        .totales-container td { padding: 2px 0; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print();" style="padding: 6px 12px; background: #000; color: #fff; border: none; cursor: pointer; font-weight: bold;">
            Imprimir Manualmente 🖨️
        </button>
        <button onclick="window.close();" style="padding: 6px 12px; background: #ccc; color: #000; border: none; cursor: pointer; margin-left: 5px;">
            Cerrar Ventana ❌
        </button>
        <div style="border-bottom: 2px solid #000; margin-top: 10px;"></div>
    </div>

    <div class="header-ticket text-center">
        <h2>TUCOMIDA RESTAURANTE</h2>
        <p>NIT: 901.234.567-1</p>
        <p>Calle Principal #12 - 34, Pereira</p>
        <p>Tel: (606) 321-4567</p>
    </div>

    <div class="linea"></div>

    <div>
        <p><span class="bold">Factura:</span> {{ $factura->numero_factura ?? 'FAC-'.$factura->id }}</p>
        <p><span class="bold">Fecha:</span> {{ $factura->created_at->format('d/m/Y h:i A') }}</p>
        <p><span class="bold">Cliente:</span> {{ $factura->cliente_nombre ?? 'Cliente de Paso' }}</p>
        <p><span class="bold">Método Pago:</span> {{ $factura->metodo_pago }}</p>
    </div>

    <div class="linea"></div>

    <table class="tabla-items">
        <thead>
            <tr>
                <th align="left">Cant | Descripción</th>
                <th align="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->detalles as $detalle)
                <tr>
                    <td>
                        {{ $detalle->cantidad }} x {{ $detalle->producto->name ?? 'Platillo Eliminado' }}
                        <br>
                        <span style="font-size: 10px; color: #555;">&nbsp;&nbsp;P.Unit: ${{ number_format($detalle->precio_unitario, 2) }}</span>
                    </td>
                    <td align="right" class="bold">
                        ${{ number_format($detalle->total_linea, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="linea"></div>

    <table class="totales-container">
        <tr>
            <td>Subtotal:</td>
            <td align="right">${{ number_format($factura->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>IVA (19%):</td>
            <td align="right">${{ number_format($factura->impuesto, 2) }}</td>
        </tr>
        <tr class="bold" style="font-size: 14px;">
            <td>TOTAL APAGAR:</td>
            <td align="right">${{ number_format($factura->total, 2) }}</td>
        </tr>
    </table>

    <div class="linea"></div>

    <div class="text-center" style="margin-top: 15px; font-size: 11px;">
        <p class="bold">¡Gracias por tu visita!</p>
        <p>Propina voluntaria no incluida.</p>
        <p>Desarrollado por TuTienda POS</p>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Lanza el diálogo de impresión de inmediato al cargar la pestaña
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>

</body>
</html>