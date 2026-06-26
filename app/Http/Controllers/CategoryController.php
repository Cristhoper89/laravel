<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Muestra el listado de categorías.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $categories = $query->latest()->paginate(10);

        return view('admin.categorias.index', compact('categories'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * Guarda la nueva categoría.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:activo,inactivo', // Valida contra las opciones de tu enum
        ]);

        Category::create($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada con éxito.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id)
    {
        $categoria = Category::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    /**
     * Actualiza la categoría.
     */
    public function update(Request $request, $id)
    {
        $categoria = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:activo,inactivo',
        ]);

        $categoria->update($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada con éxito.');
    }

    /**
     * Elimina la categoría.
     */
    public function destroy($id)
    {
        $categoria = Category::findOrFail($id);
        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}