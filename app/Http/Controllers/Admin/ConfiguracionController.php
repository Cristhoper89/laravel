<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $empresa = DB::table('company')->where('id', 1)->first();
        $estilos = DB::table('estilos')->get();

        return view('admin.configuracion.index', compact('empresa', 'estilos'));
    }

    public function updateEmpresa(Request $request)
    {
        // Validamos el campo IVA (admite enteros y decimales como 19 o 19.50)
        $request->validate([
            'name'      => 'required|string|max:255',
            'NIT'       => 'required|string|max:50',
            'address'   => 'required|string|max:255',
            'contact'   => 'required|string|max:50',
            'email'     => 'required|email|max:255',
            'city'      => 'required|string|max:100',
            'iva'       => 'required|numeric|min:0|max:100', // Acepa floats/números
            'logo_url'  => 'nullable|string|max:2048',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $empresaActual = DB::table('company')->where('id', 1)->first();
        $logoFinal = $empresaActual->logo ?? null;

        $eraLocal = $empresaActual && $empresaActual->logo && !Str::startsWith($empresaActual->logo, ['http://', 'https://']);

        if ($request->hasFile('logo_file')) {
            if ($eraLocal) {
                Storage::disk('public')->delete($empresaActual->logo);
            }
            $logoFinal = $request->file('logo_file')->store('logos', 'public');
        } 
        elseif ($request->filled('logo_url')) {
            if ($eraLocal) {
                Storage::disk('public')->delete($empresaActual->logo);
            }
            $logoFinal = $request->logo_url;
        }

        DB::table('company')->where('id', 1)->update([
            'name'       => $request->name,
            'NIT'        => $request->NIT,
            'address'    => $request->address,
            'contact'    => $request->contact,
            'email'      => $request->email,
            'city'       => $request->city,
            'IVA'        => (float) $request->iva, // 👈 Se guardan los datos casteados explícitamente a float
            'logo'       => $logoFinal,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }

    public function updateEstilo(Request $request)
    {
        $request->validate([
            'estilo_id' => 'required|integer|exists:estilos,id'
        ]);

        DB::table('estilos')->update(['estado' => 0]);
        DB::table('estilos')->where('id', $request->estilo_id)->update(['estado' => 1]);

        return redirect()->back()->with('success', 'Tema visual actualizado con éxito. 🎨');
    }
}