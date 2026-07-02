<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Caja;
use App\Models\Factura;
use App\Models\MovimientoCaja;
use LaravelWebauthn\Facades\Webauthn;

class CajaController extends Controller
{
    public function index()
{
    $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
    $totales = null;

    if ($cajaAbierta) {
        $desde = $cajaAbierta->created_at;
        $hasta = now();

        // 1. Obtener de la tabla 'reports' solo los IDs que estén estrictamente 'activo' en este turno
        $reportesDelTurno = DB::table('reports')
            ->whereBetween('created_at', [$desde, $hasta])
            ->where('status', 'activo') // Filtro único y centralizado de estados
            ->get();

        $facturaIds = $reportesDelTurno->pluck('id_factura')->filter()->toArray();
        $movimientoIds = $reportesDelTurno->pluck('movimiento_id')->filter()->toArray();

        // 2. FACTURAS: Sumamos directamente usando la lista de IDs activos
        // (Asegúrate de que la columna se llame 'metodo_pago' en tu tabla 'factura')
        $facturasEfectivo = Factura::whereIn('id', $facturaIds)->where('metodo_pago', 'efectivo')->sum('total');
        $facturasTarjeta  = Factura::whereIn('id', $facturaIds)->where('metodo_pago', 'tarjeta')->sum('total');
        $facturasCredito  = Factura::whereIn('id', $facturaIds)->where('metodo_pago', 'credito')->sum('total');

        // 3. MOVIMIENTOS DE CAJA: Sumamos directamente usando la lista de IDs activos y la columna 'monto'
        $movimientosIngresoEfectivo = MovimientoCaja::whereIn('id', $movimientoIds)->where('tipo', 'ingreso')->sum('monto');
        $movimientosEgresoEfectivo  = MovimientoCaja::whereIn('id', $movimientoIds)->where('tipo', 'egreso')->sum('monto');

        // 4. BALANCE DE ARQUEO TOTALIZADO
        $efectivoTotalCaja = $cajaAbierta->monto_apertura + $facturasEfectivo + $movimientosIngresoEfectivo - $movimientosEgresoEfectivo;
        $totalVentasAcumulado = $facturasEfectivo + $facturasTarjeta + $facturasCredito;

        $totales = [
            'ventas_efectivo' => $facturasEfectivo + $movimientosIngresoEfectivo,
            'ventas_tarjeta'  => $facturasTarjeta,
            'ventas_credito'  => $facturasCredito,
            'gastos'          => $movimientosEgresoEfectivo,
            'efectivo_esperado_final' => $efectivoTotalCaja,
            'total_ventas_acumulado'  => $totalVentasAcumulado
        ];
    }

    return view('caja.index', compact('cajaAbierta', 'totales'));
}

    public function procesarCaja(Request $request)
{
    // Validamos primero los datos comunes y el tipo de autenticación
    $request->validate([
        'accion'    => 'required|in:abrir,cerrar',
        'monto'     => 'required|numeric|min:0',
        'auth_type' => 'required|in:password,biometric'
    ]);

    $authType = $request->input('auth_type');
    $user = auth()->user();

    // --- LOGICA DE AUTENTICACIÓN OPCIONAL ---
    if ($authType === 'password') {
        // Si elige contraseña, esta pasa a ser obligatoria aquí
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'La contraseña de confirmación es incorrecta.'], 401);
        }
    } else {
        // Si elige huella, validamos la aserción enviada por el script de JS
        $request->validate(['webauthn_assertion' => 'required']);
        
        // El facade comprueba los datos del sensor contra las llaves públicas guardadas en tu nueva tabla
        if (!\LaravelWebauthn\Facades\Webauthn::validateAssertion($user, $request->input('webauthn_assertion'))) {
            return response()->json(['success' => false, 'message' => 'Validación biométrica fallida o no autorizada.'], 401);
        }
    }

    // --- PROCESAMIENTO DE TU CAJA (MANTENIDO EXACTAMENTE IGUAL) ---
    $accion = $request->input('accion');
    $monto = $request->input('monto');

    if ($accion === 'abrir') {
        if (Caja::where('estado', 'abierta')->exists()) {
            return response()->json(['success' => false, 'message' => 'Ya hay una caja abierta.'], 400);
        }

        Caja::create([
            'user_id'        => $user->id,
            'monto_apertura' => $monto,
            'estado'         => 'abierta'
        ]);

        return response()->json(['success' => true, 'message' => 'Caja abierta exitosamente.']);
    } 
    
    if ($accion === 'cerrar') {
        $caja = Caja::where('estado', 'abierta')->latest()->first();
        if (!$caja) {
            return response()->json(['success' => false, 'message' => 'No hay caja abierta.'], 400);
        }

        $caja->update([
            'monto_cierre' => $monto,
            'estado'       => 'cerrada',
            'fecha_cierre' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Caja cerrada y arqueada exitosamente.']);
    }

    return response()->json(['success' => false, 'message' => 'Acción inválida.'], 400);
}
public function validarContrasena(Request $request)
{
    $request->validate([
        'password' => 'required|string'
    ]);

    if (!Hash::check($request->password, auth()->user()->password)) {
        return response()->json([
            'success' => false, 
            'message' => 'La contraseña ingresada no es válida para autorizar el registro.'
        ], 401);
    }

    return response()->json([
        'success' => true, 
        'message' => 'Contraseña confirmada.'
    ]);
}
}