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
                ->where('status', 'activo')
                ->get();

            $facturaIds = $reportesDelTurno->pluck('id_factura')->filter()->toArray();
            $movimientoIds = $reportesDelTurno->pluck('movimiento_id')->filter()->toArray();

            // 2. FACTURAS POR MÉTODOS DE PAGO (Variables separadas)
            $facturasEfectivo      = Factura::whereIn('id', $facturaIds)->where('metodo_pago', 'Efectivo')->sum('total');
            $facturasTarjeta       = Factura::whereIn('id', $facturaIds)->where('metodo_pago', 'Tarjeta')->sum('total');
            $facturasPlataforma    = Factura::whereIn('id', $facturaIds)->where('metodo_pago', 'Plataforma')->sum('total');
            $facturasTransferencia = Factura::whereIn('id', $facturaIds)->where('metodo_pago', 'Transferencia')->sum('total');

            // 3. MOVIMIENTOS DE CAJA
            $movimientosIngresoEfectivo = MovimientoCaja::whereIn('id', $movimientoIds)->where('tipo', 'ingreso')->sum('monto');
            $movimientosEgresoEfectivo  = MovimientoCaja::whereIn('id', $movimientoIds)->where('tipo', 'egreso')->sum('monto');

            // 4. CÁLCULOS Y BALANCE DE ARQUEO
            // Dinero físico esperado solo en efectivo (Caja física)
            $efectivoTotalCaja = $cajaAbierta->monto_apertura + $facturasEfectivo + $movimientosIngresoEfectivo - $movimientosEgresoEfectivo;
            
            // Suma total de todas las ventas del turno (Todos los métodos)
            $totalVentasAcumulado = $facturasEfectivo + $facturasTarjeta + $facturasPlataforma + $facturasTransferencia;

            // Total con el que debería cerrar la caja (Apertura + Todas las ventas + Entradas - Salidas)
            $montoCierreCalculado = $cajaAbierta->monto_apertura + $totalVentasAcumulado + $movimientosIngresoEfectivo - $movimientosEgresoEfectivo;

            $totales = [
                'ventas_efectivo'         => $facturasEfectivo + $movimientosIngresoEfectivo,
                'ventas_tarjeta'          => $facturasTarjeta,
                'ventas_plataforma'       => $facturasPlataforma,
                'ventas_transferencia'    => $facturasTransferencia,
                'gastos'                  => $movimientosEgresoEfectivo,
                'efectivo_esperado_final' => $efectivoTotalCaja,
                'total_ventas_acumulado'  => $totalVentasAcumulado,
                'monto_cierre_calculado'  => $montoCierreCalculado
            ];
        }

        return view('caja.index', compact('cajaAbierta', 'totales'));
    }

    public function procesarCaja(Request $request)
    {
        $request->validate([
            'accion'    => 'required|in:abrir,cerrar',
            'monto'     => 'required|numeric|min:0',
            'auth_type' => 'required|in:password,biometric'
        ]);

        $authType = $request->input('auth_type');
        $user = auth()->user();

        if ($authType === 'password') {
            $request->validate(['password' => 'required']);

            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'La contraseña de confirmación es incorrecta.'], 401);
            }
        } else {
            $request->validate(['webauthn_assertion' => 'required']);

            if (!\LaravelWebauthn\Facades\Webauthn::validateAssertion($user, $request->input('webauthn_assertion'))) {
                return response()->json(['success' => false, 'message' => 'Validación biométrica fallida o no autorizada.'], 401);
            }
        }

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

    public function historial(Request $request)
    {
        $cajas = Caja::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('caja.historial', compact('cajas'));
    }

    public function obtenerMovimientos(Caja $caja)
    {
        $desde = $caja->created_at;
        $hasta = $caja->fecha_cierre ?? now();

        $reportesDelTurno = DB::table('reports')
            ->whereBetween('created_at', [$desde, $hasta])
            ->where('status', 'activo')
            ->get();

        $movimientoIds = $reportesDelTurno->pluck('movimiento_id')->filter()->toArray();
        $facturaIds    = $reportesDelTurno->pluck('id_factura')->filter()->toArray();

        $movimientos = MovimientoCaja::whereIn('id', $movimientoIds)->latest()->get();
        $facturas    = Factura::whereIn('id', $facturaIds)->latest()->get();

        return response()->json([
            'success'     => true,
            'caja_id'     => $caja->id,
            'movimientos' => $movimientos,
            'facturas'    => $facturas,
        ]);
    }
}