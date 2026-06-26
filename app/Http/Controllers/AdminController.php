<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\User;
use App\Models\Category; // Importa los modelos arriba
use App\Models\Producto; // Importamos el modelo de comida
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
    public function index()
    {
        $productos = Producto::all(); // El admin ve toda la comida, incluso sin stock
        return view('admin.dashboard', compact('productos'));
    }

    // =========================================================================
    // CRUD DE USUARIOS (ADMIN / CLIENTE)
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
     * Guardar nuevo usuario con Foto (Solo admin o cliente).
     */
    public function store(Request $request)
{
    // 1. Validación inicial flexible para los datos del usuario
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => ['required', \Illuminate\Validation\Rule::in(['admin', 'cliente'])],
        'photo' => 'nullable', // Pasará libre inicialmente para evaluarlo abajo
    ]);

    $photoPath = null;

    // 2. ✨ PROCESAMIENTO HÍBRIDO SEGURO DEL CAMPO 'photo'
    if ($request->hasFile('photo')) {
        
        // CASO A: Es un archivo local físico
        // Aquí sí limitamos el formato y peso en el servidor (2MB)
        $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = $request->file('photo')->store('photos', 'public');
        $photoPath = '/storage/' . $path;

    } elseif ($request->filled('photo')) {
        
        // CASO B: El usuario pegó texto en el campo
        $urlCandidata = trim($request->photo);

        // 🌟 FILTRO ESTRICTO: Valida si es una dirección URL web legítima
        // Esto descarta automáticamente los textos masivos en Base64 y previene el error 22001
        if (filter_var($urlCandidata, FILTER_VALIDATE_URL)) {
            $photoPath = $urlCandidata;
        } else {
            // Si es un Base64 o texto inválido, regresa con un mensaje de error limpio a la vista
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
            // CORREGIDO: Ahora solo valida 'admin' y 'cliente'
            'role' => ['required', Rule::in(['admin', 'cliente'])],
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
            $data['photo'] = $path;
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
            Storage::disk('public')->delete($user->photo);
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
        // Traemos solo los proveedores y categorías activos para listarlos en el formulario
        $proveedores = Proveedor::orderBy('company_name', 'asc')->get();
        $categorias = Category::where('type', 'activo')->orderBy('name', 'asc')->get();

        return view('admin.productos.create', compact('proveedores', 'categorias'));
    }

    /**
     * Guardar el nuevo producto en la base de datos.
     */
    public function storeProducto(Request $request)
    {
        // 1. Modificamos la validación inicial. 
        // ❌ Quitamos 'image' para que no rechace los strings de tipo URL.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:proveedors,id',
            'category_id' => 'required|exists:category,id',
            'unit_of_measurement' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable'
        ]);

        // 2. ✨ PROCESAMIENTO HÍBRIDO DEL CAMPO 'image'
        if ($request->hasFile('image')) {

            // CASO A: Es un archivo local físico de verdad.
            // Ejecutamos validación estricta de imagen en tiempo de ejecución.
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,webp,gif'
            ]);

            // Guarda en storage/app/public/products y asigna la ruta con el prefijo /storage/
            // Esto garantiza compatibilidad con la validación triple en cascada de tus vistas
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = '/storage/' . $path;
        } elseif ($request->filled('image')) {

            // CASO B: El usuario no subió archivo pero pegó un texto (URL).
            // Validamos que cumpla estrictamente con el formato de un enlace web.
            if (filter_var($request->input('image'), FILTER_VALIDATE_URL)) {
                $validated['image'] = $request->input('image');
            } else {
                // Si puso texto plano que no es una URL, regresamos marcando el error en la vista
                return back()->withErrors(['image' => 'El enlace proporcionado no es una dirección URL válida.'])->withInput();
            }
        } else {
            // CASO C: Si no se envió ninguna imagen, puedes dejarlo como null o una por defecto
            $validated['image'] = null;
        }

        // 3. Crear el registro usando asignación masiva ($fillable) con la data limpia
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
        // Buscamos el producto en la base de datos
        $producto = Producto::findOrFail($id);

        // 1. Modificamos la validación inicial.
        // Separamos 'image_file' e 'image_url' para que actúen de forma independiente y opcional.
        $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:proveedors,id', 
            'category_id' => 'required|exists:category,id',
            'unit_of_measurement' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'image_url' => 'nullable|url',
        ]);

        // Capturamos los datos básicos del formulario (excluyendo los inputs de imagen)
        $data = $request->only(['name', 'supplier_id', 'category_id', 'unit_of_measurement', 'price', 'stock']);

        // 2. ✨ PROCESAMIENTO HÍBRIDO SEGURO DEL RECURSO MULTIMEDIA
        if ($request->hasFile('image_file')) {
            
            // CASO A: El usuario subió un archivo físico local nuevo
            // Si existía una imagen local previa almacenada, la eliminamos del disco
            if ($producto->image && !\Illuminate\Support\Str::startsWith($producto->image, 'http')) {
                // Limpiamos la ruta removiendo el prefijo '/storage/' si lo tiene para que Storage::delete funcione correctamente
                $oldPath = str_replace('/storage/', '', $producto->image);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }

            // Guardamos el nuevo archivo en public/products y guardamos la ruta estructurada con /storage/
            $path = $request->file('image_file')->store('products', 'public');
            $data['image'] = '/storage/' . $path;

        } elseif ($request->filled('image_url')) {
            
            // CASO B: El usuario pegó o modificó un enlace URL web directo
            $urlCandidata = trim($request->image_url);

            // Validamos estrictamente que la estructura del texto corresponda a un enlace URL legítimo
            if (filter_var($urlCandidata, FILTER_VALIDATE_URL)) {
                
                // Opcional: Si antes había una imagen física local y ahora pasa a ser URL, borramos el archivo viejo del servidor
                if ($producto->image && !\Illuminate\Support\Str::startsWith($producto->image, 'http')) {
                    $oldPath = str_replace('/storage/', '', $producto->image);
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }

                $data['image'] = $urlCandidata;
            } else {
                // Si introdujo texto inválido, retornamos notificando el error
                return back()
                    ->withErrors(['image_url' => 'El enlace proporcionado no es una dirección URL válida.'])
                    ->withInput();
            }
        }

        // 3. Sincronizamos y actualizamos el modelo en la base de datos
        $producto->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Producto actualizado con éxito.');
    }

    /**
     * Eliminar un producto.
     */
    public function destroyProducto($id)
    {
        // Buscamos el producto
        $producto = Producto::findOrFail($id);

        // Si tiene una imagen física local asignada, la eliminamos del disco antes de borrar el registro
        if ($producto->image && !str_starts_with($producto->image, 'http')) {
            Storage::disk('public')->delete($producto->image);
        }

        // Borramos el producto de la base de datos
        $producto->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Producto eliminado correctamente.');
    }
}
