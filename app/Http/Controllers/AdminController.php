<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\User;
use App\Models\Category;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // =========================================================================
    // VISTA PRINCIPAL (DASHBOARD)
    // =========================================================================

    /**
     * Muestra la tabla principal con todo el menú de comida.
     */
        public function index(Request $request)
    {
        $tab = $request->input('tab', 'activos'); // Filtro por defecto: 'activos'

        // Métricas rápidas para las tarjetas (KPIs)
        $totalProductos = Producto::count();
        $totalActivos = Producto::where('state', true)->orWhereNull('state')->count();
        $totalInactivos = Producto::where('state', false)->count();
        $bajoStock = Producto::where('state', true)->where('stock', '<=', 10)->count();

        // Consulta filtrada según la pestaña seleccionada
        $query = Producto::query();

        if ($tab === 'inactivos') {
            $query->where('state', false);
        } else {
            // Considera 'true' o valores nulos por compatibilidad con registros previos
            $query->where(function($q) {
                $q->where('state', true)->orWhereNull('state');
            });
        }

        $productos = $query->orderBy('name', 'asc')->get();

        return view('admin.dashboard', compact(
            'productos',
            'tab',
            'totalProductos',
            'totalActivos',
            'totalInactivos',
            'bajoStock'
        ));
    }

    public function toggleEstado($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
        return back()->with('error', 'No está permitido cambiar el estado de un administrador.');
        }
        $user->estado = !$user->estado;
        $user->save();

        $mensaje = $user->estado ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.';
        return redirect()->back()->with('success', $mensaje);
    }
    // =========================================================================
    // CRUD DE USUARIOS (ADMIN / CLIENTE / CAJERO / CAJERO2)
    // =========================================================================

    /**
     * Muestra la pantalla de GESTIÓN DE USUARIOS (El CRUD con la tabla)
     */
    public function usuarios(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.crud.dashboard', compact('users'));
    }

    /**
     * Muestra el formulario para registrar un nuevo usuario.
     */
    public function create()
    {
        return view('admin.crud.create');
    }

    /**
     * Guardar nuevo usuario con Foto (Soporta admin, cliente, cajero y cajero2).
     */
    public function store(Request $request)
    {
        // 1. Validación inicial actualizada con los nuevos roles
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'cliente', 'cajero', 'cajero2'])],
            'photo' => 'nullable',
        ]);

        $photoPath = null;

        // 2. PROCESAMIENTO HÍBRIDO SEGURO DEL CAMPO 'photo'
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $path = $request->file('photo')->store('photos', 'public');
            $photoPath = '/storage/' . $path;

        } elseif ($request->filled('photo')) {
            $urlCandidata = trim($request->photo);

            if (filter_var($urlCandidata, FILTER_VALIDATE_URL)) {
                $photoPath = $urlCandidata;
            } else {
                return back()
                    ->withErrors(['photo' => 'El texto ingresado no es un enlace URL web válido. Por favor, introduce una dirección directa de internet (ej. https://...) o sube un archivo local.'])
                    ->withInput();
            }
        }

        // 3. Crear el registro usando asignación masiva segura
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'photo' => $photoPath,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.crud.edit', compact('user'));
    }

    /**
     * Actualizar usuario y procesar cambio de Foto.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'cliente', 'cajero', 'cajero2'])],
            'password' => 'nullable|string|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('name', 'email', 'role');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('photos', 'public');
            $data['photo'] = '/storage/' . $path;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario junto con su Foto.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($user->photo) {
            $oldPath = str_replace('/storage/', '', $user->photo);
            Storage::disk('public')->delete($oldPath);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    // =========================================================================
    // CRUD DE PRODUCTOS (COMIDA)
    // =========================================================================
    
    /**
     * Muestra el formulario para agregar un producto nuevo.
     */
    public function createProducto()
    {
        $proveedores = Proveedor::orderBy('company_name', 'asc')->get();
        $categorias = Category::where('type', 'activo')->orderBy('name', 'asc')->get();

        return view('admin.productos.create', compact('proveedores', 'categorias'));
    }

    /**
     * Guardar el nuevo producto en la base de datos.
     */
    public function storeProducto(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:proveedors,id',
            'category_id' => 'required|exists:category,id',
            'unit_of_measurement' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable',
            'state' => 'nullable|boolean', // Permite validar el estado si viene del formulario
        ]);

        // Asignamos 'true' (activo) por defecto si no viene explícitamente en el request
        $validated['state'] = $request->has('state') ? $request->boolean('state') : true;

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,webp,gif|max:2048'
            ]);

            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = '/storage/' . $path;
        } elseif ($request->filled('image')) {
            if (filter_var($request->input('image'), FILTER_VALIDATE_URL)) {
                $validated['image'] = $request->input('image');
            } else {
                return back()->withErrors(['image' => 'El enlace proporcionado no es una dirección URL válida.'])->withInput();
            }
        } else {
            $validated['image'] = null;
        }

        Producto::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Producto creado con éxito.');
    }

    /**
     * Muestra el formulario para editar un producto.
     */
    public function editProducto($id)
    {
        $producto = Producto::findOrFail($id);
        $proveedores = Proveedor::orderBy('company_name', 'asc')->get();
        $categorias = Category::where('type', 'activo')->orderBy('name', 'asc')->get();

        return view('admin.productos.edit', compact('producto', 'proveedores', 'categorias'));
    }

    /**
     * Actualizar los datos del producto (Procesamiento Híbrido Local/URL).
     */
    public function updateProducto(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:proveedors,id', 
            'category_id' => 'required|exists:category,id',
            'unit_of_measurement' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'image_url' => 'nullable|url',
        ]);

        $data = $request->only(['name','barcode', 'supplier_id', 'category_id', 'unit_of_measurement', 'price', 'stock']);

        if ($request->hasFile('image_file')) {
            if ($producto->image && !\Illuminate\Support\Str::startsWith($producto->image, 'http')) {
                $oldPath = str_replace('/storage/', '', $producto->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image_file')->store('products', 'public');
            $data['image'] = '/storage/' . $path;

        } elseif ($request->filled('image_url')) {
            $urlCandidata = trim($request->image_url);

            if (filter_var($urlCandidata, FILTER_VALIDATE_URL)) {
                if ($producto->image && !\Illuminate\Support\Str::startsWith($producto->image, 'http')) {
                    $oldPath = str_replace('/storage/', '', $producto->image);
                    Storage::disk('public')->delete($oldPath);
                }

                $data['image'] = $urlCandidata;
            } else {
                return back()
                    ->withErrors(['image_url' => 'El enlace proporcionado no es una dirección URL válida.'])
                    ->withInput();
            }
        }

        $producto->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Producto actualizado con éxito.');
    }

    /**
     * Eliminar (desactivar) un producto.
     */
    public function destroyProducto($id)
    {
        $producto = Producto::findOrFail($id);

        // En lugar de delete(), cambiamos el estado a inactivo
        $producto->update([
            'state' => false
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Producto desactivado correctamente.');
    }

    /**
     * Reactivar un producto desactivado previamente.
     */
    public function reactivarProducto($id)
    {
        $producto = Producto::findOrFail($id);

        $producto->update([
            'state' => true
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'inactivos'])
            ->with('success', 'Producto reactivado correctamente.');
    }
}