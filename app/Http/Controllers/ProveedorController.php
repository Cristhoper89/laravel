<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProveedorController extends Controller
{
    /**
     * Muestra la lista de proveedores con buscador.
     */
    public function index(Request $request)
    {
        $query = Proveedor::query();

        // Filtro de búsqueda por Empresa, Contacto o NIT
        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_name', 'LIKE', "%{$search}%")
                  ->orWhere('nit', 'LIKE', "%{$search}%");
        }

        // Paginación de 10 en 10 ordenados por el más reciente
        $proveedores = $query->latest()->paginate(10);

        return view('admin.proveedores.index', compact('proveedores'));
    }

    /**
     * Muestra el formulario para crear un nuevo proveedor.
     */
    public function create()
    {
        return view('admin.proveedores.create');
    }

    /**
     * Almacena un proveedor recién creado en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos del formulario
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'nit'          => 'required|string|max:45',
            'contact_name' => 'required|string|max:255',
            'phone'        => 'nullable|string|max:45',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
            'image' => 'nullable',
        ]);

        // 2. Procesar y guardar la imagen si fue cargada (Archivo o URL)
        if ($request->hasFile('image')) {
            // Caso A: Es un archivo local. Validamos que sea imagen real antes de guardar
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,webp,gif'
            ]);

            // Guarda en storage/app/public/proveedores y retorna la ruta relativa
            $validated['image'] = $request->file('image')->store('proveedores', 'public');

        } elseif ($request->filled('image')) {
            // Caso B: Es texto. Validamos si tiene la estructura de una URL de internet
            if (filter_var($request->input('image'), FILTER_VALIDATE_URL)) {
                $validated['image'] = $request->input('image');
            } else {
                // Si mandó texto pero no es una URL válida, regresamos con el error
                return back()->withErrors(['image' => 'El enlace proporcionado no es una URL válida.'])->withInput();
            }
        }

        // 3. Crear el registro usando asignación masiva ($fillable)
        Proveedor::create($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar un proveedor existente.
     */
    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualiza los datos del proveedor en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        // 1. Validar datos
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'nit'          => 'required|string|max:45',
            'contact_name' => 'required|string|max:255',
            'phone'        => 'nullable|string|max:45',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 2. Procesar imagen nueva si el usuario la cambia
        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior del disco para no acumular archivos huérfanos
            if ($proveedor->image) {
                Storage::disk('public')->delete($proveedor->image);
            }
            // Guardar la nueva imagen
            $validated['image'] = $request->file('image')->store('proveedores', 'public');
        }

        // 3. Actualizar el registro existente
        $proveedor->update($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado con éxito.');
    }

    /**
     * Elimina un proveedor del sistema.
     */
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);

        // Borrar su imagen del almacenamiento antes de destruir el registro
        if ($proveedor->image) {
            Storage::disk('public')->delete($proveedor->image);
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado del sistema.');
    }
}