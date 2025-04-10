<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\ProductoEvento;
use App\Models\PedidoDetalle;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Mostrar la lista de pedidos.
     * - Si es admin/superadmin: muestra todos.
     * - Si es externo: solo los suyos.
     */
    public function index(Request $request)
    {
        // Si el usuario es empleado (admin/superadmin), mostrar todos los pedidos
        $empleado = Auth::guard('empleado')->user();
        if ($empleado && in_array($empleado->rol, ['admin', 'superadmin','master'])) {
            $pedidos = Pedido::with('externo', 'evento')
                             ->when($request->estado, function ($query, $estado) {
                                 return $query->where('estado', $estado);
                             })
                             ->orderBy('id', 'desc')
                             ->get();
    
            return view('pedidos.index', compact('pedidos'));
        }
    
        // Si el usuario es externo, solo mostrar pedidos del evento al que está inscrito
        $externo = Auth::guard('externo')->user();
        if ($externo) {
            // Verificar si el usuario tiene un evento asignado en "externos.evento_id"
            if (!$externo->evento_id) {
                return redirect()->route('home')
                                 ->withErrors(['error' => 'No estás inscrito en ningún evento.']);
            }
    
            // Filtrar pedidos donde "evento_id" coincida con el evento al que está inscrito el usuario externo
            $pedidos = Pedido::with('evento')
                             ->where('evento_id', $externo->evento_id)
                             ->when($request->estado, function ($query, $estado) {
                                 return $query->where('estado', $estado);
                             })
                             ->orderBy('id', 'desc')
                             ->get();
    
            return view('pedidos.index', compact('pedidos'));
        }
    
        // Si no es empleado ni externo, redirigir al login
        return redirect()->route('login')->withErrors(['error' => 'No tienes permiso para ver los pedidos.']);
    }
    

    /**
     * Mostrar formulario para crear un pedido (catálogo del evento).
     */
    public function create($eventoId)
    {
        $externo = Auth::guard('externo')->user();
        if (!$externo) {
            return redirect()->route('login')
                             ->withErrors(['error' => 'Debes iniciar sesión como usuario externo.']);
        }
    
        // Buscar productos del evento con stock > 0
        $productos = ProductoEvento::where('evento_id', $eventoId)
            ->where('stock_disponible', '>', 0)
            ->get();
    
        // Si no hay productos disponibles, mostramos un error
        if ($productos->isEmpty()) {
            // Permanecer en la misma pantalla no tiene sentido si no existe
            // la vista actual, así que puedes redirigir al index con error:
            return redirect()->route('pedidos.index')
                             ->withErrors(['error' => 'No hay productos disponibles para este evento.']);
        }
    
        return view('pedidos.create', compact('productos', 'eventoId'));
    }
    

    /**
     * Guardar el pedido y sus detalles.
     */
    public function store(Request $request, $eventoId)
    {
        $externo = Auth::guard('externo')->user();
        if (!$externo) {
            return redirect()->route('login')
                             ->withErrors(['error' => 'Debes iniciar sesión como usuario externo.']);
        }
        
        // Array de [producto_evento_id => cantidad]
        $productosSeleccionados = $request->input('productos', []);
        
        // Verificar si seleccionó al menos un producto con cantidad > 0
        $haySeleccion = false;
        foreach ($productosSeleccionados as $productoId => $cant) {
            if ((int)$cant > 0) {
                $haySeleccion = true;
                break;
            }
        }
        if (!$haySeleccion) {
            return redirect()->back()
                             ->withErrors(['error' => 'No has seleccionado ningún producto.'])
                             ->withInput();
        }
    
        // Contador de cuántos pedidos fueron creados
        $pedidosCreados = 0;
        // Acumulador de errores
        $errores = [];
    
        // Recorrer cada producto seleccionado
        foreach ($productosSeleccionados as $productoId => $cant) {
            $cantidad = (int)$cant;
            if ($cantidad <= 0) {
                continue;
            }
    
            // Verificar que el producto pertenezca al evento
            $producto = ProductoEvento::where('id', $productoId)
                                      ->where('evento_id', $eventoId)
                                      ->first();
            if (!$producto) {
                $errores[] = "El producto con ID $productoId no pertenece a este evento.";
                continue;
            }
    
            // Verificar stock
            if ($producto->stock_disponible < $cantidad) {
                $errores[] = "No hay suficiente stock para el producto '{$producto->nombre}'. "
                           . "Stock disponible: {$producto->stock_disponible}, solicitado: $cantidad.";
                continue;
            }
    
            // Calcular subtotal
            $precioUnitario = $producto->precio;
            $subtotal = $precioUnitario * $cantidad;
    
            // Crear un registro en la tabla 'pedidos'
            $nuevoPedido = \App\Models\Pedido::create([
                'evento_id'  => $eventoId,
                'externo_id' => $externo->id,
                'nombre'     => $producto->nombre,   // Guardamos el nombre del producto
                'precio'     => $precioUnitario,     // Guardamos el precio unitario
                'cantidad'   => $cantidad,
                'total'      => $subtotal,
                'estado'     => 'pendiente',
            ]);
    
            if ($nuevoPedido) {
                $pedidosCreados++;
                // Descontar stock del producto
                $producto->stock_disponible -= $cantidad;
                $producto->save();
            }
        }
    
        // Si no se creó ningún pedido, mostramos errores
        if ($pedidosCreados === 0) {
            if (empty($errores)) {
                $errores[] = "No se pudo crear el pedido, revisa la selección de productos.";
            }
            return redirect()->back()->withErrors($errores)->withInput();
        }
    
        // Si se crearon pedidos con éxito, pero hubo errores parciales, los mostramos:
        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }
    
        // Todo correcto
        return redirect()->route('home')
            ->with('success', '¡Pedido(s) creado(s) correctamente!');
    }
    
    
    
    

    /**
     * Ver detalle de un pedido (para el externo o el admin/superadmin).
     */
    public function show($pedidoId)
    {
        // Cargamos detalles y la relación productoEvento
        $pedido = Pedido::with('detalles.productoEvento')->findOrFail($pedidoId);

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Cambiar el estado de un pedido (por admin o superadmin).
     */
    public function changeStatus(Request $request, $pedidoId)
    {
        // Verificamos que sea admin o superadmin
        $empleado = Auth::guard('empleado')->user();
        if (!$empleado || !in_array($empleado->rol, ['admin', 'superadmin','master'])) {
            abort(403, 'No tienes permisos para cambiar el estado del pedido.');
        }

        $nuevoEstado = $request->input('estado');
        $pedido = Pedido::findOrFail($pedidoId);

        // Validar que el estado sea uno de los permitidos
        if (!in_array($nuevoEstado, ['en_preparacion', 'enviado', 'entregado'])) {
            return redirect()->back()->withErrors(['error' => 'Estado inválido.']);
        }

        $pedido->estado = $nuevoEstado;
        $pedido->save();

        return redirect()->route('pedidos.show', $pedido->id)
                         ->with('success', "El estado del pedido ahora es: $nuevoEstado");
    }

    /**
     * Mostrar los pedidos de un evento específico (para admin/superadmin).
     */
    public function porEvento(Evento $evento)
    {
        // Cargar pedidos del evento con la relación 'externo' para obtener sus ubicaciones
        $pedidos = $evento->pedidos()->with('externo')->get();
    
        // Retornar la vista y pasarle el evento y los pedidos
        return view('pedidos.por_evento', compact('evento', 'pedidos'));
    }
    
    /**
     * Actualizar el estado de un pedido (AJAX).
     */
    public function updateStatus(Request $request, Pedido $pedido)
    {
        $request->validate(['estado' => 'required|in:pendiente,en_preparacion,enviado']);
        $pedido->update(['estado' => $request->estado]);
        return response()->json(['success' => true]);
    }

    /**
     * Subir evidencia de un pedido (AJAX).
     */
    public function updateEvidence(Request $request, Pedido $pedido)
    {
        \Log::info('Entrando a updateEvidence', [
            'pedido_id' => $pedido->id,
            'tiene_archivo' => $request->hasFile('evidence'),
        ]);
    
        $request->validate(['evidence' => 'required|image|max:2048']);
    
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('fotos_referencia', 'public');
            $pedido->update(['foto_evidencia' => $path]);
        }
    
        return response()->json(['success' => true]);
    }
}
