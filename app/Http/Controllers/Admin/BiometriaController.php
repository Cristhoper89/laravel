<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiometriaController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validamos que el template de la huella sea un string obligatorio
        $request->validate([
            'template' => 'required|string'
        ]);

        try {
            $user = Auth::user(); // Obtenemos el usuario autenticado actualmente

            // 2. Guardamos en la tabla 'user_biometrics' que creamos anteriormente
            DB::table('user_biometrics')->updateOrInsert(
                ['user_id' => $user->id], // Si ya tenía huella, la sobrescribe (actualiza)
                [
                    'fingerprint_template' => $request->input('template'),
                    'finger_index' => 1, // Por defecto pulgar derecho, puedes volverlo dinámico
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // 3. Respondemos en formato JSON para que el JavaScript del Frontend sepa que todo salió bien
            return response()->json([
                'success' => true,
                'message' => 'Huella registrada correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}