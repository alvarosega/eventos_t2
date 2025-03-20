<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Evento;
use App\Models\ProductoEvento;

class CatalogoController extends Controller
{
    // Mostrar lista de eventos para administrar catálogos
    public function index()
    {
        $usuario = Auth::user();
        if (!in_array($usuario->rol, ['superadmin', 'admin'])) {
            abort(403, 'Acceso no autorizado');
        }
        $eventos = Evento::all();
        return view('catalogos.index', compact('eventos', 'usuario'));
    }

    // Mostrar el catálogo de productos para un evento específico
    public function show($evento_id)
    {
        $usuario = Auth::user();
        $evento = Evento::findOrFail($evento_id);
        $productos = ProductoEvento::where('evento_id', $evento_id)->get();
        return view('catalogos.evento', compact('evento', 'productos', 'usuario'));
    }

    // Mostrar formulario para agregar un nuevo producto al catálogo de un evento (solo superadmin)
    public function create($evento_id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado');
        }
        $evento = Evento::findOrFail($evento_id);
        return view('catalogos.create', compact('evento'));
    }

    // Almacenar el nuevo producto en el catálogo del evento (solo superadmin)
    public function store(Request $request, $evento_id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'stock_disponible' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
            'descripcion' => 'nullable|string',
        ]);

        $validated['evento_id'] = $evento_id;

        if($request->hasFile('imagen')){
            $path = $request->file('imagen')->store('productos_evento', 'public');
            $validated['imagen'] = $path;
        }

        ProductoEvento::create($validated);
        return redirect()->route('catalogos.show', $evento_id)->with('success', 'Producto agregado al catálogo.');
    }

    // Mostrar formulario para editar un producto del catálogo (solo superadmin)
    public function edit($id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado');
        }
        $producto = ProductoEvento::findOrFail($id);
        return view('catalogos.edit', compact('producto'));
    }

    // Actualizar el producto del catálogo (solo superadmin)
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado');
        }
        $producto = ProductoEvento::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'stock_disponible' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
            'descripcion' => 'nullable|string',
        ]);

        if($request->hasFile('imagen')){
            $path = $request->file('imagen')->store('productos_evento', 'public');
            $validated['imagen'] = $path;
        }

        $producto->update($validated);
        return redirect()->route('catalogos.show', $producto->evento_id)->with('success', 'Producto actualizado correctamente.');
    }

    // Eliminar un producto del catálogo (solo superadmin)
    public function destroy($id)
    {
        $usuario = Auth::user();
        if ($usuario->rol !== 'superadmin') {
            abort(403, 'Acceso no autorizado');
        }
        $producto = ProductoEvento::findOrFail($id);
        $evento_id = $producto->evento_id;
        $producto->delete();
        return redirect()->route('catalogos.show', $evento_id)->with('success', 'Producto eliminado del catálogo.');
    }
}
