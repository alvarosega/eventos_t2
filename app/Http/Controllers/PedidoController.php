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
    public function index()
    {
        // Verificamos si el usuario es empleado (admin/superadmin)
        $empleado = Auth::guard('empleado')->user();
        if ($empleado && in_array($empleado->rol, ['admin', 'superadmin'])) {
            // Mostrar todos los pedidos
            $pedidos = Pedido::with('externo', 'evento')->orderBy('id', 'desc')->get();
            return view('pedidos.index', compact('pedidos'));
        }

        // Si no es empleado, probamos si es un usuario externo
        $externo = Auth::guard('externo')->user();
        if ($externo) {
            // Mostrar solo los pedidos de este usuario
            $pedidos = Pedido::with('evento')
                ->where('externo_id', $externo->id)
                ->orderBy('id', 'desc')
                ->get();
            return view('pedidos.index', compact('pedidos'));
        }

        // Si no se cumple nada, redirigimos
        return redirect()->route('login')->withErrors(['error' => 'No tienes permiso para ver los pedidos.']);
    }

    /**
     * Mostrar formulario para crear un pedido (catálogo del evento).
     */
    public function create($eventoId)
    {
        $externo = Auth::guard('externo')->user();
        if (!$externo) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión como usuario externo.']);
        }

        // Obtenemos los productos de ese evento
        $productos = ProductoEvento::where('evento_id', $eventoId)->get();

        return view('pedidos.create', compact('productos', 'eventoId'));
    }

    /**
     * Guardar el pedido y sus detalles.
     */
    public function store(Request $request, $eventoId)
    {
        $externo = Auth::guard('externo')->user();
        if (!$externo) {
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión.']);
        }

        // El formulario envía un array de [producto_evento_id => cantidad]
        $productosSeleccionados = $request->input('productos', []);

        // Creamos la cabecera del pedido
        $pedido = Pedido::create([
            'evento_id'  => $eventoId,
            'externo_id' => $externo->id,
            'cantidad'   => 0,          // Se calculará luego
            'total'      => 0,          // Se calculará luego
            'estado'     => 'pendiente' // Estado inicial
        ]);

        $totalItems  = 0;
        $totalPrecio = 0;

        foreach ($productosSeleccionados as $productoId => $cant) {
            $cantidad = (int)$cant;
            if ($cantidad <= 0) continue;

            // Usamos el modelo ProductoEvento
            $producto = ProductoEvento::find($productoId);
            if ($producto) {
                $precioUnitario = $producto->precio;
                $subtotal       = $precioUnitario * $cantidad;

                // Guardar detalle en la tabla pedido_detalles
                PedidoDetalle::create([
                    'pedido_id'          => $pedido->id,
                    'producto_evento_id' => $producto->id,
                    'cantidad'           => $cantidad,
                    'precio_unitario'    => $precioUnitario,
                ]);

                $totalItems  += $cantidad;
                $totalPrecio += $subtotal;
            }
        }

        // Actualizar totales en la cabecera del pedido
        $pedido->update([
            'cantidad' => $totalItems,
            'total'    => $totalPrecio,
        ]);

        return redirect()->route('pedidos.show', $pedido->id)
                         ->with('success', 'Pedido creado correctamente.');
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
        if (!$empleado || !in_array($empleado->rol, ['admin', 'superadmin'])) {
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
        $request->validate(['evidence' => 'required|image|max:2048']);

        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('evidencias', 'public');
            $pedido->update(['foto_evidencia' => $path]);
        }

        return response()->json(['success' => true]);
    }
}
